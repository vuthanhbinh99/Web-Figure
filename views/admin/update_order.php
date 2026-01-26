<?php
session_start();
include("../../config/database.php");
include("../../model/Order.php");

$root = "/QuanLyBanHangFigure";

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    header("Location: $root/index.php");
    exit();
}

$orderModel = new Order($conn);
$id = $_GET['id'] ?? null;

if(!$id) die("Thiếu ID đơn hàng");

$order = $orderModel->getById($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $orderModel->updateStatus($id, $status);

    header("Location: orders.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<h2 class="mb-3">Cập nhật trạng thái đơn: <?= $order['id_order'] ?></h2>

<form method="POST" class="card p-3" style="max-width:400px;">
    <label class="form-label fw-bold">Chọn trạng thái:</label>
    <select name="status" class="form-select mb-3">
        <option value="pending" <?= $order['status']=='pending'?'selected':'' ?>>Chờ duyệt</option>
        <option value="confirmed" <?= $order['status']=='confirmed'?'selected':'' ?>>Đã duyệt</option>
        <option value="shipping" <?= $order['status']=='shipping'?'selected':'' ?>>Đang giao hàng</option>
        <option value="completed" <?= $order['status']=='completed'?'selected':'' ?>>Hoàn thành</option>
        <option value="cancelled" <?= $order['status']=='cancelled'?'selected':'' ?>>Đã hủy</option>
    </select>

    <button class="btn btn-primary" type="submit">Cập nhật</button>
    <a href="orders.php" class="btn btn-secondary ms-2">Quay lại</a>
</form>

</body>
</html>
