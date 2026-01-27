<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/database.php';
require_once '../../helpers/auto_id.php';

session_start();

try {
    // Check authentication
    if (!isset($_SESSION['id_users'])) {
        errorResponse('Not authenticated', 401);
    }

    $id_users = $_SESSION['id_users'];

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get all cart items for user with product details
        $stmt = $conn->prepare("
            SELECT c.id_carts, c.id_products, c.quantity, c.created_at,
                   p.name, p.price, p.image, p.stock
            FROM carts c
            JOIN products p ON c.id_products = p.id_products
            WHERE c.id_users = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$id_users]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => $items
    ]);
    exit;

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Add item to cart
        $input = json_decode(file_get_contents('php://input'), true);

        $id_products = $input['id_products'] ?? '';
        $quantity = intval($input['quantity'] ?? 1);

        if (!$id_products || $quantity < 1) {
            errorResponse('Invalid product ID or quantity', 400);
        }

        // Check if product exists and has stock
        $stmt = $conn->prepare("SELECT stock FROM products WHERE id_products = ?");
        $stmt->execute([$id_products]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            errorResponse('Product not found', 404);
        }

        if ($product['stock'] < $quantity) {
            errorResponse('Insufficient stock', 400);
        }

        // Check if item already in cart
        $stmt = $conn->prepare("SELECT id_carts, quantity FROM carts WHERE id_users = ? AND id_products = ?");
        $stmt->execute([$id_users, $id_products]);
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem['quantity'] + $quantity;
            $stmt = $conn->prepare("UPDATE carts SET quantity = ? WHERE id_carts = ?");
            $stmt->execute([$newQuantity, $existingItem['id_carts']]);
        } else {
            // Create new cart item
            $id_carts = generateId('CART');
            $stmt = $conn->prepare("
                INSERT INTO carts (id_carts, id_users, id_products, quantity, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$id_carts, $id_users, $id_products, $quantity]);
        }

        http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => ['message' => 'Item added to cart'], 201
    ]);
    exit;

    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // Clear entire cart
        $stmt = $conn->prepare("DELETE FROM carts WHERE id_users = ?");
        $stmt->execute([$id_users]);

        http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => ['message' => 'Cart cleared']
    ]);
    exit;
    } else {
        errorResponse('Method not allowed', 405);
    }

} catch (Exception $e) {
    errorResponse('Server error: ' . $e->getMessage());
}
?>
