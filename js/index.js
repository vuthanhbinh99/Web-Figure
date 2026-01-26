document.addEventListener("DOMContentLoaded", function () {
    const registerForm = document.getElementById("registerForm");
    const registerError = document.getElementById("registerError");

    if (!registerForm || !registerError) return;
    registerForm.addEventListener("submit", async function(e) {
        e.preventDefault();

        registerError.classList.add("d-none");

        const formData = new FormData(registerForm);

        try {
            const res = await fetch("/QuanLyBanHangFigure/controllers/RegisterController.php", {
                method: "POST",
                body: formData
            });

            const data = await res.json();

            if (!data.status) {
                registerError.textContent = data.message;
                registerError.classList.remove("d-none");
                return;
            }

            //  Hiện thông báo thành công
            alert("Đăng ký thành công!");

            //  Redirect về home
            window.location.href = data.redirect;

        } catch (err) {
            registerError.textContent = "Có lỗi xảy ra!";
            registerError.classList.remove("d-none");
        }
    });
});

