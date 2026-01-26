<?php
session_start();
include(__DIR__ . "/../../config/database.php");
include(__DIR__ . "/../../model/Category.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /QuanLyBanHangFigure/index.php");
    exit();
}

$cateModel = new Category($conn);
$categories = $cateModel->getAll();

$root = "/QuanLyBanHangFigure";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý danh mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $root ?>/css/dashboard.css">
    <link rel="stylesheet" href="<?= $root ?>/css/index.css">
</head>
<body>
  <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container d-flex align-items-center">
        <a class="navbar-brand fw-bold" href="<?= $root ?>/index.php">FigureStore</a>
        <form class="form-search d-none d-lg-flex" role="search" action="store.php" method="get">
            <input class="form-control me-2" type="search" placeholder="Tìm kiếm sản phẩm..." name="q">
            <button class="btn btn-outline-light" type="submit">Tìm</button>
        </form>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link active" href="<?= $root ?>/index.php">Trang chủ</a></li>
                <!--Dropdown danh mục -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="cateMenu" data-bs-toggle="dropdown">
                        Danh mục
                    </a>
                    <ul class="dropdown-menu">
                        <?php foreach ($categories as $cate):?>
                            <li>
                                <a class="dropdown-item" href="<?= $root ?>/views/Store.php?category=<?= $cate['slug'] ?>">
                                    <?= $cate['name'] ?>
                                </a> 
                            </li>
                        <?php endforeach;?>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="<?= $root ?>/views/Store.php">Sản phẩm</a></li>
                
                <!-- Link Dashboard nếu là admin -->
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-warning" href="<?= $root ?>/views/admin/dashboard.php">
                            Dashboard
                        </a>
                    </li>
                <?php endif; ?>
               <?php if (!isset($_SESSION['user'])): ?>
                <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng nhập</a></li>
                <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Đăng ký</a></li>
               <?php else: ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <?php if (!empty($_SESSION['user']['avatar'])): ?>
                            <img src="<?= $_SESSION['user']['avatar'] ?>" class="rounded-circle me-2" width="35" height="35">
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-person-circle me-2" viewBox="0 0 16 16">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                            </svg>
                        <?php endif; ?>
                        <?= $_SESSION['user']['full_name'] ?? $_SESSION['user']['username'] ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><h6 class="dropdown-header">Thông tin tài khoản</h6></li>
                        <li><span class="dropdown-item-text">Họ tên: <?= $_SESSION['user']['full_name'] ?></span></li>
                        <li><span class="dropdown-item-text">Username: <?= $_SESSION['user']['username'] ?></span></li>
                        <li><span class="dropdown-item-text">Email: <?= $_SESSION['user']['email'] ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= $root?>/views/profile.php">Chỉnh sửa thông tin</a></li>
                        <li><a class="dropdown-item text-danger" href="<?= $root ?>/views/logout.php">Đăng xuất</a></li>
                    </ul>
                </li>
               <?php endif; ?>
            </ul>

            <!-- Mobile search -->
            <form class="form-search d-lg-none mt-2" action="store.php" method="get">
                <input class="form-control" type="search" placeholder="Tìm kiếm..." name="q">
                <button class="btn btn-outline-light mt-1" type="submit">Tìm</button>
            </form>
        </div>
    </div>
</nav>

<div class ="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block sidebar py-3">
        <ul class="nav flex-column">
            <li class="nav-item mb-2"><a href="dashboard.php" class="nav-link">Dashboard</a></li>
            <li class="nav-item mb-2"><a href="<?= $root ?>/views/admin/products.php" class="nav-link">Quản lý sản phẩm</a></li>
            <li class="nav-item mb-2"><a href="<?= $root ?>/views/admin/categories.php" class="nav-link active">Quản lý danh mục</a></li>
             <li class="nav-item mb-2"><a href="<?= $root?>/views/admin/orders.php" class="nav-link">Quản lý đơn hàng</a></li>
        </ul>
        </nav>
        <!-- Main content-->
         <main class="col-md-10 ms-sm-auto px4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>Quản lý danh mục</h3>
                <a href="<?= $root ?>/views/admin/add_category.php" class="btn btn-success">Thêm danh mục</a>
            </div>

            <table class="table table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID danh mục</th>
                        <th>Tên danh mục</th>
                        <th>Slug</th>
                        <th width="160">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($categories as $cate):?>
                        <tr>
                            <td><?= $cate['id_categories'] ?></td>
                            <td><?= $cate['name'] ?></td>
                            <td><?= $cate['slug'] ?></td>
                            <td>
                                <a href="<?= $root ?>/views/admin/edit_category.php?id=<?= $cate['id_categories'] ?>" class="btn btn-sm btn-primary">Sửa</a>
                                <a href="<?= $root ?>/views/admin/delete_category.php?id<?= $cate['id_categories'] ?>" onclick="return confirm('Bạn chắc chắn muốn xóa')" class="btn btn-sm btn-danger">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>