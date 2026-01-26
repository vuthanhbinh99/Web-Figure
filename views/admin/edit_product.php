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

$id = $_GET['id'] ?? null;
$product = $productModel->getById($id);

if (!$product) {
    echo "Sản phẩm không tồn tại!";
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id_products = $_POST['id_products'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $id_category=$_POST['id_categories'];
    $id_featured=$_POST['id_featured'] ?? 0;

    $image=$product['image'];
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){
        $ext= pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = 'images/' . uniqid() .'.'. $ext;
        move_uploaded_file($_FILES['image']['tmp_name'],__DIR__ . '/../../' . $image);
    }

    $productModel->update($id_products, $name, $price, $description, $image, $stock, $id_category, $id_featured) ;
    header("Location: /QuanLyBanHangFigure/views/admin/products.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $root ?>/css/dashboard.css">
</head>
<body>
    <div class="container mt-4">
        <h3>Sủa sản phẩm</h3>
        <form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label class="form-label">Mã sản phẩm (YÊU CẦU NHẬP MÃ THEO MẪU: PD001)</label>
        <input type="text" name="id_products" class="form-control" value="<?= htmlspecialchars($product['id_products']) ?>" readonly>
    </div>
    <div class="mb-3">
        <label class="form-label">Tên sản phẩm</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Giá</label>
        <input type="number" name="price" class="form-control" value="<?= htmlspecialchars($product['price']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Ảnh sản phẩm</label>
        <input type="file" name="image" class="form-control">
        <?php if($product['image']): ?>
            <img src="<?= $root ?>/<?= $product['image'] ?>" width="100" class="mt-2">
        <?php endif; ?>
    </div>
    <div class="mb-3">
        <label class="form-label">Số lượng</label>
        <input type="number" name="stock" class="form-control" value="<?= htmlspecialchars($product['stock']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Nổi bật (0: SẢN PHẨM KHÔNG NỔI BẬT, 1: LÀ SẢN PHẨM NỔI BẬT)</label>
       <input type="number" name="id_featured" class="form-control" value="<?= htmlspecialchars($product['id_featured'] ?? 0) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Danh mục</label>
        <select name="id_categories" class="form-select" required>
            <?php foreach($categories as $c): ?>
                <option value="<?= $c['id_categories'] ?>" <?= $c['id_categories'] == $product['id_categories'] ? 'selected' : '' ?>>
                    <?= $c['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Cập nhật</button>
    <a href="<?= $root ?>/views/admin/products.php" class="btn btn-secondary">Hủy</a>
</form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>