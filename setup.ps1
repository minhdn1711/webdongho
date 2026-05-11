# Script thiết lập tự động dự án webdongho (PowerShell)

# 1. Kiểm tra file .env
if (-not (Test-Path .env)) {
    Write-Host ">>> Đang tạo file .env từ .env.example..." -ForegroundColor Cyan
    Copy-Item .env.example .env
    
    # Cập nhật cấu hình DB để khớp với Docker
    (Get-Content .env) -replace 'DB_CONNECTION=sqlite', 'DB_CONNECTION=mysql' `
                       -replace '# DB_HOST=127.0.0.1', 'DB_HOST=db' `
                       -replace '# DB_PORT=3306', 'DB_PORT=3306' `
                       -replace '# DB_DATABASE=laravel', 'DB_DATABASE=webdongho' `
                       -replace '# DB_USERNAME=root', 'DB_USERNAME=user' `
                       -replace '# DB_PASSWORD=', 'DB_PASSWORD=root' | Set-Content .env
    Write-Host ">>> Đã cấu hình xong .env cho Docker MySQL." -ForegroundColor Green
}

# 2. Khởi động Docker
Write-Host ">>> Đang khởi động các container Docker..." -ForegroundColor Cyan
docker-compose up -d --build

# 3. Đợi database sẵn sàng
Write-Host ">>> Đang chờ Database khởi tạo (15 giây)..." -ForegroundColor Yellow
Start-Sleep -Seconds 15

# 4. Cấp quyền cho các thư mục Laravel (Fix lỗi Permission denied & Vite manifest)
Write-Host ">>> Đang cấp quyền cho các thư mục storage, bootstrap/cache và public..." -ForegroundColor Cyan
docker-compose exec -T app chmod -R 777 storage bootstrap/cache public

# 5. Cài đặt PHP dependencies (Composer)
Write-Host ">>> Đang cài đặt Composer dependencies..." -ForegroundColor Cyan
docker-compose exec -T app composer install

# 6. Tạo Application Key
Write-Host ">>> Đang tạo APP_KEY cho Laravel..." -ForegroundColor Cyan
docker-compose exec -T app php artisan key:generate

# 7. Chạy Migration & Seed dữ liệu
Write-Host ">>> Đang chạy Migration và Seed dữ liệu mẫu..." -ForegroundColor Cyan
docker-compose exec -T app php artisan migrate --seed

# 8. Cài đặt Node dependencies & Build assets
Write-Host ">>> Đang cài đặt NPM dependencies..." -ForegroundColor Cyan
docker-compose exec -T node npm install

Write-Host ">>> Đang build assets (Vite)..." -ForegroundColor Cyan
docker-compose exec -T node npm run build

Write-Host "`n====================================================" -ForegroundColor Green
Write-Host " THIẾT LẬP HOÀN TẤT!" -ForegroundColor Green
Write-Host " - Website: http://localhost:8000" -ForegroundColor White
Write-Host " - phpMyAdmin: http://localhost:8001 (User: root / Pass: root)" -ForegroundColor White
Write-Host "====================================================" -ForegroundColor Green
