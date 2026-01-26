<?php
session_start();
include("../config/database.php");
include("../model/Account.php");

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $accountModel = new Account($conn);
    $account = $accountModel->getAccountWithUser($username);

    if (!$account) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tài khoản không tồn tại.'
        ]);
        exit;
    }

    if (!password_verify($password, $account['password'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sai mật khẩu.'
        ]);
        exit;
    }

    // Lưu session
    $_SESSION['user'] = [
        'id_accounts' => $account['id_accounts'],
        'username' => $account['username'],
        'full_name' => $account['full_name'],
        'email' => $account['email'],
        'role' => $account['role'],
        'avatar' => $account['avatar'] ?? null
    ];

    // trả JSON điều hướng thay vì header redirect
    echo json_encode([
        'status' => 'success',
        'redirect' =>
            $account['role'] === 'admin'
            ? '/QuanLyBanHangFigure/views/admin/dashboard.php'
            : '/QuanLyBanHangFigure/index.php'
    ]);

    exit;
}
