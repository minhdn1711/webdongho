<x-mail::message>
# Cảnh báo hết hàng

Sản phẩm **{{ $product->name }}** hiện đang có số lượng tồn kho thấp.

**Thông tin sản phẩm:**
- **Mã sản phẩm:** #{{ $product->id }}
- **Tên sản phẩm:** {{ $product->name }}
- **Số lượng còn lại:** {{ $product->stock }}
- **Giá:** {{ number_format($product->price, 0, ',', '.') }}đ

Vui lòng kiểm tra và nhập thêm hàng để tránh gián đoạn việc bán hàng.

<x-mail::button :url="config('app.url') . '/admin/products'">
Quản lý sản phẩm
</x-mail::button>

Trân trọng,<br>
{{ config('app.name') }}
</x-mail::message>
