<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../model/Voucher.php';

try {
    $code = isset($_GET['code']) ? trim($_GET['code']) : '';

    if (!$code) {
        errorResponse('Voucher code is required', 400);
    }

    $voucherModel = new Voucher($conn);
    $voucher = $voucherModel->getByCode($code);
    
    if (!$voucher) {
        errorResponse('Voucher not found', 404);
    }
    
    if ($voucher['is_active'] == 0) {
        errorResponse('Voucher is inactive', 400);
    }
    
    if ($voucher['usage_count'] >= $voucher['max_uses']) {
        errorResponse('Voucher usage limit reached', 400);
    }
    
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => $voucher
    ]);
    exit;
} catch (Exception $e) {
    errorResponse($e->getMessage());
}
?>
