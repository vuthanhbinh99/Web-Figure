<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../model/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $id_users = isset($_GET['id_users']) ? trim($_GET['id_users']) : '';
        
        if (!$id_users) {
            errorResponse('User ID is required', 400);
        }
        
        $userModel = new User($conn);
        $user = $userModel->getById($id_users);
        
        if (!$user) {
            errorResponse('User not found', 404);
        }
        
        http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => $user
    ]);
    exit;
    } catch (Exception $e) {
        errorResponse($e->getMessage());
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    try {
        $id_users = isset($_GET['id_users']) ? trim($_GET['id_users']) : '';
        $input = json_decode(file_get_contents("php://input"), true);
        
        if (!$id_users) {
            errorResponse('User ID is required', 400);
        }
        
        $full_name = $input['full_name'] ?? '';
        $email = $input['email'] ?? '';
        $phone = $input['phone'] ?? '';
        $address = $input['address'] ?? '';
        
        $userModel = new User($conn);
        $result = $userModel->update($id_users, $full_name, $email, $phone, $address);
        
        if ($result) {
            http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => ['message' => 'User updated successfully']
    ]);
    exit;
        } else {
            errorResponse('Failed to update user', 500);
        }
    } catch (Exception $e) {
        errorResponse($e->getMessage());
    }
}
?>
