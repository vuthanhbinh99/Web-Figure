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

    $id_products = $_GET['id_products'] ?? null;
    if (!$id_products) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Product ID required']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    $sql = "UPDATE products SET products_name = :products_name, id_categories = :id_categories, description = :description, price = :price, stock_quantity = :stock_quantity, image = :image, id_featured = :id_featured, status = :status WHERE id_products = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id_products);
    $stmt->bindParam(':products_name', $data['products_name']);
    $stmt->bindParam(':id_categories', $data['id_categories']);
    $stmt->bindParam(':description', $data['description']);
    $stmt->bindParam(':price', $data['price']);
    $stmt->bindParam(':stock_quantity', $data['stock_quantity']);
    $stmt->bindParam(':image', $data['image']);
    $stmt->bindParam(':id_featured', $data['id_featured']);
    $stmt->bindParam(':status', $data['status']);
    
    $result = $stmt->execute();

    if ($result) {
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Product updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to update product']);
    }
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}
?>
