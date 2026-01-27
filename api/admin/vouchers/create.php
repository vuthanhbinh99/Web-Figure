<?php
header('Content-Type: application/json; charset=utf-8');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header('Access-Control-Allow-Origin: ' . $origin);
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../../config/database.php';

session_start();

try {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Access denied']);
        exit;
    }

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
    
    if ($stmt->execute()) {
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
