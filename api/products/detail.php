<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../model/Product.php';

try {
    $id_products = isset($_GET['id_products']) ? trim($_GET['id_products']) : '';

    if (!$id_products) {
        errorResponse('Product ID is required', 400);
    }

    $productModel = new Product($conn);
    $product = $productModel->getById($id_products);

    if (!$product) {
        errorResponse('Product not found', 404);
    }

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => $product
    ]);
    exit;
} catch (Exception $e) {
    errorResponse('Error loading product: ' . $e->getMessage());
}
?>

            'message' => 'Product not found'
        ]);
    exit;
    }
    
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'data' => $product
    ]);
    exit;} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;}
?>
