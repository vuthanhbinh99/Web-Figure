document.addEventListener("DOMContentLoaded", () => {
    const orderBtn = document.getElementById("btnPlaceOrder");
    const formContainer = document.getElementById("checkoutFormContainer");
    const submitBtn = document.getElementById("btnSubmitOrder");

    if (!orderBtn || !submitBtn) return;

    // Hiện form khi bấm "Đặt hàng"
    orderBtn.addEventListener("click", () => {
        formContainer.style.display = "block";
        orderBtn.style.display = "none";
        window.scrollTo({ top: formContainer.offsetTop - 20, behavior: 'smooth' });
    });

    // Xác nhận đặt hàng
    submitBtn.addEventListener("click", async () => {
        const cart = JSON.parse(localStorage.getItem("cart")) || [];
        if (cart.length === 0) return alert("Giỏ hàng đang trống!");

        const fullName = document.querySelector("#checkoutForm [name='full_name']").value.trim();
        const phone = document.querySelector("#checkoutForm [name='phone']").value.trim();
        const address = document.querySelector("#checkoutForm [name='address']").value.trim();
        const payment = document.querySelector("#checkoutForm [name='payment_method']").value;
        const email = document.querySelector("#checkoutForm [name='email']").value.trim();


        if (!fullName || !phone || !address) return alert("Vui lòng nhập đầy đủ thông tin giao hàng!");

        try {
            const res = await fetch("/QuanLyBanHangFigure/payment/place_order.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ cart, full_name: fullName, phone, address, payment_method: payment })
            });

            const data = await res.json();
            if (!res.ok) return alert(data.message || "Lỗi server");

            if (data.status === "momo") {
                // Redirect to Momo payment
                window.location.href = data.payment_url;
            } else if (data.status === "vnpay") {
                // Redirect to VNPay payment
                window.location.href = data.payment_url;
            } else if (data.status === "success") {
                localStorage.removeItem("cart");
                window.location.href = "/QuanLyBanHangFigure/views/success.php?id=" + data.id_order;
            } else {
                alert(data.message || "Đặt hàng thất bại");
            }

        } catch (err) {
            console.error(err);
            alert("Lỗi kết nối server!");
        }
    });
});
