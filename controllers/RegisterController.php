<?php
session_start();
header("Content-Type: application/json");

ini_set('display_errors', 1);
error_reporting(E_ALL);

include "../config/database.php";
include "../model/User.php";
include "../model/Account.php";
include "../helpers/auto_id.php";
include "../helpers/email_helper.php";

if (!$conn) {
    echo json_encode(["status" => false, "message" => "Database connection failed"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(["status" => false, "message" => "Invalid request"]);
    exit;
}

$username   = trim($_POST['username'] ?? '');
$password   = trim($_POST['password'] ?? '');
$full_name  = trim($_POST['full_name'] ?? '');
$email      = trim($_POST['email'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$address    = trim($_POST['address'] ?? '');

error_log("Register data: " . json_encode($_POST));

$errors = [];
if (!$username) $errors[] = "Tên đăng nhập là bắt buộc.";
if (!$password) $errors[] = "Mật khẩu là bắt buộc.";
if (!$full_name) $errors[] = "Họ tên là bắt buộc.";
if (!$email) $errors[] = "Email là bắt buộc.";

if (!empty($errors)) {
    echo json_encode(["status" => false, "message" => implode("\n", $errors)]);
    exit;
}

$accountModel = new Account($conn);
$userModel = new User($conn);

// Kiểm tra username trùng
$check = $accountModel->getByUsername($username);
if ($check) {
    echo json_encode(["status" => false, "message" => "Tên đăng nhập đã tồn tại"]);
    exit;
}

try {
    $id_accounts = generateId("AC");
    $id_users = generateId("US");
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    error_log("Generated IDs - Account: $id_accounts, User: $id_users");

    // Tạo account
    $createAcc = $accountModel->register($id_accounts, $username, $hashedPassword, "customer");
    if (!$createAcc) {
        echo json_encode(["status" => false, "message" => "Không thể tạo tài khoản"]);
        exit;
    }

    // Tạo user
    $createUser = $userModel->create($id_users, $full_name, $email, $phone, $address, $id_accounts);
    if (!$createUser) {
        echo json_encode(["status" => false, "message" => "Không thể tạo thông tin người dùng"]);
        exit;
    }

    // Tạo mã OTP (6 chữ số)
    $otp_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Lưu OTP vào SESSION với thời hạn 2 phút (120 giây)
    $_SESSION['otp'] = [
        'email' => $email,
        'code' => $otp_code,
        'expires_at' => time() + 120, // Hết hạn sau 2 phút
        'id_accounts' => $id_accounts
    ];

    // Gửi email OTP
    $emailSent = sendOTPEmail($email, $otp_code, $full_name);
    if (!$emailSent) {
        error_log("Email gửi thất bại cho: $email");
    }

    echo json_encode([
        "status" => true,
        "message" => "Đăng ký thành công! Vui lòng kiểm tra email để nhập mã OTP.",
        "pending_email" => $email,
        "redirect" => "otp_verification"
    ]);
    exit;

} catch (Exception $e) {
    error_log("Register error: " . $e->getMessage());
    echo json_encode([
        "status" => false,
        "message" => "Lỗi: " . $e->getMessage()
    ]);
}
?>