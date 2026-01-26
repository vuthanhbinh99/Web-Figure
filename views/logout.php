<?php
session_start();

// Xóa toàn bộ session
session_unset();
session_destroy();

// Quay về trang chủ
header("Location: /QuanLyBanHangFigure/index.php");
exit();
?>
