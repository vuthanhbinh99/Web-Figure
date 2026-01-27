<?php
session_start();
include("../config/database.php");
include("../model/Order.php");

$requestId = isset($_GET['requestId']) ? $_GET['requestId'] : '';
$orderId = isset($_GET['orderId']) ? $_GET['orderId'] : '';
$resultCode = isset($_GET['resultCode']) ? $_GET['resultCode'] : '-1';
$message = isset($_GET['message']) ? $_GET['message'] : 'Unknown error';

// Extract order ID from orderId (format: ORD_id_timestamp)
$parts = explode('_', $orderId);
$id_order = $parts[0];

try {
    // resultCode = 0 means successful payment
    if ($resultCode == 0) {
        // Update order status to paid
        $sql = "UPDATE orders SET status = :status WHERE id_order = :id_order";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':status', $status = 'confirmed', PDO::PARAM_STR);
        $stmt->bindParam(':id_order', $id_order, PDO::PARAM_STR);
        $stmt->execute();
        
        header("Location: ../views/success.php?id=$id_order");
        exit();
    } else {
        // Payment failed or cancelled
        $sql = "UPDATE orders SET status = :status WHERE id_order = :id_order";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':status', $status = 'cancelled', PDO::PARAM_STR);
        $stmt->bindParam(':id_order', $id_order, PDO::PARAM_STR);
        $stmt->execute();
        
        header("Location: ../views/checkout.php?error=" . urlencode($message));
        exit();
    }
} catch (PDOException $e) {
    error_log("Momo return error: " . $e->getMessage());
    header("Location: ../views/checkout.php?error=" . urlencode("Lỗi xử lý thanh toán"));
    exit();
}
exit;
?>