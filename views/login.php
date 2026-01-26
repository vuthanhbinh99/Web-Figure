<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">\
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="../css/login.css">
    <title>Đăng nhập</title>
</head>
<body>
   <div class="login-box">
    <h3 class="text-center mb-4">Đăng nhập</h3>

    <!-- Hiển thị lỗi nếu có -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <form action="../controllers/LoginController.php" method="POST">

        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" class="form-control" name="username" placeholder="Nhập tên đăng nhập..." required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" class="form-control" name="password" placeholder="Nhập mật khẩu..." required>
        </div>

        <button type="submit" class="btn btn-custom w-100">Đăng nhập</button>

        <p class="text-center mt-3">
            Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
        </p>
    </form>
</div>


</body>
</html>
</body>
</html>