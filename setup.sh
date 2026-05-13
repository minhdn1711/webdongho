#!/bin/bash

# Script thiết lập tự động dự án webdongho (Bash) - Hỗ trợ VPS & Local

# Kiểm tra tham số môi trường
IS_PROD=false
COMPOSE_FILE="docker-compose.yml"

for arg in "$@"; do
    if [ "$arg" == "--prod" ]; then
        IS_PROD=true
        COMPOSE_FILE="docker-compose.prod.yml"
    fi
done

echo ">>> Bắt đầu thiết lập hệ thống (Môi trường: $([ "$IS_PROD" = true ] && echo "Production" || echo "Local"))..."

# 0. Kiểm tra và cài đặt Docker (Dành cho Ubuntu/Debian)
if ! [ -x "$(command -v docker)" ]; then
    echo ">>> Docker chưa được cài đặt. Đang tiến hành cài đặt Docker..."
    sudo apt-get update
    sudo apt-get install -y apt-transport-https ca-certificates curl software-properties-common
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
    sudo apt-get update
    sudo apt-get install -y docker-ce docker-ce-cli containerd.io
    sudo usermod -aG docker $USER
    echo ">>> Đã cài đặt Docker. Vui lòng logout và login lại nếu gặp lỗi quyền truy cập."
fi

if ! [ -x "$(command -v docker-compose)" ]; then
    echo ">>> Docker Compose chưa được cài đặt. Đang tiến hành cài đặt..."
    sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
    echo ">>> Đã cài đặt Docker Compose."
fi

# 1. Kiểm tra file .env
if [ ! -f .env ]; then
    echo ">>> Đang tạo file .env từ .env.example..."
    cp .env.example .env
    
    # Cập nhật cấu hình DB để khớp với Docker
    sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/g' .env
    sed -i 's/# DB_HOST=127.0.0.1/DB_HOST=db/g' .env
    sed -i 's/# DB_PORT=3306/DB_PORT=3306/g' .env
    sed -i 's/# DB_DATABASE=laravel/DB_DATABASE=webdongho/g' .env
    sed -i 's/# DB_USERNAME=root/DB_USERNAME=user/g' .env
    sed -i 's/# DB_PASSWORD=/DB_PASSWORD=root/g' .env
    echo ">>> Đã cấu hình xong .env cho Docker MySQL."
fi

# 2. Khởi động Docker
echo ">>> Đang khởi động các container Docker với file $COMPOSE_FILE..."
docker-compose -f $COMPOSE_FILE up -d --build

# 3. Đợi database sẵn sàng
echo ">>> Đang chờ Database khởi tạo (15 giây)..."
sleep 15

# 4. Cấp quyền cho các thư mục Laravel
echo ">>> Đang cấp quyền cho các thư mục storage, bootstrap/cache và public..."
docker-compose -f $COMPOSE_FILE exec -T app chmod -R 777 storage bootstrap/cache public

# 5. Cài đặt PHP dependencies (Composer)
echo ">>> Đang cài đặt Composer dependencies..."
docker-compose -f $COMPOSE_FILE exec -T app composer install --optimize-autoloader --no-dev

# 6. Tạo Application Key
echo ">>> Đang tạo APP_KEY cho Laravel..."
docker-compose -f $COMPOSE_FILE exec -T app php artisan key:generate

# 7. Chạy Migration & Seed dữ liệu
echo ">>> Đang chạy Migration và Seed dữ liệu mẫu..."
docker-compose -f $COMPOSE_FILE exec -T app php artisan migrate --force --seed

# 8. Xử lý Frontend (Vite)
if [ "$IS_PROD" = true ]; then
    echo ">>> Đang build assets cho Production..."
    # Chạy container node tạm thời để build rồi xóa
    docker run --rm -v $(pwd):/var/www -w /var/www node:20-alpine sh -c "npm install && npm run build"
else
    echo ">>> Đang cài đặt NPM dependencies (Local)..."
    docker-compose -f $COMPOSE_FILE exec -T node npm install
    echo ">>> Đang build assets (Vite)..."
    docker-compose -f $COMPOSE_FILE exec -T node npm run build
fi

echo -e "\n===================================================="
echo " THIẾT LẬP HOÀN TẤT!"
if [ "$IS_PROD" = true ]; then
    echo " - Môi trường: Production"
    echo " - Website: http://(IP_CUA_BAN)"
else
    echo " - Môi trường: Local"
    echo " - Website: http://localhost:8000"
    echo " - phpMyAdmin: http://localhost:8001"
fi
echo "===================================================="
