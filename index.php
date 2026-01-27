<?php
/**
 * Main Entry Point - Redirects to React Frontend
 * 
 * Old HTML views are deprecated. Use React frontend instead.
 * React app runs on http://localhost:3000
 */

// If it's an API request or test file, let it pass through
if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false || 
    strpos($_SERVER['REQUEST_URI'], 'test_db.php') !== false) {
    // API requests are handled by /api/ folder
    return false;
}

// For all other requests, redirect to React app
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$react_url = $protocol . '://' . str_replace(':8000', ':3000', $host);

// Redirect to React
header("Location: $react_url");
exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= $root ?>/css/index.css">
    <title>Trang Chủ</title>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container d-flex align-items-center">
        <a class="navbar-brand fw-bold" href="index.php">FigureStore</a>

        <form class="form-search d-none d-lg-flex" role="search" action="<?= $root ?>/views/Store.php" method="get">
            <input class="form-control me-2" type="search" placeholder="Tìm kiếm sản phẩm..." name="q">
            <button class="btn btn-outline-light" type="submit">Tìm</button>
        </form>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link active" href="index.php">Trang chủ</a></li>
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
                        <li><a class="dropdown-item" href="<?= $root?>/views/profile.php">Chỉnh sửa thông tin</a></li>
                        <li><a class="dropdown-item text-danger" href="<?= $root ?>/views/logout.php">Đăng xuất</a></li>
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

<!-- Slider Banner -->
<div class="container mt-5" id="categories">
    <div id="categorySlider" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            $banners = [
                ['img' => "$root/images/banner.jpg" ],
                ['img' => "$root/images/banner1.jpg"],
                ['img' => "$root/images/banner2.jpg"]
            ];
            foreach ($banners as $i => $b): ?>
                <div class="carousel-item <?= $i == 0 ? 'active' : '' ?>">
                    <img src="<?= $b['img'] ?>" class="d-block w-100 banner-img">
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#categorySlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#categorySlider" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
        <div class="carousel-indicators mt-3">
            <?php foreach (array_keys($banners) as $i): ?>
                <button type="button" data-bs-target="#categorySlider" data-bs-slide-to="<?= $i ?>" class="<?= $i == 0 ? 'active' : '' ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Featured Products -->
<div class="container mt-5">
    <h3 class="mb-4 fw-bold">Sản phẩm nổi bật</h3>
    <div class="row">
        <?php if (!empty($featuredProducts)): ?>
            <?php foreach ($featuredProducts as $p): ?>
            <div class="col-md-3 mb-4">
                <div class="card product-card shadow-sm">
                    <img src="<?= $root ?>/<?= $p['image'] ?>" class="card-img-top" alt="<?= $p['name'] ?>">
                    <div class="card-body">
                        <h6 class="card-title"><?= $p['name'] ?></h6>
                        <p class="text-danger fw-bold"><?= number_format($p['price'],0,',','.') ?>đ</p>
                        <a href="<?= $root ?>/views/product_detail.php?id=<?= $p['id_products'] ?>" class="btn btn-sm btn-dark w-100">Xem chi tiết</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">Hiện chưa có sản phẩm nổi bật.</p>
        <?php endif; ?>
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

        <form id="registerForm" method="POST">
            <h5 class="mb-3 text-primary">Thông tin đăng nhập</h5>

            <div class="mb-3">
                <label class="form-label">Tên đăng nhập</label>
                <input type="text" class="form-control" name="username" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" name="password" required>
            </div>

            <h5 class="mt-4 mb-3 text-primary">Thông tin cá nhân</h5>

            <div class="mb-3">
                <label class="form-label">Họ và tên</label>
                <input type="text" class="form-control" name="full_name" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Số điện thoại</label>
                <input type="number" class="form-control" name="phone">
            </div>

            <div class="mb-3">
                <label class="form-label">Địa chỉ</label>
                <textarea class="form-control" name="address" rows="3"></textarea>
            </div>

            <div id="registerError" class="alert alert-danger d-none"></div>

            <button type="submit" class="btn btn-dark w-100">Đăng ký</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- <script src="./js/index.js"></script> -->
<!-- Auto show modal if error/success -->
<script>
 document.addEventListener("DOMContentLoaded", function () {
    const registerForm = document.getElementById("registerForm");
    const registerError = document.getElementById("registerError");

    if (registerForm && registerError) {
        registerForm.addEventListener("submit", async function(e) {
            e.preventDefault(); //  Ngăn form submit mặc định

            registerError.classList.add("d-none");

            const formData = new FormData(registerForm);

            try {
                //  Gửi request với fetch
                const res = await fetch("./controllers/RegisterController.php", {
                    method: "POST",
                    body: formData
                });

                const data = await res.json();

                if (!data.status) {
                    registerError.textContent = data.message;
                    registerError.classList.remove("d-none");
                    return;
                }

                // Đăng ký thành công - Hiển thị form OTP
                // Ẩn form đăng ký
                document.getElementById("registerForm").style.display = "none";
                
                // Hiển thị form OTP
                showOTPVerificationForm(data.pending_email);
                
            } catch (err) {
                console.error("Error:", err);
                registerError.textContent = "Có lỗi xảy ra! Vui lòng thử lại.";
                registerError.classList.remove("d-none");
            }
        });
    }
});

