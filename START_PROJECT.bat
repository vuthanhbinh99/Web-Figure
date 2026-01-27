@echo off
REM Start QuanLyBanHang Project - All services at once

echo.
echo ========================================
echo  QuanLyBanHang Project Startup
echo ========================================
echo.

REM Kill any existing processes
echo Cleaning up old processes...
taskkill /F /IM php.exe >nul 2>&1
taskkill /F /IM node.exe >nul 2>&1
timeout /t 2 /nobreak >nul

REM Start PHP Backend Server
echo.
echo [1/2] Starting PHP Backend Server (localhost:8000)...
start "PHP Server" cmd /k "cd e:\warm64\www && php -S 127.0.0.1:8000"
timeout /t 3 /nobreak

REM Start React Frontend Server
echo [2/2] Starting React Frontend Server (localhost:3000)...
echo.
cd e:\warm64\www\QuanLyBanHangFigure\frontend
start "React Server" cmd /k "npm start"

echo.
echo ========================================
echo All services started!
echo.
echo Backend:  http://localhost:8000
echo Frontend: http://localhost:3000
echo.
echo Press Ctrl+C in any window to stop a server.
echo ========================================
echo.
timeout /t 5
