<?php
session_start();
include("../config/database.php");
require("../model/Account.php");
require("../model/User.php");

if (!isset($_SESSION['user'])){
    header("Location: /QuanLyBanHangFigure/index.php");
    exit;
}

try {
    $id = $_SESSION['user']['id_accounts'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "UPDATE users SET full_name = :full_name, email = :email WHERE id_accounts = :id_accounts";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':full_name', $full_name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':id_accounts', $id, PDO::PARAM_STR);

    if(!$stmt->execute()){
        $_SESSION['error_update'] = "Không thể cập nhật thông tin người dùng!";
        header("Location: /QuanLyBanHangFigure/views/profile.php");
        exit;
    }

    $_SESSION['user']['full_name'] = $full_name;
    $_SESSION['user']['email'] = $email;

    if(!empty($password)){
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sqlPass = "UPDATE accounts SET password = :password WHERE id_accounts = :id_accounts";
        $stmt2 = $conn->prepare($sqlPass);
        $stmt2->bindParam(':password', $hashed, PDO::PARAM_STR);
        $stmt2->bindParam(':id_accounts', $id, PDO::PARAM_STR);

        if (!$stmt2->execute()) {
            $_SESSION['error_update'] = "Cập nhật mật khẩu thất bại!";
            header("Location: /QuanLyBanHangFigure/views/profile.php");
            exit;
        }
    }
    $_SESSION['success_update'] = "Cập nhật thông tin thành công!";
    header("Location: /QuanLyBanHangFigure/index.php");
    exit;
} catch (PDOException $e) {
    error_log("Update profile error: " . $e->getMessage());
    $_SESSION['error_update'] = "Lỗi cập nhật: " . $e->getMessage();
    header("Location: /QuanLyBanHangFigure/views/profile.php");
    exit;
}
?>
