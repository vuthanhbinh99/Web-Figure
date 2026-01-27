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

    $cartModel = new Cart($conn);
    
    // Get all cart items and delete them
    $items = $cartModel->getByUserId($_SESSION['user']['id_users']);
    
    foreach ($items as $item) {
        $cartModel->delete($item['id_carts']);
    }
    
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => ['message' => 'Cart cleared']
    ]);
    exit;
} catch (Exception $e) {
    errorResponse($e->getMessage());
}
?>
