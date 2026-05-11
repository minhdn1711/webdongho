# Kế hoạch thiết kế Website bán đồng hồ (Laravel + Vue.js + Docker)

Dự án nhằm xây dựng một website thương mại điện tử bán đồng hồ chuyên nghiệp, lấy cảm hứng từ `julius.vn`, với hệ quản trị (Admin) mạnh mẽ kiểu WordPress và hiệu năng cao trên nền tảng Docker.

## User Review Required

> [!IMPORTANT]
> **Lựa chọn Framework Frontend:** Tôi đề xuất sử dụng **Inertia.js** cùng với **Vue 3**. Inertia giúp kết hợp sức mạnh của Laravel (Routing, Auth, SSR) với trải nghiệm mượt mà của Vue mà không cần xây dựng API riêng biệt phức tạp. Bạn có đồng ý với hướng này không?

> [!WARNING]
> **Quản lý Ảnh:** Đối với VPS, việc lưu trữ ảnh trực tiếp trên ổ đĩa có thể gây đầy dung lượng nhanh chóng nếu không tối ưu. Tôi đề xuất sử dụng **Spatie Media Library** để tự động resize, tạo thumbnail và chuyển đổi sang định dạng **WebP** để tối ưu tốc độ load.

## Kiến trúc Hệ thống (Docker)

Hệ thống sẽ chạy trên 4 container chính:
1. **App (PHP 8.3-FPM):** Chạy mã nguồn Laravel.
2. **Web (Nginx):** Xử lý request và serve file tĩnh.
3. **DB (MySQL 8.0):** Lưu trữ dữ liệu.
4. **Redis:** Dùng cho caching và session để tăng tốc độ.
5. **Node.js (chỉ dùng khi phát triển):** Để compile assets (Vite).

## Thiết kế Database

Các bảng chính dự kiến:
- `categories`: Danh mục (Đồng hồ nam, nữ, cặp...).
- `brands`: Thương hiệu (Julius, Julius Star...).
- `products`: Thông tin sản phẩm (tên, slug, giá, giá khuyến mãi, mô tả, thông số kỹ thuật).
- `product_attributes`: Các thuộc tính như màu sắc, chất liệu dây, kiểu mặt.
- `product_images`: Lưu trữ nhiều ảnh cho mỗi sản phẩm.
- `orders` & `order_items`: Quản lý đơn hàng và chi tiết.
- `sliders` & `banners`: Quản lý giao diện trang chủ.

## Giao diện & Tính năng

### 1. Web Admin (Phong cách WordPress)
- **Sidebar:** Thanh menu bên trái màu tối, có thể thu gọn, phân chia rõ ràng: Tổng quan, Sản phẩm, Đơn hàng, Bài viết, Hình ảnh, Cấu hình.
- **Media Manager:** Xây dựng một thư viện ảnh riêng giống WP để quản lý và tái sử dụng ảnh.
- **Trình soạn thảo:** Sử dụng TinyMCE hoặc CKEditor để viết mô tả sản phẩm/bài viết.
- **Dashboard:** Biểu đồ doanh thu (Chart.js) và thống kê đơn hàng mới.

### 2. Web Client (Giống Julius.vn)
- **Trang chủ:** Slider lớn, danh mục nổi bật, sản phẩm mới, sản phẩm bán chạy.
- **Trang danh mục:** Bộ lọc AJAX (màu sắc, chất liệu, giá) không cần load lại trang.
- **Trang chi tiết:** Gallery ảnh có zoom, thông số kỹ thuật dạng bảng, sản phẩm liên quan.
- **Giỏ hàng & Thanh toán:** Quy trình tối giản, hỗ trợ đặt hàng nhanh.

## Chiến lược Xử lý Ảnh & Upload

Để việc upload ảnh hợp lý trên VPS:
- **Xử lý phía Server:** Khi upload, Laravel sẽ resize ảnh về các kích thước chuẩn (Large, Medium, Thumbnail) và lưu dưới dạng WebP.
- **Lưu trữ:** Sử dụng `public/storage` kết hợp với Docker Volume để đảm bảo ảnh không bị mất khi container restart.
- **Lazy Loading:** Sử dụng thư viện Vue để chỉ load ảnh khi user cuộn tới.

## Kế hoạch Triển khai VPS

1. **Chuẩn bị:** Cài đặt Docker & Docker Compose trên VPS (Ubuntu 22.04/24.04).
2. **CI/CD (Tùy chọn):** Sử dụng GitHub Actions để tự động build và push image hoặc đơn giản là `git pull` & `docker-compose up -d --build`.
3. **SSL:** Sử dụng **Nginx Proxy Manager** hoặc **Certbot** để cài đặt SSL (HTTPS) miễn phí.
4. **Backup:** Script tự động backup database hàng ngày và lưu ra ngoài VPS (Google Drive/S3).

## Các bước tiếp theo

1. Khởi tạo cấu trúc thư mục Docker.
2. Cài đặt Laravel 11 + Inertia + Vue 3.
3. Thiết kế giao diện Admin cơ bản (Layout & Sidebar).
4. Xây dựng Database Migration.
