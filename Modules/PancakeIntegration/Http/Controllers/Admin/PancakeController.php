<?php

namespace Modules\PancakeIntegration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\PancakeIntegration\Models\PancakeSetting;
use Modules\PancakeIntegration\Models\PancakeSyncLog;
use Modules\PancakeIntegration\Models\PancakeWebhookLog;
use Modules\PancakeIntegration\Services\ProductService;
use Modules\PancakeIntegration\Services\OrderService;
use App\Models\Product;
use App\Models\Order;

class PancakeController extends Controller
{
    public function settings()
    {
        return Inertia::render('Admin/Pancake/Settings', [
            'settings' => [
                'shop_id' => PancakeSetting::getValue('pancake_shop_id', ''),
                'warehouse_id' => PancakeSetting::getValue('pancake_warehouse_id', ''),
                'api_token' => PancakeSetting::getValue('pancake_api_token', ''),
                'api_url' => PancakeSetting::getValue('pancake_api_url', 'https://api.pancake.vn/v1'),
                'webhook_secret' => PancakeSetting::getValue('pancake_webhook_secret', ''),
                'sync_products' => PancakeSetting::getValue('pancake_sync_products', '1'),
                'sync_orders' => PancakeSetting::getValue('pancake_sync_orders', '1'),
            ]
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'shop_id' => 'required|string',
            'warehouse_id' => 'required|string',
            'api_token' => 'required|string',
            'api_url' => 'required|url',
            'webhook_secret' => 'nullable|string',
            'sync_products' => 'boolean',
            'sync_orders' => 'boolean',
        ]);

        foreach ($data as $key => $value) {
            PancakeSetting::setValue("pancake_{$key}", $value);
        }

        return redirect()->back()->with('success', 'Cấu hình Pancake đã được cập nhật.');
    }

    public function syncLogs()
    {
        return Inertia::render('Admin/Pancake/SyncLogs', [
            'logs' => PancakeSyncLog::latest()->paginate(20)
        ]);
    }

    public function webhookLogs()
    {
        return Inertia::render('Admin/Pancake/WebhookLogs', [
            'logs' => PancakeWebhookLog::latest()->paginate(20)
        ]);
    }

    public function manualSync($type, $id, ProductService $productService, OrderService $orderService)
    {
        if ($type === 'product') {
            $product = Product::findOrFail($id);
            $productService->syncProduct($product);
        } elseif ($type === 'order') {
            $order = Order::findOrFail($id);
            $orderService->syncOrder($order);
        }

        return redirect()->back()->with('success', 'Đã bắt đầu đồng bộ thủ công.');
    }

    public function retrySync($id, ProductService $productService, OrderService $orderService)
    {
        $log = PancakeSyncLog::findOrFail($id);
        
        if ($log->model_type === Product::class) {
            $product = Product::find($log->model_id);
            if ($product) $productService->syncProduct($product);
        } elseif ($log->model_type === Order::class) {
            $order = Order::find($log->model_id);
            if ($order) $orderService->syncOrder($order);
        }

        return redirect()->back()->with('success', 'Đã thử lại đồng bộ.');
    }
}
