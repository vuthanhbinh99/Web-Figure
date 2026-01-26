<?php
session_start();
include("../../config/database.php");
include("../../model/Order.php");
include("../../model/OrderItem.php");

$root = "/QuanLyBanHangFigure";

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    header("Location: $root/index.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) die("Thiếu ID đơn hàng");

$orderModel = new Order($conn);
$orderItemModel = new OrderItem($conn);

$order = $orderModel->getById($id);
$items = $orderItemModel->getByOrderId($id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<h2 class="mb-3">Chi tiết đơn hàng: <?= $order['id_order'] ?></h2>

<div class="card p-3 mb-4">
    <h5>Thông tin khách hàng</h5>
    <p><strong>Họ tên:</strong> <?= $order['full_name'] ?></p>
    <p><strong>SĐT:</strong> <?= $order['phone'] ?></p>
    <p><strong>Địa chỉ:</strong> <?= $order['address'] ?></p>
    <p><strong>Phương thức thanh toán:</strong> <?= $order['payment_method'] ?></p>
    <p><strong>Trạng thái:</strong> <span class="badge bg-info"><?= $order['status'] ?></span></p>
</div>

<h4>Sản phẩm trong đơn</h4>

<table class="table table-bordered">
    <thead class="table-secondary">
        <tr>
            <th>Tên sản phẩm</th>
            <th>Số lượng</th>
            <th>Giá</th>
            <th>Thành tiền</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($items as $it): ?>
        <tr>
            <td><?= $it['name'] ?></td>
            <td><?= $it['quantity'] ?></td>
            <td><?= number_format($it['price'],0,',','.') ?>đ</td>
            <td><?= number_format($it['quantity'] * $it['price'],0,',','.') ?>đ</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p class="fw-bold fs-5 mt-3">
    Tổng tiền: <span class="text-danger"><?= number_format($order['total'],0,',','.') ?>đ</span>
</p>

<a href="orders.php" class="btn btn-secondary mt-3">Quay lại</a>

</body>
</html>
