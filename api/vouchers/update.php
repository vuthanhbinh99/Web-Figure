<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
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

    $data = json_decode(file_get_contents('php://input'), true);
    
    $sql = "UPDATE vouchers SET code = :code, discount_value = :discount_value, discount_type = :discount_type, min_purchase = :min_purchase, max_uses = :max_uses, expire_date = :expire_date WHERE id_vouchers = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id_vouchers);
    $stmt->bindParam(':code', $data['code']);
    $stmt->bindParam(':discount_value', $data['discount_value']);
    $stmt->bindParam(':discount_type', $data['discount_type']);
    $stmt->bindParam(':min_purchase', $data['min_purchase']);
    $stmt->bindParam(':max_uses', $data['max_uses']);
    $stmt->bindParam(':expire_date', $data['expire_date']);
    
    $result = $stmt->execute();

    if ($result) {
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Voucher updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to update voucher']);
    }
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}
?>
