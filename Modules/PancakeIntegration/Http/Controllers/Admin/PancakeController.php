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
use Modules\PancakeIntegration\Services\WarehouseService;
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

    // ── Ẩn / Hiện sản phẩm ───────────────────────────────────────────────

    public function hideProduct($id, ProductService $productService)
    {
        $product = Product::findOrFail($id);
        $ok = $productService->hideProduct($product);
        return redirect()->back()->with(
            $ok ? 'success' : 'error',
            $ok ? "Đã ẩn sản phẩm \"{$product->name}\" trên Pancake." : 'Không ẩn được sản phẩm. Kiểm tra sản phẩm đã được đồng bộ chưa.'
        );
    }

    public function showProduct($id, ProductService $productService)
    {
        $product = Product::findOrFail($id);
        $ok = $productService->showProduct($product);
        return redirect()->back()->with(
            $ok ? 'success' : 'error',
            $ok ? "Đã hiện sản phẩm \"{$product->name}\" trên Pancake." : 'Không hiện được sản phẩm.'
        );
    }

    public function syncStock($id, ProductService $productService)
    {
        $product = Product::findOrFail($id);
        $ok = $productService->updateStock($product);
        return redirect()->back()->with(
            $ok ? 'success' : 'error',
            $ok ? "Đã cập nhật tồn kho sản phẩm \"{$product->name}\" lên Pancake." : 'Không cập nhật được tồn kho. Kiểm tra warehouse ID trong cấu hình.'
        );
    }

    // ── Kho (Warehouse) ──────────────────────────────────────────────────

    public function warehouses(WarehouseService $warehouseService)
    {
        $warehouses = $warehouseService->listWarehouses();
        return Inertia::render('Admin/Pancake/Warehouses', [
            'warehouses'         => $warehouses,
            'current_warehouse'  => PancakeSetting::getValue('pancake_warehouse_id', ''),
        ]);
    }

    public function createWarehouse(Request $request, WarehouseService $warehouseService)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'address'      => 'nullable|string|max:500',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $result = $warehouseService->createWarehouse($data);

        if ($result && isset($result['id'])) {
            PancakeSetting::setValue('pancake_warehouse_id', $result['id']);
            return redirect()->back()->with('success', "Tạo kho \"{$data['name']}\" thành công. Warehouse ID đã được cập nhật vào cấu hình.");
        }

        return redirect()->back()->with('error', 'Tạo kho thất bại. Kiểm tra lại API token và shop ID.');
    }

    public function updateWarehouse(Request $request, WarehouseService $warehouseService)
    {
        $data = $request->validate([
            'warehouse_id' => 'required|string',
            'name'         => 'required|string|max:255',
            'address'      => 'nullable|string|max:500',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $ok = $warehouseService->updateWarehouse($data['warehouse_id'], $data);

        return redirect()->back()->with(
            $ok ? 'success' : 'error',
            $ok ? "Đã cập nhật kho thành công." : 'Cập nhật kho thất bại.'
        );
    }

    public function setDefaultWarehouse(Request $request)
    {
        $request->validate(['warehouse_id' => 'required|string']);
        PancakeSetting::setValue('pancake_warehouse_id', $request->warehouse_id);
        return redirect()->back()->with('success', 'Đã đặt kho mặc định.');
    }
}