// Hàm hiển thị form OTP
function showOTPVerificationForm(email) {
    const registerModalBody = document.querySelector('#registerModal .modal-body');
    const registerError = document.getElementById("registerError");
    
    registerError.classList.add("d-none");
    
    registerModalBody.innerHTML = `
        <div class="text-center mb-4">
            <h5>Xác thực Email</h5>
            <p class="text-muted">Mã OTP đã được gửi tới <strong>${email}</strong></p>
        </div>
        
        <div id="otpError" class="alert alert-danger d-none"></div>
        
        <form id="otpForm">
            <div class="mb-3">
                <label class="form-label">Mã OTP (6 chữ số)</label>
                <input type="text" class="form-control text-center" id="otpCode" name="otp_code" 
                       placeholder="000000" maxlength="6" required pattern="[0-9]{6}">
                <small class="text-muted d-block mt-2">
                    ⏱️ Mã OTP sẽ hết hạn sau <span id="countdown">120</span> giây
                </small>
            </div>
            
            <button type="submit" class="btn btn-dark w-100">Xác thực</button>
            
            <div class="mt-3 text-center">
                <p class="text-muted">Không nhận được mã?</p>
                <button type="button" class="btn btn-link p-0" id="resendBtn">Gửi lại mã OTP</button>
            </div>
        </form>
        
        <input type="hidden" id="pendingEmail" value="${email}">
    `;
    
    // Bắt đầu countdown
    startCountdown();
    
    // Handler form OTP
    document.getElementById("otpForm").addEventListener("submit", handleOTPSubmit);
    document.getElementById("resendBtn").addEventListener("click", handleResendOTP);
}

// Hàm xử lý submit OTP
async function handleOTPSubmit(e) {
    e.preventDefault();
    
    const otpCode = document.getElementById("otpCode").value;
    const email = document.getElementById("pendingEmail").value;
    const otpError = document.getElementById("otpError");
    
    if (!otpCode || otpCode.length !== 6) {
        otpError.textContent = "Vui lòng nhập mã OTP 6 chữ số";
        otpError.classList.remove("d-none");
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append("action", "verify");
        formData.append("email", email);
        formData.append("otp_code", otpCode);
        
        const res = await fetch("/QuanLyBanHangFigure/controllers/OTPController.php", {
            method: "POST",
            body: formData
        });
        
        const data = await res.json();
        
        if (!data.status) {
            otpError.textContent = data.message;
            otpError.classList.remove("d-none");
            return;
        }
        
        // Xác thực thành công - Đóng modal và reload
        alert("Xác thực email thành công! Chào mừng bạn!");
        const modal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
        if (modal) {
            modal.hide();
        }
        
        setTimeout(() => {
            window.location.href = data.redirect || "/QuanLyBanHangFigure/index.php";
        }, 500);
        
    } catch (err) {
        console.error("Error:", err);
        otpError.textContent = "Có lỗi xảy ra! Vui lòng thử lại.";
        otpError.classList.remove("d-none");
    }
}

// Hàm gửi lại OTP
async function handleResendOTP(e) {
    e.preventDefault();
    
    const email = document.getElementById("pendingEmail").value;
    const resendBtn = document.getElementById("resendBtn");
    const otpError = document.getElementById("otpError");
    
    resendBtn.disabled = true;
    resendBtn.textContent = "Đang gửi...";
    
    try {
        const formData = new FormData();
        formData.append("action", "resend");
        formData.append("email", email);
        
        const res = await fetch("/QuanLyBanHangFigure/controllers/OTPController.php", {
            method: "POST",
            body: formData
        });
        
        const data = await res.json();
        
        if (data.status) {
            otpError.classList.add("d-none");
            alert(data.message);
            
            // Reset countdown
            startCountdown();
            
            // Reset form
            document.getElementById("otpCode").value = "";
        } else {
            otpError.textContent = data.message;
            otpError.classList.remove("d-none");
        }
        
    } catch (err) {
        console.error("Error:", err);
        otpError.textContent = "Lỗi gửi lại mã OTP!";
        otpError.classList.remove("d-none");
    } finally {
        resendBtn.disabled = false;
        resendBtn.textContent = "Gửi lại mã OTP";
    }
}

// Hàm countdown
let countdownInterval;
function startCountdown() {
    let seconds = 120;
    const countdownElement = document.getElementById("countdown");
    
    if (countdownInterval) clearInterval(countdownInterval);
    
    countdownInterval = setInterval(() => {
        seconds--;
        if (countdownElement) {
            countdownElement.textContent = seconds;
        }
        
        if (seconds <= 0) {
            clearInterval(countdownInterval);
        }
    }, 1000);
}

document.getElementById("loginForm").addEventListener("submit", async function(e) {
    e.preventDefault();

    let formData = new FormData(this);
    let loginError = document.getElementById("loginError");

    try {
        const res = await fetch("/QuanLyBanHangFigure/controllers/LoginController.php", {
            method: "POST",
            body: formData
        });

        const data = await res.json();

        if (data.status === 'error') {
            // Lỗi: Hiển thị thông báo nhưng không reload
            loginError.textContent = data.message;
            loginError.classList.remove("d-none");
            return;
        }

        // Thành công: Reload trang sau một chút để modal kịp đóng
        if (data.status === 'success') {
            loginError.classList.add("d-none");
            setTimeout(() => {
                window.location.href = data.redirect || "/QuanLyBanHangFigure/index.php";
            }, 300);
        }

    } catch (err) {
        console.error(err);
        loginError.textContent = "Có lỗi xảy ra! Vui lòng thử lại.";
        loginError.classList.remove("d-none");
    }
});

// Cập nhật số lượng giỏ hàng
function updateCartCount() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let count = cart.reduce((t, item) => t + item.quantity, 0);
    
    const cartCountElement = document.getElementById("cart-count");
    if (cartCountElement) {
        cartCountElement.textContent = count;
    }
}

document.addEventListener("DOMContentLoaded", updateCartCount);
</script>

</body>
</html>
