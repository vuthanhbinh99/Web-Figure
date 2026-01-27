<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../model/Order.php';
require_once __DIR__ . '/../../model/Cart.php';
require_once '../../helpers/auto_id.php';

session_start();

try {
    if (!isset($_SESSION['user'])) {
        errorResponse('Not authenticated', 401);
    }

    $input = json_decode(file_get_contents("php://input"), true);

    $payment_method = $input['payment_method'] ?? 'cod';
    $full_name = $input['full_name'] ?? '';
    $phone = $input['phone'] ?? '';
    $address = $input['address'] ?? '';

    if (!$full_name || !$phone || !$address) {
        errorResponse('Missing required fields', 400);
    }

    $orderModel = new Order($conn);
    $cartModel = new Cart($conn);
    
    // Get cart items
    $cart = $cartModel->getByUserId($_SESSION['user']['id_users']);
    
    if (empty($cart)) {
        errorResponse('Cart is empty', 400);
    }
    
    // Create order
    $id_order = generateId('ORDER');
    $result = $orderModel->createOrder(
        $_SESSION['user']['id_accounts'],
        $cart,
        $payment_method,
        $full_name,
        $phone,
        $address
    );
    
    if ($result) {
        http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => ['order_id' => $id_order, 'message' => 'Order created successfully'], 201
    ]);
    exit;
    } else {
        errorResponse('Failed to create order', 500);
    }
} catch (Exception $e) {
    errorResponse($e->getMessage());
}
?>
