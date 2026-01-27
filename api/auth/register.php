<?php
session_start();

header('Content-Type: application/json; charset=utf-8');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header('Access-Control-Allow-Origin: ' . $origin);
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 3600');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../model/Account.php';
require_once __DIR__ . '/../../model/User.php';
require_once '../../helpers/auto_id.php';

session_start();

try {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input || !isset($input['username']) || !isset($input['password']) || !isset($input['email'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        exit;
    }

    $username = trim($input['username']);
    $password = trim($input['password']);
    $email = trim($input['email']);
    $full_name = trim($input['full_name'] ?? '');

    // Check if username already exists
    $accountModel = new Account($conn);
    $existing = $accountModel->getByUsername($username);

    if ($existing) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Tên đăng nhập đã tồn tại']);
        exit;
    }

    $id_accounts = generateId('ACC');
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // Register account
    $accountModel->register($id_accounts, $username, $hashed_password, 'customer');
    
    // Create user profile
    $id_users = generateId('USER');
    $userModel = new User($conn);
    $userModel->create($id_users, $full_name, $email, '', '', $id_accounts);
    
    $_SESSION['user'] = [
        'id_accounts' => $id_accounts,
        'id_users' => $id_users,
        'username' => $username,
        'full_name' => $full_name,
        'email' => $email,
        'role' => 'customer'
    ];
    
    http_response_code(201);
    echo json_encode([
        "status" => "success",
        "data" => $_SESSION['user']
    ]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Lỗi đăng ký: ' . $e->getMessage()]);
    exit;
}
?>
