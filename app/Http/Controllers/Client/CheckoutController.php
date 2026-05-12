<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\StockHistory;
use App\Mail\LowStockNotification;
use App\Mail\OrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        return Inertia::render('Client/Checkout');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = collect($request->items)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
                'status' => 'pending',
            ]);

            $settings = Setting::pluck('value', 'key')->all();
            $allowOutOfStock = ($settings['allow_out_of_stock_orders'] ?? '0') === '1';
            $lowStockThreshold = (int)($settings['low_stock_threshold'] ?? 5);
            $adminEmail = $settings['admin_notification_email'] ?? $settings['contact_email'] ?? null;

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['id']);
                
                if (!$product) {
                    throw new \Exception("Sản phẩm {$item['name']} không tồn tại.");
                }

                // Kiểm tra tồn kho nếu không cho phép đặt khi hết hàng
                if (!$allowOutOfStock && $product->stock < $item['quantity']) {
                    throw new \Exception("Sản phẩm {$product->name} hiện chỉ còn {$product->stock} sản phẩm trong kho.");
                }

                $oldStock = $product->stock;

                // Trừ kho
                $product->stock -= $item['quantity'];
                $product->save();

                // Ghi lịch sử kho (Loại: sale)
                StockHistory::create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'quantity' => $item['quantity'],
                    'old_stock' => $oldStock,
                    'new_stock' => $product->stock,
                    'note' => 'Đơn hàng: ' . $order->order_number,
                    'user_id' => null, // Khách hàng mua
                ]);

                // Gửi thông báo nếu sắp hết hàng
                if ($adminEmail && $product->stock <= $lowStockThreshold) {
                    try {
                        Mail::to($adminEmail)->send(new LowStockNotification($product));
                    } catch (\Exception $e) {
                        \Log::error('Low stock mail error: ' . $e->getMessage());
                    }
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // Gửi email thông báo đơn hàng mới cho Admin
            if ($adminEmail) {
                try {
                    Mail::to($adminEmail)->send(new OrderNotification($order));
                } catch (\Exception $e) {
                    \Log::error('Order notification mail error: ' . $e->getMessage());
                }
            }

            DB::commit();

            event(new \App\Events\OrderPlaced($order));

            return redirect()->route('checkout.success', ['order' => $order->order_number]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'Có lỗi xảy ra, vui lòng thử lại: ' . $e->getMessage()]);
        }
    }

    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with('items')->firstOrFail();
        return Inertia::render('Client/OrderSuccess', [
            'order' => $order
        ]);
    }
}
