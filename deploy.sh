#!/bin/bash

# Script cập nhật dự án trên VPS - webdongho

echo ">>> Đang cập nhật mã nguồn từ GitHub..."
git pull origin main

echo ">>> Dừng containers (giữ MySQL chạy)..."
docker compose -f docker-compose.prod.yml stop app web queue scheduler

echo ">>> Đang build và khởi động lại các container..."
docker compose -f docker-compose.prod.yml up -d --build app web queue scheduler

echo ">>> Đang dọn dẹp các image cũ..."
docker image prune -f

echo ">>> Chờ MySQL sẵn sàng..."
until docker compose -f docker-compose.prod.yml exec -T db mysqladmin ping -uroot -proot --silent 2>/dev/null; do
    echo "MySQL chưa sẵn sàng, đợi thêm..."
    sleep 3
done
echo "MySQL đã sẵn sàng!"

docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force

echo ">>> Đang tối ưu hóa Laravel..."
docker compose -f docker-compose.prod.yml exec -T app php artisan optimize
docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache

echo ">>> Đang build lại Assets (Vite)..."
docker run --rm -v $(pwd):/var/www -w /var/www node:20-alpine sh -c "npm install && npm run build"

echo ">>> CẬP NHẬT HOÀN TẤT!"
