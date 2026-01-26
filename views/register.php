<?php
session_start();

$otp = trim($_POST['otp_code'] ?? '');
$email = trim($_POST['email'] ?? '');

if (!isset($_SESSION['otp_code']) || !isset($_SESSION['otp_email'])) {
    $_SESSION['error'] = "Vui lòng gửi mã xác thực email.";
    header("Location: /QuanLyBanHangFigure/index.php#registerModal");
    exit();
}

// kiểm tra email
if ($_SESSION['otp_email'] !== $email) {
    $_SESSION['error'] = "Email không trùng khớp với email đã gửi mã OTP.";
    header("Location: /QuanLyBanHangFigure/index.php#registerModal");
    exit();
}

// kiểm tra mã OTP
if ($otp != $_SESSION['otp_code']) {
    $_SESSION['error'] = "Mã OTP không đúng.";
    header("Location: /QuanLyBanHangFigure/index.php#registerModal");
    exit();
}

// kiểm tra hết hạn
if (time() > $_SESSION['otp_expire']) {
    $_SESSION['error'] = "Mã OTP đã hết hạn.";
     header("Location: /QuanLyBanHangFigure/index.php#registerModal");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="../css/register.css">
    <title>Đăng kí</title>
</head>
<body>
    <div class="register-box">
    <h3 class="text-center mb-3">Tạo tài khoản</h3>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form action="../controllers/RegisterController.php" method="POST">

        <h5 class="mb-3 text-primary">Thông tin đăng nhập</h5>

        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" class="form-control" name="username" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" class="form-control" name="password" required>
        </div>

        <h5 class="mb-3 mt-4 text-primary">Thông tin cá nhân</h5>

        <div class="mb-3">
            <label class="form-label">Họ và tên</label>
            <input type="text" class="form-control" name="full_name" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input type="number" class="form-control" name="phone">
        </div>

        <div class="mb-3">
            <label class="form-label">Địa chỉ</label>
            <textarea class="form-control" name="address" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-custom w-100">Đăng ký</button>

        <p class="text-center mt-3">
            Đã có tài khoản? <a href="login.php">Đăng nhập</a>
        </p>
    </form>
</div>
</body>
</html>