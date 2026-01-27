@echo off
REM QuanLyBanHang - Quick Start Script
REM This script starts both the PHP backend and React frontend servers

setlocal enabledelayedexpansion

echo.
echo ========================================
echo  QuanLyBanHang - Project Startup
echo ========================================
echo.

REM Check if we're in the right directory
if not exist "api" (
    echo ERROR: Please run this script from the QuanLyBanHangFigure directory
    echo Current directory: %cd%
    pause
    exit /b 1
)

REM Kill any existing PHP processes
echo Cleaning up old processes...
taskkill /F /IM php.exe >nul 2>&1
timeout /t 1 /nobreak >nul

echo.
echo [1/2] Starting PHP Backend Server...
echo        URL: http://localhost:8000
echo        Directory: %cd%
echo.
start "PHP Development Server" cmd /k "php -S localhost:8000"

REM Wait for PHP to fully start
timeout /t 3 /nobreak >nul

echo [2/2] Starting React Frontend Server...
echo        URL: http://localhost:3000
echo        Directory: %cd%\frontend
echo.

cd frontend
start "React Development Server" cmd /k "npm start"

echo.
echo ========================================
echo  ^✓ Servers are starting!
echo ========================================
echo.
echo Backend:  http://localhost:8000/QuanLyBanHangFigure/api/
echo Frontend: http://localhost:3000
echo.
echo Your browser should open automatically.
echo If not, open http://localhost:3000 manually.
echo.
echo Press Ctrl+C in any window to stop a server.
echo.
timeout /t 5

