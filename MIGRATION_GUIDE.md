# Hướng dẫn chuyển đổi từ HTML sang React

## 📋 Tổng quan

Project đã được chuyển đổi từ HTML frontend sang **ReactJS** với backend PHP không thay đổi. Tất cả tên thuộc tính trong API vẫn giữ nguyên từ database.

## 🚀 Cách cài đặt

### Phần 1: Setup Backend (PHP)

Backend hiện tại vẫn chạy bình thường, chỉ cần chắc chắn:

1. **Database đã cấu hình**
   - MySQL 8.0 đang chạy
   - Database `qlmh` đã được tạo
   - Import schema từ `database.sql`

2. **PHP 8.1+ đang chạy**
   ```bash
   php --version
   ```

3. **Apache mod_rewrite đã bật**
   ```bash
   # Trên Linux
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   
   # Trên Windows XAMPP
   # Mở XAMPP Control Panel -> Enable Apache
   ```

4. **Cấu hình `.env`**
   ```bash
   cp .env.example .env
   # Cập nhật các thông tin database
   ```

### Phần 2: Setup Frontend (React)

1. **Cài đặt Node.js**
   - Download từ https://nodejs.org (v16+)
   - Kiểm tra: `node --version` và `npm --version`

2. **Cài đặt dependencies**
   ```bash
   cd frontend
   npm install
   ```

3. **Cấu hình environment**
   ```bash
   cp .env.example .env
   ```
   
   Mặc định:
   ```
   REACT_APP_API_URL=http://localhost:8000/QuanLyBanHangFigure/api
   REACT_APP_IMAGE_URL=http://localhost:8000/QuanLyBanHangFigure/images
   ```

4. **Chạy development server**
   ```bash
   npm start
   ```
   
   App sẽ tự động mở ở `http://localhost:3000`

## 📁 Cấu trúc thư mục

```
QuanLyBanHangFigure/
├── api/                          # NEW: API endpoints cho React
│   ├── auth/                     # Xác thực
│   │   ├── login.php
│   │   ├── register.php
│   │   └── logout.php
│   ├── products/                 # Sản phẩm
│   ├── categories/               # Danh mục
│   ├── cart/                     # Giỏ hàng
│   ├── orders/                   # Đơn hàng
│   ├── users/                    # Người dùng
│   ├── reviews/                  # Đánh giá
│   ├── vouchers/                 # Vouchers
│   └── payment/                  # Thanh toán
│
├── frontend/                     # NEW: React application
│   ├── public/
│   │   └── index.html
│   ├── src/
│   │   ├── components/           # React components
│   │   │   ├── Navbar.js
│   │   │   ├── Footer.js
│   │   │   └── ProductCard.js
│   │   ├── pages/                # Pages
│   │   │   ├── Home.js
│   │   │   ├── Store.js
│   │   │   ├── ProductDetail.js
│   │   │   ├── Login.js
│   │   │   ├── Register.js
│   │   │   ├── Cart.js
│   │   │   ├── Checkout.js
│   │   │   ├── Profile.js
│   │   │   └── Success.js
│   │   ├── services/             # API services
│   │   │   └── api.js
│   │   ├── context/              # React Context
│   │   │   ├── AuthContext.js
│   │   │   └── CartContext.js
│   │   ├── css/                  # Styles
│   │   │   └── index.css
│   │   ├── App.js
│   │   └── index.js
│   ├── package.json
│   ├── .env.example
│   └── README.md
│
├── config/                       # Database config
│   └── database.php
├── model/                        # Data models (không thay đổi)
├── controllers/                  # Controllers (optional)
├── views/                        # Old HTML views (deprecated)
└── ...
```

## 🔌 API Endpoints

Tất cả API endpoints được định nghĩa trong `frontend/src/services/api.js`

### Authentication
- `POST /api/auth/login.php` - Đăng nhập
- `POST /api/auth/register.php` - Đăng ký
- `POST /api/auth/logout.php` - Đăng xuất

