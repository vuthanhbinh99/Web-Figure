<?php 
session_start(); 
$root = "/QuanLyBanHangFigure";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $root ?>/css/index.css">
</head>
<body class="bg-light">
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
                        <li><a class="dropdown-item" href="<?= $root?>./views/profile.php">Chỉnh sửa thông tin</a></li>
                        <li><a class="dropdown-item text-danger" href="<?= $root ?>./views/logout.php">Đăng xuất</a></li>
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

<div class="container mt-5">
    <h3 class="mb-4">Giỏ hàng của bạn</h3>
    <table class="table table-bordered" id="cart-table">
        <thead>
            <tr>
                <th>Ảnh</th>
                <th>Sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Tổng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <h4 class="text-end">Tổng tiền: <span id="cart-total" class="text-danger">0đ</span></h4>

    <div class="text-end mt-4">
    <button id="btnPlaceOrder" class="btn btn-success">Đặt hàng</button>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    loadCart();
});

// Load giỏ hàng
function loadCart() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let tbody = document.querySelector("#cart-table tbody");
    tbody.innerHTML = "";
    let total = 0;

    cart.forEach((item, index) => {
        let row = `
        <tr>
            <td><img src="/QuanLyBanHangFigure/${item.image}" width="60"></td>
            <td>${item.name}</td>
            <td>${Number(item.price).toLocaleString()}đ</td>
            <td>
                <button class="btn btn-sm btn-secondary" onclick="changeQty(${index}, -1)">-</button>
                <span class="mx-2">${item.quantity}</span>
                <button class="btn btn-sm btn-secondary" onclick="changeQty(${index}, 1)">+</button>
            </td>
            <td>${(item.quantity * item.price).toLocaleString()}đ</td>
            <td>
                <button class="btn btn-sm btn-danger" onclick="deleteItem(${index})">Xóa</button>
            </td>
        </tr>
        `;
        tbody.innerHTML += row;

        total += item.quantity * item.price;
    });

    document.getElementById("cart-total").textContent = total.toLocaleString() + "đ";
}

// Tăng/giảm số lượng
function changeQty(index, change) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    cart[index].quantity += change;

    if (cart[index].quantity < 1) cart[index].quantity = 1;

    localStorage.setItem("cart", JSON.stringify(cart));
    loadCart();
}

// Xóa sản phẩm
function deleteItem(index) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    cart.splice(index, 1);

    localStorage.setItem("cart", JSON.stringify(cart));
    loadCart();
}

document.addEventListener("DOMContentLoaded", () => {
    const orderBtn = document.getElementById("btnPlaceOrder");

    if (!orderBtn) return;

    // Chuyển hướng sang trang checkout
    orderBtn.addEventListener("click", () => {
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        if (cart.length === 0) {
            alert("Giỏ hàng trống!");
            return;
        }
        window.location.href = "/QuanLyBanHangFigure/views/checkout.php";
    });
});
</script>


</body>
</html>
