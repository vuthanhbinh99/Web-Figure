<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../model/Order.php';

try {
    $id_order = isset($_GET['id_order']) ? trim($_GET['id_order']) : '';

    if (!$id_order) {
        errorResponse('Order ID is required', 400);
    }

    $orderModel = new Order($conn);
    $order = $orderModel->getById($id_order);
    
    if (!$order) {
        errorResponse('Order not found', 404);
    }
    
    $items = $orderModel->getItemsByOrderId($id_order);
    
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => [
        'order' => $order,
        'items' => $items
    ]
    ]);
    exit;
} catch (Exception $e) {
    errorResponse($e->getMessage());
}
?>