### Products
- `GET /api/products/list.php?page=1&limit=20` - Danh sách sản phẩm
- `GET /api/products/detail.php?id_products=XXX` - Chi tiết sản phẩm
- `GET /api/products/search.php?q=XXX&page=1&limit=20` - Tìm kiếm
- `GET /api/products/category.php?category_slug=XXX&page=1&limit=20` - Theo danh mục
- `GET /api/products/featured.php` - Sản phẩm nổi bật

### Categories
- `GET /api/categories/list.php` - Danh sách danh mục

### Orders
- `POST /api/orders/create.php` - Tạo đơn hàng
- `GET /api/orders/my-orders.php` - Đơn hàng của tôi
- `GET /api/orders/detail.php?id_order=XXX` - Chi tiết đơn hàng

### Users
- `GET /api/users/detail.php?id_users=XXX` - Thông tin người dùng
- `PUT /api/users/update.php?id_users=XXX` - Cập nhật người dùng

### Reviews
- `GET /api/reviews/list.php?id_products=XXX` - Đánh giá sản phẩm
- `POST /api/reviews/list.php` - Tạo đánh giá

### Vouchers
- `GET /api/vouchers/validate.php?code=XXX` - Xác thực voucher

## 💾 Database Schema

Tất cả tên column trong database được giữ nguyên:
- `products`: id_products, name, price, description, image, stock, id_categories
- `categories`: id_categories, name, slug
- `accounts`: id_accounts, username, password, role, email
- `users`: id_users, full_name, email, phone, address, id_accounts
- `orders`: id_order, id_accounts, total_amount, status, created_at
- `order_items`: id_order_items, id_order, id_products, quantity, price
- `reviews`: id_reviews, id_products, id_users, rating, comment, created_at
- `cart`: id_carts, id_users, id_products, quantity

## 🧪 Testing

### Test Backend API
```bash
# Test login
curl -X POST http://localhost:8000/QuanLyBanHangFigure/api/auth/login.php \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"123456"}'

# Test products list
curl http://localhost:8000/QuanLyBanHangFigure/api/products/list.php
```

### Test React Frontend
```bash
# Chạy dev server
npm start

# Build production
npm run build
```

## 🔧 Troubleshooting

### CORS Error
Nếu gặp lỗi CORS, kiểm tra:
1. `.htaccess` đã cấu hình đúng
2. Apache `mod_headers` đã bật
3. Environment variables trong `.env` đúng

### 404 API
- Kiểm tra `.htaccess` và mod_rewrite
- Đảm bảo URL API đúng trong `api.js`
- Kiểm tra file PHP tồn tại

### Session Issues
- Clear cookies và localStorage
- Kiểm tra `session_start()` trong PHP files
- Đảm bảo session save path đúng

## 📝 Thay đổi chính

1. **Cấu trúc Frontend**
   - Old: HTML + Vanilla JS
   - New: React + JSX

2. **State Management**
   - Old: localStorage + session
   - New: React Context API

3. **Routing**
   - Old: Multiple PHP files
   - New: React Router v6

4. **API Calls**
   - Old: Fetch API
   - New: Axios with centralized service

5. **Styling**
   - Old: Bootstrap CSS files
   - New: Bootstrap via npm + custom CSS

## 🚢 Production Deployment

### Build React App
```bash
cd frontend
npm run build
```

### Copy build folder
```bash
# Copy frontend/build/* đến public folder của web server
cp -r frontend/build/* /var/www/html/public
```

### Configure Apache
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/html
    
    <Directory /var/www/html>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## 📞 Support

Nếu gặp vấn đề:
1. Kiểm tra console browser (F12)
2. Kiểm tra backend logs
3. Xem file `.env` cấu hình đúng chưa
4. Ensure PHP 8.1+ và MySQL 8.0+

## ✅ Checklist

- [ ] Node.js cài đặt
- [ ] PHP 8.1+ cài đặt
- [ ] MySQL 8.0 chạy
- [ ] Database imported
- [ ] `.env` file cấu hình
- [ ] `npm install` chạy xong
- [ ] `npm start` khởi chạy thành công
- [ ] Backend API accessible
- [ ] Frontend load không lỗi

## 🎉 Kết thúc

Dự án đã sẵn sàng phát triển!

React frontend kết nối với PHP backend thông qua REST API.
Tất cả tên thuộc tính API khớp với database schema.
