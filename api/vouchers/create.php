<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/database.php';

try {

    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['code']) || !isset($data['discount_value'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    $sql = "INSERT INTO vouchers (code, discount_value, discount_type, min_purchase, max_uses, expire_date, created_at) 
            VALUES (:code, :discount, :type, :min, :max, :expire, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':code', $data['code']);
    $stmt->bindParam(':discount', $data['discount_value']);
    $stmt->bindParam(':type', $data['discount_type'] ?? 'percentage');
    $stmt->bindParam(':min', $data['min_purchase'] ?? 0);
    $stmt->bindParam(':max', $data['max_uses']);
    $stmt->bindParam(':expire', $data['expire_date']);
    
    $result = $stmt->execute();

    if ($result) {
        http_response_code(201);
        echo json_encode(['status' => 'success', 'message' => 'Voucher created successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to create voucher']);
    }
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}
?>
