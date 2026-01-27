# 🚀 Hướng dẫn Khởi động Project QuanLyBanHang

## ⚡ Cách nhanh nhất (Khuyến nghị)

### Option 1: Windows Batch (Dễ nhất)
1. Mở File Explorer
2. Đi đến: `E:\warm64\www\QuanLyBanHangFigure`
3. **Double-click** `START_PROJECT.bat`
4. Chờ 10-15 giây
5. **Tự động mở** http://localhost:3000

### Option 2: PowerShell
1. Mở PowerShell
2. Chạy lệnh:
```powershell
cd E:\warm64\www\QuanLyBanHangFigure
Set-ExecutionPolicy -ExecutionPolicy Bypass -Scope Process -Force
.\START_PROJECT.ps1
```
3. Chờ servers khởi động
4. Mở http://localhost:3000

---

## 🔧 Cách khởi động từng services riêng (Nếu cần)

### Backend (PHP Server)
```bash
cd E:\warm64\www
php -S 127.0.0.1:8000
```
- Truy cập: http://localhost:8000/QuanLyBanHangFigure/api/categories/list.php

### Frontend (React Server)
```bash
cd E:\warm64\www\QuanLyBanHangFigure\frontend
npm start
```
- Tự động mở: http://localhost:3000

---

## 📋 Checklist trước khi chạy

- ✅ Database MySQL đang chạy (XAMPP/WAMP)
- ✅ Port 8000 trống (PHP)
- ✅ Port 3000 trống (React)
- ✅ Node.js/npm đã cài đặt
- ✅ PHP 8.1+ đã cài đặt

---

## 🛑 Dừng Project

1. **Đóng cửa sổ** PHP Server
2. **Đóng cửa sổ** React Server
3. Hoặc nhấn `Ctrl+C` trong terminal

---

## 🐛 Troubleshooting

**Nếu không thấy sản phẩm/danh mục:**
- ✅ Kiểm tra PHP server đang chạy (cửa sổ terminal PHP phải hiển thị)
- ✅ Kiểm tra React dev server đang chạy (terminal Node phải hiển thị)
- ✅ Refresh trình duyệt (F5)
- ✅ Kiểm tra browser console (F12) có lỗi không

**Nếu error "Port already in use":**
```powershell
# Giết processes sử dụng port
Get-Process php, node -ErrorAction SilentlyContinue | Stop-Process -Force
```

**Nếu database connection failed:**
- Chắc chắn MySQL đang chạy
- Kiểm tra .env file có đúng credentials không

---

## 📊 Cấu trúc Backend API

```
Frontend (React - localhost:3000)
         ↓
Browser HTTP Requests
         ↓
Backend (PHP - localhost:8000)
         ↓
Database (MySQL)
         ↓
Returns JSON Response ✅
```

---

Mỗi lần muốn chạy project, **chỉ cần double-click `START_PROJECT.bat`** là xong! ⚡
