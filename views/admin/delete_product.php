<?php
session_start();
include(__DIR__ . "/../../config/database.php");
include(__DIR__ . "/../../model/Product.php");
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    header("Location: /QuanLyBanHangFigure/index.php");
    exit();
}

$productModel = new Product($conn);
$id = $_GET['id'] ?? null;
if ($id) {
    $productModel->delete($id);
}
header("Location: /QuanLyBanHangFigure/views/admin/products.php");
exit();
?>