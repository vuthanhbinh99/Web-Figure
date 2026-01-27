<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/database.php';

try {

    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['categories_name']) || !isset($data['slug'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    $sql = "INSERT INTO categories (categories_name, slug, description, created_at) 
            VALUES (:name, :slug, :desc, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $data['categories_name']);
    $stmt->bindParam(':slug', $data['slug']);
    $stmt->bindParam(':desc', $data['description'] ?? '');
    
    $result = $stmt->execute();

    if ($result) {
        http_response_code(201);
        echo json_encode(['status' => 'success', 'message' => 'Category created successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to create category']);
    }
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}
?>
