<?php
/**
 * API Router
 * Tất cả API requests được xử lý thông qua file này
 * 
 * Usage: /api/[resource]/[action].php
 * Example: /api/products/list.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Log request
error_log("API Request: " . $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI']);

// Được điều chỉnh để điều hướng đến các endpoint tương ứng
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/QuanLyBanHangFigure/api/', '', $path);

// Các endpoint được xử lý bởi các file tương ứng trong thư mục con
// Không cần xử lý ở đây, Apache sẽ tự động điều hướng đến file tương ứng
?>
