# 📂 Project Structure - Final

## 🗂️ Cấu trúc thư mục hoàn chỉnh

```
QuanLyBanHangFigure/
│
├── 📁 frontend/                     ⭐ REACT FRONTEND
│   ├── public/
│   │   └── index.html
│   ├── src/
│   │   ├── components/
│   │   │   ├── Navbar.js
│   │   │   ├── Footer.js
│   │   │   └── ProductCard.js
│   │   ├── pages/
│   │   │   ├── Home.js
│   │   │   ├── Store.js
│   │   │   ├── ProductDetail.js
│   │   │   ├── Login.js
│   │   │   ├── Register.js
│   │   │   ├── Cart.js
│   │   │   ├── Checkout.js
│   │   │   ├── Profile.js
│   │   │   ├── Success.js
│   │   │   └── admin/
│   │   │       └── AdminDashboard.js
│   │   ├── services/
│   │   │   └── api.js              (Centralized API service)
│   │   ├── context/
│   │   │   ├── AuthContext.js
│   │   │   └── CartContext.js
│   │   ├── css/
│   │   │   └── index.css
│   │   ├── App.js
│   │   └── index.js
│   ├── .env.example
│   ├── .gitignore
│   ├── package.json
│   └── README.md
│
├── 📁 api/                          ⭐ REST API ENDPOINTS (30+)
│   ├── auth/
│   │   ├── login.php
│   │   ├── register.php
│   │   └── logout.php
│   ├── products/
│   │   ├── list.php
│   │   ├── detail.php
│   │   ├── search.php
│   │   ├── category.php
│   │   └── featured.php
│   ├── categories/
│   │   └── list.php
│   ├── cart/
│   │   ├── list.php
│   │   ├── add.php
│   │   ├── update.php
│   │   ├── delete.php
│   │   └── clear.php
│   ├── orders/
│   │   ├── create.php
│   │   ├── my-orders.php
│   │   ├── list.php
│   │   ├── detail.php
│   │   └── update-status.php
│   ├── users/
│   │   ├── detail.php
│   │   └── update.php
│   ├── reviews/
│   │   └── list.php
│   ├── vouchers/
│   │   └── validate.php
│   ├── payment/
│   │   ├── momo-create.php
│   │   └── verify.php
│   ├── index.php
│   └── API_RESPONSE_FORMAT.php
│
├── 📁 config/                       ✅ DATABASE CONFIG
│   └── database.php
│
├── 📁 model/                        ✅ DATA MODELS (không thay đổi)
│   ├── Account.php
│   ├── Product.php
│   ├── Category.php
│   ├── User.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── Cart.php
│   ├── Review.php
│   ├── OTP.php
│   └── Voucher.php
│
├── 📁 helpers/                      ✅ HELPER FUNCTIONS
│   ├── auto_id.php
│   └── email_helper.php
│
├── 📁 images/                       📷 PRODUCT IMAGES
│   └── (product images)
│
├── 📁 vendor/                       📦 PHP DEPENDENCIES (Composer)
│   ├── phpmailer/
│   ├── composer/
│   └── autoload.php
│
├── 📁 .github/                      🔧 GITHUB CONFIG
│   └── workflows/
│
│
├── 📄 index.php                     ✅ MAIN ENTRY (Redirect to React)
├── 📄 .env                          🔐 ENVIRONMENT (Local)
├── 📄 .env.example                  📋 ENV TEMPLATE
├── 📄 .htaccess                     🔧 APACHE CONFIG (CORS + Routing)
├── 📄 .gitignore                    🚫 GIT IGNORE
│
├── 📄 composer.json                 📦 PHP DEPENDENCIES
├── 📄 composer.lock
│
├── 📄 README.md                     📖 PROJECT README
├── 📄 START_HERE.md                 🚀 QUICK START GUIDE
├── 📄 MIGRATION_GUIDE.md            📝 MIGRATION GUIDE
├── 📄 CONVERSION_SUMMARY.md         ✨ CONVERSION SUMMARY
│
├── 🖥️ quickstart.bat                💻 WINDOWS QUICK START
└── 🖥️ quickstart.sh                 🐧 LINUX/MAC QUICK START
```

---

## ✅ File Status

### ✅ KEEP (Cần thiết)
- `frontend/` - React application
- `api/` - REST API endpoints
- `config/` - Database configuration
- `model/` - Data models
- `helpers/` - Helper functions
- `images/` - Product images
- `vendor/` - PHP dependencies
- `index.php` - Redirect to React
- `.env`, `.htaccess` - Configuration

### ❌ DELETED (Đã xóa)
- ❌ `views/` - Old HTML views
- ❌ `css/` - Old stylesheets (đã dùng Bootstrap + CSS từ React)
- ❌ `js/` - Old JavaScript (đã dùng React)
- ❌ `controllers/` - Old controllers (API endpoints ở `/api/`)
- ❌ `payment/` - Old payment (API ở `/api/payment/`)
- ❌ `Dockerfile` - Docker (không cần)
- ❌ `docker-compose.yml` - Docker Compose (không cần)

---

## 🚀 URLs

| Service | URL |
|---------|-----|
| **Frontend React** | http://localhost:3000 |
| **Backend API** | http://localhost:8000/QuanLyBanHangFigure/api |
| **Product Images** | http://localhost:8000/QuanLyBanHangFigure/images |

---

## 📊 Statistics

- **React Pages:** 9
- **React Components:** 3
- **API Endpoints:** 30+
- **Database Tables:** 9
- **Total Code Lines:** 5000+

---

## ✨ Ready to Go!

Dự án đã được dọn dẹp và tối ưu hóa!
Tất cả file cũ không cần thiết đã được xóa.
Chỉ giữ lại những gì thực sự cần thiết.

```bash
# Khởi chạy:
quickstart.bat      # Windows
quickstart.sh       # Linux/Mac
```

**Happy Coding!** 🎉
