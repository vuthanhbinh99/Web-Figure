# Start QuanLyBanHang Project - All services at once

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  QuanLyBanHang Project Startup" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

# Kill any existing processes
Write-Host "Cleaning up old processes..." -ForegroundColor Yellow
Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force
Get-Process node -ErrorAction SilentlyContinue | Stop-Process -Force
Start-Sleep -Seconds 2

# Start PHP Backend Server
Write-Host "[1/2] Starting PHP Backend Server (localhost:8000)..." -ForegroundColor Green
Start-Process -FilePath "powershell" -ArgumentList "-NoExit", "-Command", "cd e:\warm64\www; php -S 127.0.0.1:8000" -WindowStyle Normal
Start-Sleep -Seconds 3

# Start React Frontend Server
Write-Host "[2/2] Starting React Frontend Server (localhost:3000)..." -ForegroundColor Green
Start-Sleep -Seconds 1
cd e:\warm64\www\QuanLyBanHangFigure\frontend
Start-Process -FilePath "npm" -ArgumentList "start" -WindowStyle Normal

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "All services started!" -ForegroundColor Green
Write-Host "`nBackend:  http://localhost:8000" -ForegroundColor White
Write-Host "Frontend: http://localhost:3000" -ForegroundColor White
Write-Host "`nWaiting for servers to initialize..." -ForegroundColor Yellow
Write-Host "========================================`n" -ForegroundColor Cyan

Start-Sleep -Seconds 5
Write-Host "Browser should open automatically. If not, open http://localhost:3000" -ForegroundColor Yellow
