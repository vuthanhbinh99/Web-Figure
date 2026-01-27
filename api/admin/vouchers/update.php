<?php
header('Content-Type: application/json; charset=utf-8');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header('Access-Control-Allow-Origin: ' . $origin);
header('Access-Control-Allow-Methods: PUT, OPTIONS');
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

    $id_vouchers = $_GET['id_vouchers'] ?? null;
    if (!$id_vouchers) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Voucher ID required']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    $updates = [];
    $params = [':id' => $id_vouchers];
    
    if (isset($data['code'])) {
        $updates[] = "code = :code";
        $params[':code'] = $data['code'];
    }
    if (isset($data['discount_value'])) {
        $updates[] = "discount_value = :discount";
        $params[':discount'] = $data['discount_value'];
    }
    if (isset($data['discount_type'])) {
        $updates[] = "discount_type = :type";
        $params[':type'] = $data['discount_type'];
    }
    if (isset($data['min_purchase'])) {
        $updates[] = "min_purchase = :min";
        $params[':min'] = $data['min_purchase'];
    }
    if (isset($data['max_uses'])) {
        $updates[] = "max_uses = :max";
        $params[':max'] = $data['max_uses'];
    }
    if (isset($data['expire_date'])) {
        $updates[] = "expire_date = :expire";
        $params[':expire'] = $data['expire_date'];
    }

    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'No fields to update']);
        exit;
    }

    $sql = "UPDATE vouchers SET " . implode(", ", $updates) . " WHERE id_vouchers = :id";
    
    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindParam($key, $params[$key]);
    }
    
    if ($stmt->execute()) {
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
