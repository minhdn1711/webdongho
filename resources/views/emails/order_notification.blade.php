<!DOCTYPE html>
<html>
<head>
    <title>Thông báo đơn hàng mới</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; }
        .header { background: #d10000; color: #fff; padding: 10px; text-align: center; }
        .content { padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f9f9f9; }
        .footer { margin-top: 20px; font-size: 12px; color: #888; text-align: center; }
        .total { font-weight: bold; color: #d10000; font-size: 18px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>ĐƠN HÀNG MỚI #{{ $order->order_number }}</h2>
        </div>
        <div class="content">
            <p>Chào Admin, hệ thống vừa ghi nhận một đơn hàng mới từ khách hàng.</p>
            
            <h3>Thông tin khách hàng:</h3>
            <ul>
                <li><strong>Họ tên:</strong> {{ $order->customer_name }}</li>
                <li><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</li>
                <li><strong>Email:</strong> {{ $order->customer_email }}</li>
                <li><strong>Địa chỉ:</strong> {{ $order->customer_address }}</li>
                <li><strong>Ghi chú:</strong> {{ $order->notes }}</li>
            </ul>

            <h3>Chi tiết sản phẩm:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>SL</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price, 0, ',', '.') }}đ</td>
                        <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <p class="total">Tổng giá trị: {{ number_format($order->total_amount, 0, ',', '.') }}đ</p>
            
            <p>Vui lòng đăng nhập vào trang quản trị để xử lý đơn hàng.</p>
        </div>
        <div class="footer">
            <p>Email này được gửi tự động từ hệ thống DongHoWeb.</p>
        </div>
    </div>
</body>
</html>
