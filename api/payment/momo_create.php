<?php
// Momo Payment Gateway Integration
session_start();

header("Content-Type: application/json");

if (!isset($_SESSION['user'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Vui lòng đăng nhập để thanh toán!"
    ]);
    exit;
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    echo json_encode([
        "status" => "error",
        "message" => "Dữ liệu không hợp lệ"
    ]);
    exit;
}

// Momo API Configuration
// SANDBOX ENVIRONMENT
$partnerCode = "MOMOKMD220250203_TEST"; 
$accessKey = "uImGcVDNy47wg86x"; 
$secretKey = "Bj8atm6KhJ9x4HNii4LSH8UbpP17teyc"; 
$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

// Your Return URL
$returnUrl = "http://" . $_SERVER['HTTP_HOST'] . "/QuanLyBanHangFigure/payment/momo_return.php";
$notifyUrl = "http://" . $_SERVER['HTTP_HOST'] . "/QuanLyBanHangFigure/payment/momo_notify.php";

$id_order = $data['id_order'];
$amount = (int)$data['amount'];
$orderId = $id_order . "_" . time();

$requestId = $orderId;
$requestType = "captureWallet";
$ipnUrl = $notifyUrl;

// Build the request data
$requestBody = [
    'partnerCode' => $partnerCode,
    'requestId' => $requestId,
    'amount' => $amount,
    'orderId' => $orderId,
    'orderInfo' => "Thanh toán đơn hàng $id_order",
    'returnUrl' => $returnUrl,
    'notifyUrl' => $notifyUrl,
    'redirectUrl' => $returnUrl,
    'requestType' => $requestType,
    'signature' => ''
];

// Generate signature
$rawSignature = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=&ipnUrl=" . $notifyUrl . "&orderId=" . $orderId . "&orderInfo=" . urlencode($requestBody['orderInfo']) . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $returnUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;

$signature = hash_hmac("sha256", $rawSignature, $secretKey);
$requestBody['signature'] = $signature;

// Send request to Momo
$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
    exit;curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$responseBody = json_decode($response, true);

if ($httpcode == 200 && isset($responseBody['payUrl'])) {
    echo json_encode([
        "status" => "momo",
        "payment_url" => $responseBody['payUrl'],
        "request_id" => $requestId,
        "order_id" => $orderId
    ]);
    exit;} else {
    error_log("Momo API Error: " . $response);
    echo json_encode([
        "status" => "error",
        "message" => "Không thể kết nối tới Momo. Vui lòng thử lại!"
    ]);
    exit;}
?>
