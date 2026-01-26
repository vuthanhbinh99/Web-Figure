<?php
session_start();
include(__DIR__ . "/../../config/database.php");
include(__DIR__ . "/../../model/Product.php");
include(__DIR__ . "/../../model/Category.php");

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    header("Location: /QuanLyBanHangFigure/index.php");
    exit();
}

$productModel = new Product($conn);
$cateModel = new Category($conn);
$categories = $cateModel->getAll();

$root = "/QuanLyBanHangFigure";

if($_SERVER['REQUEST_METHOD'] === "POST"){
    $id_product = $_POST["id_products"];
    $name=$_POST['name'];
    $price=$_POST['price'];
    $description=$_POST['description'];
    $stock=$_POST['stock'];
    $id_category=$_POST['id_categories'];
    $id_featured=$_POST['id_featured'];

    $image='';
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){
        $ext= pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = 'images/' . uniqid() .'.'. $ext;
        move_uploaded_file($_FILES['image']['tmp_name'],__DIR__ . '/../../' . $image);
    }

    $productModel->create($id_product, $name, $price, $description, $image, $stock, $id_category,$id_featured);
    header("Location: /QuanLyBanHangFigure/views/admin/products.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $root ?>/css/dashboard.css">
</head>
<body>
    <div class="container mt-4">
        <h3>Nhập sản phẩm từ Excel</h3>
        <form method="post" enctype="multipart/form-data" action="<?= $root ?>/views/admin/import_product_excel.php">
            <div class="mb-3">
                <label class="form-label">Chọn file Excel (.xlsx hoặc .csv)</label>
                <input type="file" name="excel_file" class="form-control" required>
            </div>
            <div class="mb-3">
            <label class="form-label">Chọn file ZIP ảnh</label>
            <input type="file" name="images_zip" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Import</button>
        </form>
        <h3>Thêm sản phẩm mới</h3>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Mã sản phẩm(YÊU CẦU NHẬP MÃ THEO MẪU: PD001)</label>
                <input type="text" name="id_products" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tên sản phẩm</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Giá</label>
                <input type="number" name="price" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Ảnh sản phẩm</label>
                <input type="file" name="image" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Số lượng</label>
                <input type="number" name="stock" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nổi bật (0: SẢN PHẨM KHÔNG NỔI BẬT, 1: LÀ SẢN PHẨM NỔI BẬT)</label>
                <input type="number" name="id_featured" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Danh mục</label>
                <select name="id_categories" class="form-select" required>
                    <?php foreach($categories as $cate): ?>
                        <option value="<?= $cate['id_categories'] ?>"  ><?= $cate['name'] ?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Thêm sản phẩm</button>
            <a href="<?= $root ?>/views/admin/products" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>