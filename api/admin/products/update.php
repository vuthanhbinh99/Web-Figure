<?php
header('Content-Type: application/json; charset=utf-8');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header('Access-Control-Allow-Origin: ' . $origin);
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../../config/database.php';

session_start();

try {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Access denied']);
        exit;
    }

    $id_products = $_GET['id_products'] ?? null;
    if (!$id_products) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Product ID required']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    $updates = [];
    $params = [':id' => $id_products];
    
    if (isset($data['products_name'])) {
        $updates[] = "products_name = :name";
        $params[':name'] = $data['products_name'];
    }
    if (isset($data['id_categories'])) {
        $updates[] = "id_categories = :category";
        $params[':category'] = $data['id_categories'];
    }
    if (isset($data['description'])) {
        $updates[] = "description = :desc";
        $params[':desc'] = $data['description'];
    }
    if (isset($data['price'])) {
        $updates[] = "price = :price";
        $params[':price'] = $data['price'];
    }
    if (isset($data['stock_quantity'])) {
        $updates[] = "stock_quantity = :stock";
        $params[':stock'] = $data['stock_quantity'];
    }

    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'No fields to update']);
        exit;
    }

    $sql = "UPDATE products SET " . implode(", ", $updates) . " WHERE id_products = :id";
    
    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindParam($key, $params[$key]);
    }
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Product updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to update product']);
    }
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}
?>
