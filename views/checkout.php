<?php
session_start();
$root ="/QuanLyBanHangFigure";
if(!isset($_SESSION['user_id'])){
    header("Location: $root/index.php");
}

// Lấy thông tin user từ database
include "../config/database.php";
include "../model/User.php";

$userModel = new User($conn);
$user = $userModel->getByAccountId($_SESSION['user_id']);

$fullName = $user['full_name'] ?? '';
$email = $user['email'] ?? '';
$phone = $user['phone'] ?? '';
$address = $user['address'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light"> 
    <div class="container mt-4">
        <h3>Thanh toán</h3>
        <hr>
        <div class ="row">
            <div class="col-md-7">
                <form id="checkout-form">
                    <input type="text" name="fullname" class="form-control mt-2" placeholder="Họ tên" value="<?= htmlspecialchars($fullName) ?>" required>
                    <input type="email" name="email" class="form-control mt-2" placeholder="Email" value="<?= htmlspecialchars($email) ?>" required>
                    <input type="text" name="phone" class="form-control mt-2" placeholder="Số điện thoại" value="<?= htmlspecialchars($phone) ?>" required>
                    <textarea type="text" name="address" class="form-control mt-2" placeholder="Địa chỉ giao hàng" required><?= htmlspecialchars($address) ?></textarea>

                    <h5 class="mt-4">Phương thức thanh toán</h5>
                    <div class="form-check mt-2">
                        <input type="radio" name="payment_method" class="form-check-input" value="cod" checked>
                        <label class="form-check-label">Thanh toán khi nhận hàng (COD)</label>                 
                    </div>

                    <div class="form-check mt-2">
                        <input type="radio" name="payment_method" class="form-check-input" value="momo">
                        <label class="form-check-label">Thanh toán bằng Momo</label>
                    </div>

                    <h5 class="mt-4">Mã voucher (nếu có)</h5>
                    <div class="input-group mt-2">
                        <input type="text" id="voucher-code" name="voucher_code" class="form-control" placeholder="Nhập mã voucher">
                        <button type="button" class="btn btn-outline-secondary" onclick="applyVoucher()">Áp dụng</button>
                    </div>
                    <div id="voucher-message" class="mt-2"></div>

                    <button class="btn btn-primary mt-4 w-100" type="button" onclick="submitCheckout()">Đặt hàng</button>
                </form>
            </div>

            <div class="col-md-5">
                <h5>Giỏ hàng</h5>
                <ul id="cart-list" class="list-group mt-2"></ul>
                <h4 class="mt-3">Tổng tiền: <span id="total" class="text-danger">0đ</span></h4>
                <h4 class="mt-2">Giảm giá: <span id="discount" class="text-success">0đ</span></h4>
                <h4 class="mt-2">Thành tiền: <span id="final-total" class="text-danger">0đ</span></h4>
            </div>
        </div>
    </div>
    <script src="/QuanLyBanHangFigure/js/place_order.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () =>{
            let cart = JSON.parse(localStorage.getItem("cart")) || [];
            let list = document.getElementById("cart-list");
            let total = 0;

            cart.forEach(item => {
                total += item.price * item.quantity;
                list.innerHTML += `<li class="list-group-item d-flex align-items-center">
                    <img src="/QuanLyBanHangFigure/${item.image}" width="50" height="50" class="rounded me-2" alt="${item.name}">
                    <div class="flex-grow-1">
                        <strong>${item.name}</strong>
                        <br>
                        <small class="text-muted">${item.price.toLocaleString()}đ x ${item.quantity}</small>
                    </div>
                    <strong class="text-danger">${(item.price * item.quantity).toLocaleString()}đ</strong>
                </li>`;
            });

            document.getElementById("total").innerText = total.toLocaleString() + "đ";
            document.getElementById("final-total").innerText = total.toLocaleString() + "đ";
        });

        let appliedVoucher = null;

        function applyVoucher() {
            const voucherCode = document.getElementById("voucher-code").value.trim();
            if (!voucherCode) {
                alert("Vui lòng nhập mã voucher");
                return;
            }

            fetch("<?= $root ?>/controllers/VoucherController.php?action=check", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify({code: voucherCode})
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    appliedVoucher = data.voucher;
                    const total = parseInt(document.getElementById("total").innerText);
                    const discount = (total * appliedVoucher.discount_percent) / 100;
                    const finalTotal = total - discount;

                    document.getElementById("discount").innerText = discount.toLocaleString() + "đ";
                    document.getElementById("final-total").innerText = finalTotal.toLocaleString() + "đ";
                    
                    document.getElementById("voucher-message").innerHTML = `<span class="text-success">✓ Áp dụng mã "${voucherCode}" thành công</span>`;
                } else {
                    document.getElementById("voucher-message").innerHTML = `<span class="text-danger">✗ ${data.message}</span>`;
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById("voucher-message").innerHTML = `<span class="text-danger">✗ Lỗi kiểm tra voucher</span>`;
            });
        }

        function submitCheckout(){
            let cart = localStorage.getItem("cart");
            if(!cart || cart.length == 0){
                alert("Giỏ hàng trống");
                return;
            }

            let form = new FormData(document.getElementById("checkout-form"));
            form.append("cart",cart);
            if (appliedVoucher) {
                form.append("voucher_code", appliedVoucher.code);
            }

            fetch("<?= $root ?>/payment/place_order.php", {
                method:"POST",
                body: form
            })
            .then(res => res.json())
            .then(data =>{
                if (data.status === "momo"){
                    window.location.href = data.payment_url;
                }
                else if(data.status === "success"){
                    localStorage.removeItem("cart");
                    window.location.href = "<?= $root ?>/views/success.php?id=" + data.id_order;
                }
                else{
                    alert(data.message || "Lỗi đặt hàng");
                }
            });
        }
    </script>
</body>
</html>