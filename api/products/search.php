<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../model/Product.php';

try {
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = ($page - 1) * $limit;

    if (!$q) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Search query is required"
        ]);
        exit;
    }

    $productModel = new Product($conn);
    $products = $productModel->searchByNamePaginated($q, $limit, $offset);
    $total = $productModel->searchByName($q);

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => $products,
        "page" => $page,
        "limit" => $limit,
        "total" => (int)$total,
        "pages" => ceil((int)$total / $limit)
    ]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Error searching products: " . $e->getMessage()
    ]);
    exit;
}
?>
