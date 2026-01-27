@echo off
REM Quick Start Guide - Windows batch script

echo ======================================
echo FigureStore - Quick Start
echo ======================================
echo.

REM Check Node.js
echo Checking Node.js...
node --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Node.js not found. Please install from https://nodejs.org
    pause
    exit /b 1
)
for /f "tokens=*" %%i in ('node --version') do set NODE_VERSION=%%i
echo OK: Node.js %NODE_VERSION%
echo.

REM Check npm
echo Checking npm...
npm --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: npm not found. Please install npm
    pause
    exit /b 1
)
for /f "tokens=*" %%i in ('npm --version') do set NPM_VERSION=%%i
echo OK: npm %NPM_VERSION%
echo.

REM Install dependencies
echo Installing dependencies...
cd frontend
call npm install
if errorlevel 1 (
    echo ERROR: Failed to install dependencies
    pause
    exit /b 1
)
echo OK: Dependencies installed
echo.

REM Copy .env file if it doesn't exist
if not exist .env (
    echo Creating .env file...
    copy .env.example .env
    echo OK: .env file created. Please review and update if needed.
)
echo.

REM Start the app
echo Starting React application...
echo The app will open in your browser at http://localhost:3000
echo.
echo Make sure:
echo   1. PHP backend is running
echo   2. MySQL database is running
echo   3. .env file has correct API URL
echo.

call npm start
pause
