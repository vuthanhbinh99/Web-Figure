<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/database.php';

try {

    $id_vouchers = $_GET['id_vouchers'] ?? null;
    if (!$id_vouchers) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Voucher ID required']);
        exit;
    }

    $sql = "DELETE FROM vouchers WHERE id_vouchers = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id_vouchers);
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Voucher deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete voucher']);
    }
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}
?>
