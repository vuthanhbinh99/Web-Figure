<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../model/Product.php';

try {
    $productModel = new Product($conn);
    $products = $productModel->getFeatured();
    
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => $products
    ]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Error loading featured products: " . $e->getMessage()
    ]);
    exit;
}
?>
