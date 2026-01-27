# FigureStore - React Frontend

Đây là frontend React cho ứng dụng quản lý bán hàng.

## Cài đặt

### 1. Cài đặt dependencies
```bash
cd frontend
npm install
```

### 2. Setup environment variables
```bash
cp .env.example .env
```

Cập nhật `.env` với URL backend của bạn:
```
REACT_APP_API_URL=http://localhost:8000/QuanLyBanHangFigure/api
REACT_APP_IMAGE_URL=http://localhost:8000/QuanLyBanHangFigure/images
```

### 3. Chạy development server
```bash
npm start
```

App sẽ mở ở `http://localhost:3000`

## Build cho production
```bash
npm run build
```

## Cấu trúc dự án

```
frontend/
├── public/              # Các file tĩnh
├── src/
│   ├── components/      # React components
│   ├── pages/          # Các trang
│   ├── services/       # API services
│   ├── context/        # React Context
│   ├── css/            # Styles
│   ├── App.js          # Main component
│   └── index.js        # Entry point
├── package.json
└── .env.example        # Environment variables template
```

## Features

- ✅ Đăng nhập / Đăng ký
- ✅ Xem sản phẩm
- ✅ Tìm kiếm sản phẩm
- ✅ Lọc theo danh mục
- ✅ Giỏ hàng
- ✅ Thanh toán
- ✅ Hồ sơ người dùng
- ✅ Đánh giá sản phẩm
- ✅ Admin dashboard

## Technology Stack

- React 18
- React Router v6
- Axios
- Bootstrap 5
- Context API

## API Endpoints

Backend API endpoints được định nghĩa trong `src/services/api.js`

## Lưu ý

- Đảm bảo backend PHP đang chạy trước khi chạy React app
- Kiểm tra CORS settings nếu gặp lỗi API
- Các thuộc tính API phải khớp với backend database schema
