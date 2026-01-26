<?php
session_start();
include(__DIR__ . "/../../config/database.php");
include(__DIR__ . "/../../model/Category.php");
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    header("Location: /QuanLyBanHangFigure/index.php");
    exit();
}
$cateModel = new Category($conn);
$categories = $cateModel->getAll();
$root = "/QuanLyBanHangFigure";
if($_SERVER['REQUEST_METHOD'] === "POST"){
    $id_category=$_POST['id_categories'];
    $name= $_POST['name']; 
    $slug= $_POST['slug'];

    
if($cateModel->getById($id_category)){
        echo "<div class='alert alert-danger'>Mã danh mục <strong>$id_category</strong> đã tồn tại!</div>";
    } else {
        $cateModel->create($id_category, $name, $slug);
        header("Location: $root/views/admin/categories.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm danh mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $root ?>/css/dashboard.css">
</head>
<body>
    <div class="container mb-4">
        <h3>Thêm danh mục mới</h3>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Mã danh mục (YÊU CẦU NHẬP MÃ THEO MẪU: CG001)</label>
                <input type="text" name="id_categories" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tên danh mục</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label"> Slug</label>
                <input type="text" name="slug" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Thêm danh mục</button>
            <a href="<?= $root ?>/views/admin/categories" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>