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
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$id_carts) {
        errorResponse('Cart ID is required', 400);
    }

    $quantity = $input['quantity'] ?? 1;

    $cartModel = new Cart($conn);
    $result = $cartModel->updateQuantity($id_carts, $quantity);
    
    if ($result) {
        http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => ['message' => 'Quantity updated']
    ]);
    exit;
    } else {
        errorResponse('Failed to update quantity', 500);
    }
} catch (Exception $e) {
    errorResponse($e->getMessage());
}
?>
