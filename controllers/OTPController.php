<?php
session_start();
header("Content-Type: application/json");

ini_set('display_errors', 1);
error_reporting(E_ALL);

include "../config/database.php";
include "../model/Account.php";
include "../helpers/email_helper.php";

if (!$conn) {
    echo json_encode(["status" => false, "message" => "Database connection failed"]);
    exit;
}

$action = $_REQUEST['action'] ?? '';
$accountModel = new Account($conn);

try {
    if ($action === 'verify') {
        // Xác thực OTP
        $otp_code = trim($_POST['otp_code'] ?? '');
        
        if (!isset($_SESSION['otp']) || !is_array($_SESSION['otp'])) {
            echo json_encode(["status" => false, "message" => "Không tìm thấy yêu cầu OTP"]);
            exit;
        }

        $otp_data = $_SESSION['otp'];

        // Kiểm tra OTP đã hết hạn chưa
        if (time() > $otp_data['expires_at']) {
            unset($_SESSION['otp']);
            echo json_encode(["status" => false, "message" => "Mã OTP đã hết hạn, vui lòng yêu cầu mã mới"]);
            exit;
        }

        // Kiểm tra mã OTP
        if ($otp_code !== $otp_data['code']) {
            echo json_encode(["status" => false, "message" => "Mã OTP không chính xác"]);
            exit;
        }

        // OTP đúng - Đăng nhập tự động
        $email = $otp_data['email'];
        $id_accounts = $otp_data['id_accounts'];

        // Lấy thông tin account để lưu vào session
        $account = $accountModel->getById($id_accounts);
        if ($account) {
            $_SESSION['user_id'] = $account['id_accounts'];
            $_SESSION['username'] = $account['username'];
            $_SESSION['role'] = $account['role'];

            // Xóa OTP khỏi session
            unset($_SESSION['otp']);

            echo json_encode([
                "status" => true,
                "message" => "Xác thực OTP thành công! Đang đăng nhập...",
                "redirect" => "index.php"
            ]);
            exit;
        } else {
            echo json_encode(["status" => false, "message" => "Không tìm thấy tài khoản"]);
            exit;
        }

    } elseif ($action === 'resend') {
        // Gửi lại OTP
        if (!isset($_SESSION['otp']) || !is_array($_SESSION['otp'])) {
            echo json_encode(["status" => false, "message" => "Không tìm thấy yêu cầu OTP"]);
            exit;
        }

        $otp_data = $_SESSION['otp'];
        $email = $otp_data['email'];

        // Tạo mã OTP mới
        $new_otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Cập nhật OTP trong session
        $_SESSION['otp']['code'] = $new_otp;
        $_SESSION['otp']['expires_at'] = time() + 120; // Hết hạn sau 2 phút mới

        // Lấy tên người dùng từ database để gửi email
        $stmt = $conn->prepare("
            SELECT u.full_name 
            FROM users u 
            WHERE u.email = :email
        ");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();
        $full_name = $user['full_name'] ?? 'User';

        // Gửi email OTP mới
        $emailSent = sendOTPEmail($email, $new_otp, $full_name);
        if ($emailSent) {
            echo json_encode([
                "status" => true,
                "message" => "Mã OTP mới đã được gửi đến email của bạn"
            ]);
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Gửi email thất bại, vui lòng thử lại"
            ]);
        }
        exit;
    } else {
        echo json_encode(["status" => false, "message" => "Invalid action"]);
        exit;
    }

} catch (Exception $e) {
    error_log("OTPController error: " . $e->getMessage());
    echo json_encode([
        "status" => false,
        "message" => "Lỗi: " . $e->getMessage()
    ]);
}
?>
