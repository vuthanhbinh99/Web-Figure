# 🛍️ FigureStore - E-commerce Platform

Modern e-commerce platform với **React Frontend** + **PHP Backend**, thanh toán Momo, và quản lý sản phẩm đầy đủ.

## 🚀 Tech Stack

- **Frontend**: React 18 + React Router + Bootstrap 5
- **Backend**: PHP 8.1
- **Database**: MySQL 8.0
- **Server**: Apache 2
- **Payment**: Momo Gateway (Production)
- **API**: REST API (30+ endpoints)

## ⚙️ Yêu Cầu

### Local Development
- **Node.js** v16+ (cho React)
- **PHP 8.1+** (cho Backend)
- **MySQL 8.0** (cho Database)
- **Composer** (cho PHP dependencies)
- **Apache2** với mod_rewrite

## ⚡ Quick Start

### Windows:
```bash
cd QuanLyBanHangFigure
quickstart.bat
```

### Linux/Mac:
```bash
cd QuanLyBanHangFigure
bash quickstart.sh
```

### Manual Setup:
```bash
# 1. Install dependencies
cd frontend
npm install

# 2. Setup environment
cp .env.example .env

# 3. Start React app
npm start
```

Ứng dụng sẽ mở ở `http://localhost:3000`

## 📋 Project Structure

```
QuanLyBanHangFigure/
├── frontend/              # React Frontend
│   ├── src/
│   │   ├── pages/        # 9 pages
│   │   ├── components/   # Reusable components
│   │   ├── services/     # API service
│   │   └── context/      # Auth & Cart state
│   └── package.json
├── api/                   # REST API Endpoints (30+)
│   ├── auth/             # Authentication
│   ├── products/         # Products
│   ├── categories/       # Categories
│   ├── cart/             # Shopping Cart
│   ├── orders/           # Orders
│   ├── users/            # User Profiles
│   ├── reviews/          # Reviews
│   └── vouchers/         # Vouchers
├── config/               # Database configuration
├── model/                # Data models
├── helpers/              # Helper functions
└── images/               # Product images
```

## 🔗 URLs

| Service | URL |
|---------|-----|
| Frontend | http://localhost:3000 |
| Backend API | http://localhost:8000/QuanLyBanHangFigure/api |
| Images | http://localhost:8000/QuanLyBanHangFigure/images |

## 🔐 Database Configuration

Update `.env` file:

| Variable | Mô tả | Ví dụ |
|----------|-------|-------|
| `DB_HOST` | MySQL host | localhost |
| `DB_USER` | MySQL username | root |
| `DB_PASSWORD` | MySQL password | your_password |
| `DB_NAME` | Database name | quan_ly_ban_hang |
| `MOMO_PARTNER_CODE` | Momo Partner Code | MOMOKMD220250203 |
| `MOMO_ACCESS_KEY` | Momo Access Key | your_access_key |
| `MOMO_SECRET_KEY` | Momo Secret Key | your_secret_key |
| `MOMO_ENDPOINT` | Momo API Endpoint | https://payment.momo.vn/v2/gateway/api/create |
| `APP_URL` | Application base URL | http://yourdomain.com |

## Security

⚠️ **IMPORTANT**: 
- **NEVER** commit `.env` file to GitHub
- `.env` là private, chỉ dùng cho local/server
- `.env.example` là template cho documentation
- Production environment variables nên được set qua:
  - Docker Compose environment
  - Server environment variables
  - CI/CD secrets

## Deployment

### Docker Production
```bash
docker build -t quan-ly-ban-hang:latest .
docker run -d \
  -e DB_HOST=mysql.example.com \
  -e DB_USER=produser \
  -e DB_PASSWORD=prodpassword \
  -e MOMO_PARTNER_CODE=your_code \
  -e MOMO_ACCESS_KEY=your_key \
  -e MOMO_SECRET_KEY=your_secret \
  -e APP_URL=https://yourdomain.com \
  -p 80:80 \
  quan-ly-ban-hang:latest
```

### Traditional Server
1. Upload files
2. Copy `.env.example` → `.env`
3. Update `.env` với production credentials
4. Set proper permissions: `chmod -R 755`
5. Configure Apache virtual host
6. Run: `php -r "require 'index.php';"`

## Features

✅ User Authentication & OTP Email Verification
✅ Product Catalog Management
✅ Shopping Cart (localStorage)
✅ Voucher/Discount System
✅ Momo Payment Gateway Integration
✅ COD (Cash on Delivery) Payment
✅ Order Tracking
✅ Email Confirmations
✅ Admin Dashboard
✅ Product Import (Excel)

## Database Schema

See `database.sql` for complete schema

## API Endpoints

### Payment
- `POST /payment/place_order.php` - Create order & process payment
- `GET /payment/momo_return.php` - Momo redirect callback
- `POST /payment/momo_notify.php` - Momo IPN callback

### Authentication
- `POST /controllers/LoginController.php` - User login
- `POST /controllers/RegisterController.php` - User registration
- `POST /controllers/OTPController.php` - OTP verification

## Troubleshooting

### 1. "Bad format request" from Momo
- Verify Momo credentials in `.env`
- Check signature calculation matches Momo requirements
- Ensure `ipnUrl` and `redirectUrl` are publicly accessible

### 2. Database connection failed
- Verify `DB_HOST`, `DB_USER`, `DB_PASSWORD` in `.env`
- Ensure MySQL server is running
- Check `.env` file exists at project root

### 3. Docker connection issues
```bash
# Check containers
docker ps

# Check logs
docker logs quan_ly_ban_hang_app
docker logs quan_ly_ban_hang_mysql

# Restart
docker-compose restart
```

## Support

Issues? Check:
1. `.env` configuration
2. Database connection
3. Docker logs
4. Apache error logs

## License

Private project

## Author

Your Name
