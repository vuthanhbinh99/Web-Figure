<?php
header('Content-Type: application/json; charset=utf-8');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header('Access-Control-Allow-Origin: ' . $origin);
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../../config/database.php';

session_start();

try {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Access denied']);
        exit;
    }

    $sql = "SELECT * FROM vouchers ORDER BY id_voucher DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => $vouchers
    ]);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    error_log("Database Error in vouchers/list.php: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    error_log("Server Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server Error: ' . $e->getMessage()]);
    exit;
}
?>
