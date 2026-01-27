<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../model/Review.php';
require_once '../../helpers/auto_id.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $id_products = isset($_GET['id_products']) ? trim($_GET['id_products']) : '';
        
        if (!$id_products) {
            errorResponse('Product ID is required', 400);
        }
        
        $reviewModel = new Review($conn);
        $reviews = $reviewModel->getByProductId($id_products);
        
        http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => $reviews
    ]);
    exit;
    } catch (Exception $e) {
        errorResponse($e->getMessage());
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    
    try {
        if (!isset($_SESSION['user'])) {
            errorResponse('Not authenticated', 401);
        }
        
        $input = json_decode(file_get_contents("php://input"), true);
        
        $id_products = $input['id_products'] ?? '';
        $rating = $input['rating'] ?? 5;
        $comment = $input['comment'] ?? '';
        
        if (!$id_products) {
            errorResponse('Product ID is required', 400);
        }
        
        $reviewModel = new Review($conn);
        $id_reviews = generateId('REV');
        
        $result = $reviewModel->create(
            $id_reviews,
            $id_products,
            $_SESSION['user']['id_users'],
            $rating,
            $comment
        );
        
        if ($result) {
            http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => ['message' => 'Review created successfully'], 201
    ]);
    exit;
        } else {
            errorResponse('Failed to create review', 500);
        }
    } catch (Exception $e) {
        errorResponse($e->getMessage());
    }
}
?>
