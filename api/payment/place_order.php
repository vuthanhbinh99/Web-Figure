<?php
session_start();
header("Content-Type: application/json");

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Vui lòng đăng nhập để đặt hàng!"
    ]);
    exit;
}

include "../config/database.php";
include "../model/Order.php";
include "../helpers/auto_id.php";

$fullname = trim($_POST['fullname'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$payment_method = $_POST['payment_method'] ?? 'cod';
$voucher_code = $_POST['voucher_code'] ?? null;
$cart_json = $_POST['cart'] ?? '';

if (empty($fullname) || empty($phone) || empty($address)) {
    echo json_encode([
        "status" => "error",
        "message" => "Vui lòng nhập đầy đủ thông tin"
    ]);
    exit;
}

if (empty($cart_json)) {
    echo json_encode([
        "status" => "error",
        "message" => "Giỏ hàng trống"
    ]);
    exit;
}

try {
    $cart = json_decode($cart_json, true);
    if (!is_array($cart) || empty($cart)) {
        throw new Exception("Giỏ hàng không hợp lệ");
    }

    // Debug: kiểm tra cấu trúc cart
    error_log("Cart structure: " . json_encode($cart[0]));

    // Calculate total and get voucher discount if applicable
    $total = 0;
    foreach ($cart as $item) {
        $total += (int)$item['price'] * (int)$item['quantity'];
    }

    $discount = 0;
    $final_total = $total;
    
    if ($voucher_code) {
        $stmt = $conn->prepare("SELECT * FROM vouchers WHERE code = :code AND active = 1 AND expires_at > NOW()");
        $stmt->bindParam(':code', $voucher_code, PDO::PARAM_STR);
        $stmt->execute();
        $voucher = $stmt->fetch();
        
        if ($voucher) {
            $discount = ($total * $voucher['discount_percent']) / 100;
            $final_total = $total - $discount;
        }
    }

    $id_order = generateId("ORD");
    $id_accounts = $_SESSION['user_id'];

    // Handle payment method - kiểm tra Momo trước khi lưu order
    if ($payment_method === 'momo') {
        // Load Momo credentials from environment variables
        $partnerCode = getenv('MOMO_PARTNER_CODE');
        $accessKey = getenv('MOMO_ACCESS_KEY');
        $secretKey = getenv('MOMO_SECRET_KEY');
        $endpoint = getenv('MOMO_ENDPOINT');

        $amount = (int)$final_total;
        $orderId = $id_order . "_" . time();
        $requestId = $orderId;
        $returnUrl = "http://" . $_SERVER['HTTP_HOST'] . "/QuanLyBanHangFigure/payment/momo_return.php";
        $notifyUrl = "http://" . $_SERVER['HTTP_HOST'] . "/QuanLyBanHangFigure/payment/momo_notify.php";

        $orderInfo = "Thanh toan don hang $id_order";
        
        // Tạo request body với ĐÚNG các tham số Momo yêu cầu
        $requestBody = [
            'partnerCode' => $partnerCode,
            'partnerTransId' => $orderId,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'returnUrl' => $returnUrl,
            'notifyUrl' => $notifyUrl,
            'ipnUrl' => $notifyUrl,
            'extraData' => '',
            'requestType' => "captureWallet",
            'signature' => ""
        ];

        // Generate signature - format chính xác theo Momo docs
        // Phải theo thứ tự: accessKey, amount, extraData, ipnUrl, orderId, orderInfo, partnerCode, redirectUrl, requestId, requestType
        $rawSignature = "accessKey=" . $accessKey 
            . "&amount=" . $amount 
            . "&extraData=" 
            . "&ipnUrl=" . $notifyUrl
            . "&orderId=" . $orderId 
            . "&orderInfo=" . $orderInfo 
            . "&partnerCode=" . $partnerCode 
            . "&redirectUrl=" . $returnUrl
            . "&requestId=" . $requestId 
            . "&requestType=captureWallet";
        
        $signature = hash_hmac("sha256", $rawSignature, $secretKey);
        $requestBody['signature'] = $signature;

        // Send request to Momo
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($requestBody),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($err) {
            error_log("Momo API Error: " . $err);
            error_log("Request Body: " . json_encode($requestBody, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            error_log("Raw Signature: " . $rawSignature);
            echo json_encode([
                "status" => "error",
                "message" => "Lỗi kết nối Momo: " . $err,
                "debug" => ["error" => $err, "request" => $requestBody, "signature" => $rawSignature]
            ]);
    exit;
        }

        error_log("Momo HTTP Code: " . $httpCode);
        error_log("Momo Response: " . $response);
        error_log("Request Body: " . json_encode($requestBody, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        error_log("Raw Signature: " . $rawSignature);
        $momoResponse = json_decode($response, true);
        
        // Chỉ lưu order nếu Momo thành công
        if (!isset($momoResponse['payUrl'])) {
            error_log("Momo Error: " . json_encode($momoResponse));
            echo json_encode([
                "status" => "error",
                "message" => "Không thể tạo liên kết thanh toán Momo. " . ($momoResponse['message'] ?? 'Unknown error'),
                "debug" => [
                    "momo_response" => $momoResponse,
                    "request_body" => $requestBody,
                    "raw_signature" => $rawSignature,
                    "http_code" => $httpCode
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Momo thành công - lưu order
        $stmt = $conn->prepare("
            INSERT INTO orders (id_order, id_accounts, total, address, phone, full_name, payment_method, status, voucher_code, created_at)
            VALUES (:id_order, :id_accounts, :total, :address, :phone, :full_name, :payment_method, :status, :voucher_code, NOW())
        ");
        
        $status = 'processing';
        
        $stmt->bindParam(':id_order', $id_order, PDO::PARAM_STR);
        $stmt->bindParam(':id_accounts', $id_accounts, PDO::PARAM_STR);
        $stmt->bindParam(':total', $final_total, PDO::PARAM_INT);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':full_name', $fullname, PDO::PARAM_STR);
        $stmt->bindParam(':payment_method', $payment_method, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':voucher_code', $voucher_code, PDO::PARAM_STR);
        $stmt->execute();

        // Add order items
        foreach ($cart as $item) {
            $id_order_items = generateId("OIT");
            $stmt = $conn->prepare("
                INSERT INTO order_items (id_order_items, id_order, id_products, quantity, price)
                VALUES (:id_order_items, :id_order, :id_products, :quantity, :price)
            ");
            $stmt->bindParam(':id_order_items', $id_order_items, PDO::PARAM_STR);
            $stmt->bindParam(':id_order', $id_order, PDO::PARAM_STR);
            $stmt->bindParam(':id_products', $item['id_products'], PDO::PARAM_STR);
            $stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
            $stmt->bindParam(':price', $item['price'], PDO::PARAM_INT);
            $stmt->execute();
        }

        // Gửi email xác nhận đơn hàng
        include "../helpers/email_helper.php";
        $userEmail = $_POST['email'] ?? '';
        if ($userEmail) {
            sendOrderConfirmationEmail($userEmail, $fullname, $id_order, $total, $final_total, $discount, $cart, $payment_method);
        }

        // Trả về payment URL
        echo json_encode([
            "status" => "momo",
            "payment_url" => $momoResponse['payUrl'],
            "id_order" => $id_order
        ]);
    exit;
    }

    // COD - lưu order ngay
    $stmt = $conn->prepare("
        INSERT INTO orders (id_order, id_accounts, total, address, phone, full_name, payment_method, status, voucher_code, created_at)
        VALUES (:id_order, :id_accounts, :total, :address, :phone, :full_name, :payment_method, :status, :voucher_code, NOW())
    ");
    
    $status = 'pending';
    
    $stmt->bindParam(':id_order', $id_order, PDO::PARAM_STR);
    $stmt->bindParam(':id_accounts', $id_accounts, PDO::PARAM_STR);
    $stmt->bindParam(':total', $final_total, PDO::PARAM_INT);
    $stmt->bindParam(':address', $address, PDO::PARAM_STR);
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    $stmt->bindParam(':full_name', $fullname, PDO::PARAM_STR);
    $stmt->bindParam(':payment_method', $payment_method, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':voucher_code', $voucher_code, PDO::PARAM_STR);
    $stmt->execute();

    // Add order items
    foreach ($cart as $item) {
        $id_order_items = generateId("OIT");
        $stmt = $conn->prepare("
            INSERT INTO order_items (id_order_items, id_order, id_products, quantity, price)
            VALUES (:id_order_items, :id_order, :id_products, :quantity, :price)
        ");
        $stmt->bindParam(':id_order_items', $id_order_items, PDO::PARAM_STR);
        $stmt->bindParam(':id_order', $id_order, PDO::PARAM_STR);
        $stmt->bindParam(':id_products', $item['id_products'], PDO::PARAM_STR);
        $stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':price', $item['price'], PDO::PARAM_INT);
        $stmt->execute();
    }

    // Gửi email xác nhận đơn hàng
    include "../helpers/email_helper.php";
    $userEmail = $_POST['email'] ?? '';
    if ($userEmail) {
        sendOrderConfirmationEmail($userEmail, $fullname, $id_order, $total, $final_total, $discount, $cart, $payment_method);
    }

    // Trả về success
    echo json_encode([
        "status" => "success",
        "message" => "Đặt hàng thành công!",
        "id_order" => $id_order
    ]);
    exit;
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Lỗi: " . $e->getMessage()
    ]);
    exit;
}