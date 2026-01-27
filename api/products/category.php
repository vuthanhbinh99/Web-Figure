<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../model/Product.php';

try {
    $category_slug = isset($_GET['category_slug']) ? trim($_GET['category_slug']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = ($page - 1) * $limit;

    if (!$category_slug) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Category slug is required"
        ]);
        exit;
    }

    $productModel = new Product($conn);
    $total = $productModel->countByCategory($category_slug);
    $products = $productModel->getByCategoryPaginated($category_slug, $limit, $offset);

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => $products,
        "total" => $total,
        "page" => $page,
        "limit" => $limit,
        "pages" => ceil($total / $limit)
    ]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Error loading category products: " . $e->getMessage()
    ]);
    exit;
}
?>
