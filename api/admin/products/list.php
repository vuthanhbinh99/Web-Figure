<?php
header('Content-Type: application/json; charset=utf-8');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header('Access-Control-Allow-Origin: ' . $origin);
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../../config/database.php';

session_start();

try {
    // Check admin access
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Access denied']);
        exit;
    }

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = ($page - 1) * $limit;

    if ($page < 1) $page = 1;
    
    // Get total count
    $countStmt = $conn->query("SELECT COUNT(*) as total FROM products");
    $countResult = $countStmt->fetch();
    $total = $countResult['total'] ?? 0;
    
    // Get products with category join
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.id_categories = c.id_categories
            ORDER BY p.id_products DESC 
            LIMIT " . intval($offset) . ", " . intval($limit);
    
    $stmt = $conn->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => $products,
        "total" => (int)$total,
        "page" => $page,
        "limit" => $limit,
        "pages" => ceil($total / $limit)
    ]);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database Error']);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    error_log("Server Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server Error']);
    exit;
}
?>
