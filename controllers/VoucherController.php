<?php
session_start();
header("Content-Type: application/json");

ini_set('display_errors', 1);
error_reporting(E_ALL);

include "../config/database.php";
include "../model/Voucher.php";

if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$action = $_REQUEST['action'] ?? '';
$voucherModel = new Voucher($conn);

try {
    if ($action === 'check') {
        // Kiểm tra voucher có hợp lệ không
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);
        $code = $data['code'] ?? '';

        if (!$code) {
            echo json_encode(["status" => "error", "message" => "Mã voucher không hợp lệ"]);
            exit;
        }

        $voucher = $voucherModel->getByCode($code);
        if (!$voucher) {
            echo json_encode(["status" => "error", "message" => "Mã voucher không tồn tại hoặc đã hết hạn"]);
            exit;
        }

        // Kiểm tra số lần dùng
        if ($voucher['max_uses'] > 0 && $voucher['used_count'] >= $voucher['max_uses']) {
            echo json_encode(["status" => "error", "message" => "Mã voucher đã hết số lần sử dụng"]);
            exit;
        }

        echo json_encode([
            "status" => "success",
            "message" => "Mã voucher hợp lệ",
            "voucher" => [
                "id_voucher" => $voucher['id_voucher'],
                "code" => $voucher['code'],
                "discount_percent" => $voucher['discount_percent']
            ]
        ]);
        exit;

    } elseif ($action === 'list') {
        // Lấy danh sách vouchers active
        $vouchers = $voucherModel->getAll();
        echo json_encode([
            "status" => "success",
            "data" => $vouchers
        ]);
        exit;

    } else {
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        exit;
    }

} catch (Exception $e) {
    error_log("VoucherController error: " . $e->getMessage());
    echo json_encode([
        "status" => "error",
        "message" => "Lỗi: " . $e->getMessage()
    ]);
}
?>
