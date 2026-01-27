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

    $id_categories = $_GET['id_categories'] ?? null;
    if (!$id_categories) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Category ID required']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    $updates = [];
    $params = [':id' => $id_categories];
    
    if (isset($data['categories_name'])) {
        $updates[] = "categories_name = :name";
        $params[':name'] = $data['categories_name'];
    }
    if (isset($data['slug'])) {
        $updates[] = "slug = :slug";
        $params[':slug'] = $data['slug'];
    }
    if (isset($data['description'])) {
        $updates[] = "description = :desc";
        $params[':desc'] = $data['description'];
    }

    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'No fields to update']);
        exit;
    }

    $sql = "UPDATE categories SET " . implode(", ", $updates) . " WHERE id_categories = :id";
    
    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindParam($key, $params[$key]);
    }
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Category updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to update category']);
    }
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}
?>
