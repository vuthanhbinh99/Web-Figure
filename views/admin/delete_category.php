<?php
session_start();
include(__DIR__ . "/../../config/database.php");
include(__DIR__ . "/../../model/Category.php");
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    header("Location: /QuanLyBanHangFigure/index.php");
    exit();
}
$cateModel = new Category($conn);
$id = $_GET["id"] ?? null;
if($id){
    $cateModel->delete($id);
}
header("Location: /QuanLyBanHangFigure/views/admin.categories.php");
exit();
?>