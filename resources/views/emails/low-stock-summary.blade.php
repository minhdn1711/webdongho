<x-mail::message>
# Báo cáo hàng tồn kho thấp

Hệ thống đã kiểm tra và phát hiện các sản phẩm dưới đây đang có số lượng tồn kho thấp hơn ngưỡng quy định. 
Vì chế độ **"Cho phép đặt hàng khi hết hàng"** đang bật, vui lòng kiểm tra và nhập thêm hàng sớm nhất có thể.

<x-mail::table>
| Sản phẩm | Danh mục | Tồn kho | Giá |
| :--- | :--- | :---: | :--- |
@foreach ($products as $product)
| {{ $product->name }} | {{ $product->category->name ?? 'N/A' }} | **{{ $product->stock }}** | {{ number_format($product->price, 0, ',', '.') }}đ |
@endforeach
</x-mail::table>

<x-mail::button :url="config('app.url') . '/admin/stock'">
Quản lý kho hàng
</x-mail::button>

Trân trọng,<br>
{{ config('app.name') }}
</x-mail::message>
