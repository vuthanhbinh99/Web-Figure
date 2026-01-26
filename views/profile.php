<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user'])){
    header("Location: /QuanLyBanHangFigure/index.php");
    exit;
}

$root = "/QuanLyBanHangFigure";
$user = $_SESSION['user'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Tài Khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class ="bg-light">
    <div class = "card shadow mt-5">
        <div class ="card shadow p-4">
            <h3 class = "fw-bold">Thông tin tài khoản</h3>
            <hr>
            <!--hiển thị thông tin -->
            <p><strong>Họ tên:</strong> <?=  $user['full_name'] ?></p>
            <p><strong>Email:</strong><?=  $user['email'] ?></p>
            <p><strong>Tên đăng nhập:</strong> <?= $user['username']  ?></p>

            <button class="btn btn-dark mt-3" data-bs-toggle="collapse" data-bs-target ="#editForm">
                Chỉnh sửa 
            </button>

            <!--Form ẩn -->
            <div id="editForm" class =" collapse mt-4">
               <form action="/QuanLyBanHangFigure/controllers/UpdateProfileController.php" method="POST">
                    <div class="mb-3">
                        <label>Họ Tên</label>
                        <input type="text" name="full_name" class="form-control" value="<?= $user['full_name'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= $user['email'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label> Mật khẩu</label>
                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới (nếu muốn đổi)">
                    </div>

                    <button class="btn btn-primary w-100">Cập nhật</button>
</form>
            </div>

            <?php if(isset($_SESSION['success_update'])): ?>
                <div class="alert alert-success mt-3">
                    <?= $_SESSION['success_update']; unset($_SESSION['success_update']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error_update'])): ?>
                <div class="alert alert-danger mt-3">
                    <?= $_SESSION['error_update']; unset($_SESSION['error_update']); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>