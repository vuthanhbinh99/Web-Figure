<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../model/Cart.php';

session_start();

try {
    if (!isset($_SESSION['user'])) {
        errorResponse('Not authenticated', 401);
    }

    $id_carts = isset($_GET['id_carts']) ? trim($_GET['id_carts']) : '';

    if (!$id_carts) {
        errorResponse('Cart ID is required', 400);
    }

    $cartModel = new Cart($conn);
    $result = $cartModel->delete($id_carts);
    
    if ($result) {
        http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => ['message' => 'Item removed from cart']
    ]);
    exit;
    } else {
        errorResponse('Failed to remove item', 500);
    }
} catch (Exception $e) {
    errorResponse($e->getMessage());
}
?>
