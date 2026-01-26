<?php
// This file receives IPN (Instant Payment Notification) from Momo
session_start();
include("../config/database.php");
include("../model/Voucher.php");

$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Momo API Configuration
$secretKey = "LdTvrCh5NhLWXhx91hOOleglObnUe09l";

if (isset($data['signature'])) {
    $signature = $data['signature'];
    
    $rawSignature = "accessKey=" . $data['accessKey'] . "&amount=" . $data['amount'] . 
                   "&extraData=" . $data['extraData'] . "&ipnUrl=" . $data['ipnUrl'] . 
                   "&message=" . $data['message'] . "&orderId=" . $data['orderId'] . 
                   "&orderInfo=" . $data['orderInfo'] . "&orderType=" . $data['orderType'] . 
                   "&partnerCode=" . $data['partnerCode'] . "&payType=" . $data['payType'] . 
                   "&requestId=" . $data['requestId'] . "&responseTime=" . $data['responseTime'] . 
                   "&resultCode=" . $data['resultCode'] . "&transId=" . $data['transId'];
    
    $computedSignature = hash_hmac("sha256", $rawSignature, $secretKey);
    
    if ($signature === $computedSignature) {
        // Signature is valid
        if ($data['resultCode'] == 0) {
            // Payment successful
            $orderId = $data['orderId'];
            $parts = explode('_', $orderId);
            $id_order = $parts[0];
            
            try {
                $sql = "UPDATE orders SET status = :status WHERE id_order = :id_order";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':status', $payment_status = 'confirmed', PDO::PARAM_STR);
                $stmt->bindParam(':id_order', $id_order, PDO::PARAM_STR);
                $stmt->execute();

                // Increment voucher usage if voucher was used
                $stmt = $conn->prepare("SELECT voucher_code FROM orders WHERE id_order = :id_order");
                $stmt->bindParam(':id_order', $id_order, PDO::PARAM_STR);
                $stmt->execute();
                $orderData = $stmt->fetch();

                if ($orderData && $orderData['voucher_code']) {
                    $voucherModel = new Voucher($conn);
                    $voucherModel->incrementUsageCount($orderData['voucher_code']);
                }
                
                error_log("Momo payment success for order: $id_order");
                echo json_encode([
                    "resultCode" => 0,
                    "message" => "Success"
                ]);
            } catch (PDOException $e) {
                error_log("Momo IPN error: " . $e->getMessage());
                echo json_encode([
                    "resultCode" => 1,
                    "message" => "Database error"
                ]);
            }
        } else {
            // Payment failed
            error_log("Momo payment failed. Result Code: " . $data['resultCode']);
            echo json_encode([
                "resultCode" => 0,
                "message" => "Success"
            ]);
        }
    } else {
        error_log("Invalid Momo signature");
        echo json_encode([
            "resultCode" => 1,
            "message" => "Invalid signature"
        ]);
    }
} else {
    error_log("No signature in Momo request");
    echo json_encode([
        "resultCode" => 1,
        "message" => "No signature"
    ]);
}
?>

