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
    
    if (!$data || !isset($data['products_name']) || !isset($data['price']) || !isset($data['id_categories'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    $sql = "INSERT INTO products (products_name, id_categories, description, price, stock_quantity, image, status, created_at) 
            VALUES (:name, :category, :desc, :price, :stock, :image, :status, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $data['products_name']);
    $stmt->bindParam(':category', $data['id_categories']);
    $stmt->bindParam(':desc', $data['description'] ?? '');
    $stmt->bindParam(':price', $data['price']);
    $stmt->bindParam(':stock', $data['stock_quantity'] ?? 0);
    $stmt->bindParam(':image', $data['image'] ?? null);
    $stmt->bindParam(':status', $data['status'] ?? 'active');
    
    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(['status' => 'success', 'message' => 'Product created successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to create product']);
    }
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}
?>
