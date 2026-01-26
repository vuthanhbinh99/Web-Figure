<?php
session_start();
include("../config/database.php");
include("../model/Product.php");
$id_order = $_GET['id'];
$root="/QuanLyBanHangFigure";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= $root ?>/css/index.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container d-flex align-items-center">
        <a class="navbar-brand fw-bold" href="<?= $root ?>/index.php">FigureStore</a>

        <form class="form-search d-none d-lg-flex" role="search" action="<?= $root ?>/views/Store.php" method="get">
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
                
                 <!-- cart -->
                <li class="nav-item position-relative">
                    <a class="nav-link" href="<?= $root ?>/views/cart.php">
                        <i class="bi bi-cart3" style="font-size: 22px;"></i>
                        <span id="cart-count" class="position-absolute top-0 start-100 
                            translate-middle badge rounded-pill bg-danger">
                            0
                        </span>
                    </a>
                </li>

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
            <form class="form-search d-lg-none mt-2" action="<?= $root ?>/views/Store.php" method="get">
                <input class="form-control" type="search" placeholder="Tìm kiếm..." name="q">
                <button class="btn btn-outline-light mt-1" type="submit">Tìm</button>
            </form>
        </div>
    </div>
</nav>

    <div class="container mt-5 text-center">
        <div class="card p-4 shadow">
            <h2 class="text-success">Đặt hàng thành công</h2>
            <p class="fs-5">Mã đơn hàng của bạn: <strong><?= htmlspecialchars($id_order) ?></strong></p>
            <p>Cảm ơn bạn đã mua sắm tại cửa hàng của chúng tôi!</p>
            <a href="<?= $root ?>/views/Store.php" class="btn btn-primary mt-3">Tiếp tục mua sắm</a>
        </div>

        <h4 class="mt-5">Bạn có thể thích những sản phẩm này:</h4>
        <div class="row mt-3">
            <?php
            $productModel = new Product($conn);
            $suggestions = $productModel->getSuggestions(4);
            foreach($suggestions as $prod){
                echo '<div class="col-md-3 mb-3">
                    <div class="card product-card shadow-sm">
                        <img src="'.$root.'/'.htmlspecialchars($prod['image']).'" class="card-img-top" alt="'.htmlspecialchars($prod['name']).'">
                        <div class="card-body">
                            <h5 class="card-title">'.htmlspecialchars($prod['name']).'</h5>
                            <p class="card-text">'.number_format($prod['price'],0,',','.').' ₫</p>
                            <a href="'.$root.'/views/product_detail.php?id='.$prod['id_products'].'" class="btn btn-sm btn-success">Xem chi tiết</a>
                        </div>
                    </div>
                  </div>';
            }            
            ?>
            
        </div>
    </div>

    <!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Đăng nhập</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="loginError" class="alert alert-danger d-none"></div>
        <form id="loginForm">
          <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" class="form-control" name="username" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" class="form-control" name="password" required>
          </div>
          <button type="submit" class="btn btn-dark w-100">Đăng nhập</button>
          <p class="mt-3 text-center">Chưa có tài khoản? 
            <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Đăng ký</a>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer>
    <div class="footer-container">
        <div class="footer-col">
            <h6>Giới thiệu</h6>
            <a href="#">Giới thiệu về FigureStore</a>
            <a href="#">Liên hệ hợp tác</a>
        </div>
        <div class="footer-col">
            <h6>Tin tức</h6>
            <a href="#">Tin tuyển dụng</a>
            <a href="#">Sản phẩm khuyến mại</a>
            <a href="#">Sản phẩm mới</a>
        </div>
        <div class="footer-col">
            <h6>Hỗ trợ khách hàng</h6>
            <a href="#">Tra cứu đơn hàng</a>
            <a href="#">Hướng dẫn mua hàng trực tuyến</a>
            <a href="#">Hướng dẫn thanh toán</a>
        </div>
        <div class="footer-col">
            <h6>Chính sách</h6>
            <a href="#">Quy định chung</a>
            <a href="#">Phân định trách nhiệm</a>
            <a href="#">Chính sách vận chuyển</a>
            <a href="#">Chính sách bảo mật</a>
            <a href="#">Chính sách kiểm hàng</a>
            <a href="#">Chính sách đổi trả</a>
            <a href="#">Chính sách thanh toán</a>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; <?= date("Y") ?> FigureStore - All rights reserved.
    </div>
</footer>

<!-- Register Modal -->
<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Đăng ký</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Hiển thị lỗi nếu có -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Hiển thị thành công -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form action="/QuanLyBanHangFigure/controllers/RegisterController.php" method="POST">
          <h5 class="mb-3 text-primary">Thông tin đăng nhập</h5>
          <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" class="form-control" name="username" required 
                   value="<?= $_SESSION['old_input']['username'] ?? '' ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" class="form-control" name="password" required>
          </div>

          <h5 class="mb-3 mt-4 text-primary">Thông tin cá nhân</h5>
          <div class="mb-3">
            <label class="form-label">Họ và tên</label>
            <input type="text" class="form-control" name="full_name" required 
                   value="<?= $_SESSION['old_input']['full_name'] ?? '' ?>">
          </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" required>
            <div class="d-flex align-items-center mt-2">
                <button type="button" id="sendOtpBtn" class="btn btn-sm btn-primary">Gửi</button>
                <div id="otpCountdown" class="ms-2 text-primary"></div>
            </div>
            <div id="otpStatus" class="mt-1 text-success"></div>
        </div>
        <div class="mb-3">
            <label>Mã xác nhận (OTP):</label>
            <input type="text" name="otp_code" class="form-control">
        </div>
          <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input type="text" class="form-control" name="phone" 
                   value="<?= $_SESSION['old_input']['phone'] ?? '' ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Địa chỉ</label>
            <textarea class="form-control" name="address" rows="3"><?= $_SESSION['old_input']['address'] ?? '' ?></textarea>
          </div>
          <button type="submit" class="btn btn-dark w-100">Đăng ký</button>
          <p class="mt-3 text-center">Đã có tài khoản? 
            <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Đăng nhập</a>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $root ?>/js/index.js"></script>
<!-- Auto show modal if error/success -->
<script>
    function updateCartCount() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let count = cart.reduce((t, item) => t + item.quantity, 0);

    document.getElementById("cart-count").textContent = count;
    }
document.addEventListener("DOMContentLoaded", updateCartCount);

document.addEventListener("DOMContentLoaded", function() {
    <?php if(isset($_SESSION['error'])): ?>
        var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['success'])): ?>
        var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
});

function updateCartCount() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let count = cart.reduce((t, item) => t + item.quantity, 0);

    document.getElementById("cart-count").textContent = count;
}
document.addEventListener("DOMContentLoaded", updateCartCount);
</script>
</body>
</html>
