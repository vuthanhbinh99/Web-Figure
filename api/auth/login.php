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

try {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input || !isset($input['username']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        exit;
    }

    $username = trim($input['username']);
    $password = trim($input['password']);

    $accountModel = new Account($conn);
    $account = $accountModel->getAccountWithUser($username);

    if (!$account) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Tài khoản không tồn tại.']);
        exit;
    }

    if (!password_verify($password, $account['password'])) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Sai mật khẩu.']);
        exit;
    }

    // Lưu session
    $_SESSION['user'] = [
        'id_accounts' => $account['id_accounts'],
        'id_users' => $account['id_users'] ?? null,
        'username' => $account['username'],
        'full_name' => $account['full_name'] ?? '',
        'email' => $account['email'] ?? '',
        'role' => $account['role'],
        'avatar' => $account['avatar'] ?? null
    ];

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => $_SESSION['user']
    ]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Login error: ' . $e->getMessage()]);
    exit;
}
?>
