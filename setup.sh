#!/bin/bash

# Script thiết lập tự động dự án webdongho (Bash)

# 1. Kiểm tra file .env
if [ ! -f .env ]; then
    echo ">>> Đang tạo file .env từ .env.example..."
    cp .env.example .env
    
    # Cập nhật cấu hình DB để khớp với Docker
    # Sử dụng sed để thay thế (tương thích macOS và Linux)
    sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/g' .env
    sed -i 's/# DB_HOST=127.0.0.1/DB_HOST=db/g' .env
    sed -i 's/# DB_PORT=3306/DB_PORT=3306/g' .env
    sed -i 's/# DB_DATABASE=laravel/DB_DATABASE=webdongho/g' .env
    sed -i 's/# DB_USERNAME=root/DB_USERNAME=user/g' .env
    sed -i 's/# DB_PASSWORD=/DB_PASSWORD=root/g' .env
    echo ">>> Đã cấu hình xong .env cho Docker MySQL."
fi

# 2. Khởi động Docker
echo ">>> Đang khởi động các container Docker..."
docker-compose up -d --build

# 3. Đợi database sẵn sàng
echo ">>> Đang chờ Database khởi tạo (15 giây)..."
sleep 15

# 4. Cấp quyền cho các thư mục Laravel (Fix lỗi Permission denied & Vite manifest)
echo ">>> Đang cấp quyền cho các thư mục storage, bootstrap/cache và public..."
docker-compose exec -T app chmod -R 777 storage bootstrap/cache public

# 5. Cài đặt PHP dependencies (Composer)
echo ">>> Đang cài đặt Composer dependencies..."
docker-compose exec -T app composer install

# 6. Tạo Application Key
echo ">>> Đang tạo APP_KEY cho Laravel..."
docker-compose exec -T app php artisan key:generate

# 7. Chạy Migration & Seed dữ liệu
echo ">>> Đang chạy Migration và Seed dữ liệu mẫu..."
docker-compose exec -T app php artisan migrate --seed

# 8. Cài đặt Node dependencies & Build assets
echo ">>> Đang cài đặt NPM dependencies..."
docker-compose exec -T node npm install

echo ">>> Đang build assets (Vite)..."
docker-compose exec -T node npm run build

echo -e "\n===================================================="
echo " THIẾT LẬP HOÀN TẤT!"
echo " - Website: http://localhost:8000"
echo " - phpMyAdmin: http://localhost:8001 (User: root / Pass: root)"
echo "===================================================="
