<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/database.php';

try {

    $id_categories = $_GET['id_categories'] ?? null;
    if (!$id_categories) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Category ID required']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    $sql = "UPDATE categories SET categories_name = :categories_name, slug = :slug, description = :description WHERE id_categories = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id_categories);
    $stmt->bindParam(':categories_name', $data['categories_name']);
    $stmt->bindParam(':slug', $data['slug']);
    $stmt->bindParam(':description', $data['description']);
    
    $result = $stmt->execute();

    if ($result) {
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
