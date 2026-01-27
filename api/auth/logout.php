<?php
session_start();

header('Content-Type: application/json; charset=utf-8');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header('Access-Control-Allow-Origin: ' . $origin);
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

require_once __DIR__ . '/../../config/database.php';

// Xóa session
session_destroy();

http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => ['message' => 'Đăng xuất thành công']
    ]);
    exit;
?>
