<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Gửi email OTP bằng PHPMailer
 * 
 * @param string $email Email người nhận
 * @param string $otp_code Mã OTP
 * @param string $username Tên người dùng
 * @return bool
 */
function sendOTPEmail($email, $otp_code, $username) {
    try {
        if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
            error_log("PHPMailer không được cài đặt. Cần chạy: composer require phpmailer/phpmailer");
            return false;
        }

        require __DIR__ . '/../vendor/autoload.php';

        $mail = new PHPMailer(true);

        // Cấu hình SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'figurestore.noreply@gmail.com'; 
        $mail->Password = 'cwgz zksi dlds xrci'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Thiết lập sender
        $mail->setFrom('noreply@figureshop.vn', 'FigureStore');
        $mail->addAddress($email, $username);

        // Nội dung email
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Xác thực tài khoản FigureStore - Mã OTP';
        
        $mail->Body = "
        <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #333; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; }
                    .otp-code { 
                        background-color: #f0f0f0; 
                        padding: 15px; 
                        text-align: center; 
                        font-size: 24px; 
                        font-weight: bold; 
                        letter-spacing: 5px;
                        margin: 20px 0;
                        border-radius: 5px;
                    }
                    .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
                    .warning { color: #d9534f; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>FigureStore</h1>
                    </div>
                    <div class='content'>
                        <h2>Xin chào $username,</h2>
                        <p>Cảm ơn bạn đã đăng ký tài khoản FigureStore!</p>
                        <p>Để hoàn tất việc đăng ký, vui lòng nhập mã OTP dưới đây:</p>
                        <div class='otp-code'>$otp_code</div>
                        <p><span class='warning'>⚠️ Mã OTP này sẽ hết hạn sau 2 phút.</span></p>
                        <p>Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này.</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; 2026 FigureStore. All rights reserved.</p>
                    </div>
                </div>
            </body>
        </html>
        ";

        $mail->AltBody = "Mã OTP của bạn là: $otp_code. Mã này sẽ hết hạn sau 2 phút.";

        return $mail->send();

    } catch (Exception $e) {
        error_log("PHPMailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Gửi email xác nhận đơn hàng
 */
function sendOrderConfirmationEmail($email, $fullName, $orderId, $total, $finalTotal, $discount, $items, $paymentMethod) {
    try {
        if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
            error_log("PHPMailer không được cài đặt");
            return false;
        }

        require __DIR__ . '/../vendor/autoload.php';

        $mail = new PHPMailer(true);

        // Cấu hình SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'figurestore.noreply@gmail.com';
        $mail->Password = 'cwgz zksi dlds xrci';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Thiết lập sender
        $mail->setFrom('noreply@figureshop.vn', 'FigureStore');
        $mail->addAddress($email, $fullName);

        // Nội dung email
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Xác nhận đơn hàng #' . $orderId;

        // Tạo danh sách sản phẩm
        $itemsHtml = '';
        foreach ($items as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $itemsHtml .= "
            <tr>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>${item['name']}</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>${item['quantity']}</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: right;'>" . number_format($item['price']) . "đ</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: right;'><strong>" . number_format($itemTotal) . "đ</strong></td>
            </tr>";
        }

        $paymentMethodText = ($paymentMethod === 'momo') ? 'Thanh toán bằng Momo' : 'Thanh toán khi nhận hàng (COD)';

        $mail->Body = "
        <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #333; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; }
                    .order-info { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0; }
                    .order-id { font-size: 18px; font-weight: bold; color: #d9534f; }
                    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                    .summary { background-color: #f9f9f9; padding: 15px; text-align: right; }
                    .summary-row { margin: 10px 0; font-size: 14px; }
                    .total { font-size: 18px; font-weight: bold; color: #d9534f; }
                    .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>FigureStore</h1>
                    </div>
                    <div class='content'>
                        <h2>Xin chào $fullName,</h2>
                        <p>Cảm ơn bạn đã đặt hàng tại FigureStore!</p>
                        
                        <div class='order-info'>
                            <p><strong>Mã đơn hàng:</strong> <span class='order-id'>$orderId</span></p>
                            <p><strong>Thời gian đặt:</strong> " . date('d/m/Y H:i:s') . "</p>
                            <p><strong>Phương thức thanh toán:</strong> $paymentMethodText</p>
                        </div>

                        <h3>Chi tiết đơn hàng</h3>
                        <table>
                            <thead style='background-color: #333; color: white;'>
                                <tr>
                                    <th style='padding: 10px; text-align: left;'>Sản phẩm</th>
                                    <th style='padding: 10px; text-align: center;'>Số lượng</th>
                                    <th style='padding: 10px; text-align: right;'>Đơn giá</th>
                                    <th style='padding: 10px; text-align: right;'>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                $itemsHtml
                            </tbody>
                        </table>

                        <div class='summary'>
                            <div class='summary-row'>
                                <span>Tổng tiền:</span>
                                <strong>" . number_format($total) . "đ</strong>
                            </div>";
        
        if ($discount > 0) {
            $mail->Body .= "
                            <div class='summary-row'>
                                <span>Giảm giá:</span>
                                <strong style='color: green;'>-" . number_format($discount) . "đ</strong>
                            </div>";
        }

        $mail->Body .= "
                            <div class='summary-row total'>
                                <span>Thành tiền:</span>
                                <strong>" . number_format($finalTotal) . "đ</strong>
                            </div>
                        </div>

                        <p style='margin-top: 20px; color: #666;'>
                            Đơn hàng của bạn đang được xử lý. Chúng tôi sẽ liên hệ với bạn sớm để xác nhận thông tin giao hàng.
                        </p>
                    </div>
                    <div class='footer'>
                        <p>&copy; 2026 FigureStore. All rights reserved.</p>
                        <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi.</p>
                    </div>
                </div>
            </body>
        </html>
        ";

        $mail->AltBody = "Đơn hàng #$orderId\nTổng tiền: " . number_format($finalTotal) . "đ\nCảm ơn bạn đã mua hàng!";

        return $mail->send();

    } catch (Exception $e) {
        error_log("Order email error: " . $e->getMessage());
        return false;
    }
}
?>

