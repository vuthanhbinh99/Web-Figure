<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../model/Category.php';

try {
    $categoryModel = new Category($conn);
    $categories = $categoryModel->getAll();
    
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'data' => $categories
    ]);
    exit;} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;}
?>
