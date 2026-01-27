# QuanLyBanHang - How to Run Your Project

## ⚡ Quick Start (Easiest Way)

**Double-click this file to start everything:**
- **Windows:** `START_PROJECT.bat`

This will automatically:
1. ✅ Start PHP server on http://127.0.0.1:8000
2. ✅ Start React on http://localhost:3000
3. ✅ Open your browser

---

## 📋 Manual Setup (If needed)

### Step 1: Start PHP Backend Server
Open PowerShell or Command Prompt and run:
```powershell
cd e:\warm64\www
php -S 127.0.0.1:8000
```

You should see:
```
[Tue Jan 27 00:20:51 2026] PHP 8.4.0 Development Server (http://127.0.0.1:8000) started
```

### Step 2: Start React Frontend Server
Open a new PowerShell/Command Prompt window and run:
```powershell
cd e:\warm64\www\QuanLyBanHangFigure\frontend
npm start
```

This will open http://localhost:3000 in your browser automatically.

---

## ✅ Verification Checklist

After both servers start, check:

- [ ] **Frontend loads:** http://localhost:3000 should show your store
- [ ] **Products display:** You should see product cards
- [ ] **Categories show:** Check the navbar category dropdown
- [ ] **Images load:** Product images should display
- [ ] **No console errors:** Press F12 to open browser console (should be clean)

---

## 🔧 Troubleshooting

### Products/Categories not showing?
1. **Check if PHP server is running:**
   - Test: http://127.0.0.1:8000/QuanLyBanHangFigure/api/categories/list.php
   - You should see JSON like: `{"status": "success", "data": [...]}`

2. **If API not working:**
   - Make sure no other app is using port 8000
   - Try a different port: `php -S 127.0.0.1:9000`
   - Update React API config in `.env`

3. **If React won't start:**
   - Make sure Node.js is installed: `node --version`
   - Clear cache: `cd frontend && rm -r node_modules && npm install`
   - Try a different port: `PORT=3001 npm start`

### Images not showing?
- Images should load from: `http://127.0.0.1:8000/QuanLyBanHangFigure/images/`
- Check browser console (F12) for 404 errors
- Verify image files exist in: `e:\warm64\www\QuanLyBanHangFigure\images\`

### Port already in use?
- Kill process using port 8000:
  ```powershell
  Get-Process | Where-Object {$_.Name -eq 'php'} | Stop-Process -Force
  ```

---

## 📁 Project Structure

```
QuanLyBanHangFigure/
├── frontend/              (React app)
│   └── src/
│       ├── pages/        (9 pages: Store, Cart, Login, etc.)
│       └── components/   (Navbar, Footer, ProductCard, etc.)
├── api/                  (30+ API endpoints)
│   ├── auth/
│   ├── products/
│   ├── categories/
│   ├── cart/
│   ├── orders/
│   └── ...
├── config/               (Database config)
├── images/               (Product images)
└── .env                  (Configuration)
```

---

## 🚀 Default Ports

- **Frontend:** http://localhost:3000
- **Backend:** http://127.0.0.1:8000

If you need to change ports:
1. Frontend: Set `PORT=XXXX` environment variable before `npm start`
2. Backend: Run `php -S 127.0.0.1:XXXX`
3. Update React `.env` file with new API URL

---

## 🛑 Stop the Servers

- **Close the terminal windows** where servers are running, or
- **Press Ctrl+C** in each terminal window

---

## 💡 Tips

- **Keep both terminal windows open** while developing
- **Hard refresh** in browser (Ctrl+Shift+R) if changes don't appear
- **Check browser console** (F12) for any errors
- **Database:** MySQL is required (check `.env` file for connection details)

---

**Last Updated:** January 27, 2026
**Version:** 1.0
