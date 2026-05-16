<?php

namespace Modules\PancakeIntegration\Services;

use App\Models\Order;
use Modules\PancakeIntegration\Models\PancakeOrderMapping;
use Modules\PancakeIntegration\Models\PancakeSyncLog;
use Modules\PancakeIntegration\Models\PancakeSetting;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected PancakeApiClient $client;

    public function __construct(PancakeApiClient $client)
    {
        $this->client = $client;
    }

    public function syncOrder(Order $order)
    {
        $token = PancakeSetting::getValue('pancake_api_token');
        $shopId = PancakeSetting::getValue('pancake_shop_id');

        if (!$token || !$shopId || PancakeSetting::getValue('pancake_sync_orders', '1') !== '1') {
            return false;
        }

        try {
            $mapping = PancakeOrderMapping::where('order_id', $order->id)->first();
            
            $warehouseId = PancakeSetting::getValue('pancake_warehouse_id');
            $shopId = PancakeSetting::getValue('pancake_shop_id');
            
            $payload = [
                'shop_id' => (int) $shopId,
                'bill_full_name' => $order->customer_name,
                'bill_phone_number' => $order->customer_phone,
                'is_free_shipping' => false,
                'received_at_shop' => false,
                'status' => 0,
                'items' => $order->items->map(function ($item) use ($warehouseId) {
                    $productMapping = \Modules\PancakeIntegration\Models\PancakeProductMapping::where('product_id', $item->product_id)->first();
                    return [
                        'quantity' => (int) $item->quantity,
                        'product_id' => $productMapping ? $productMapping->pancake_product_id : null,
                        'warehouse_id' => $warehouseId,
                        'one_time_product' => $productMapping ? false : true,
                        'variation_info' => [
                            'name' => $item->product_name,
                            'retail_price' => (float) $item->price,
                        ]
                    ];
                })->toArray(),
                'shipping_address' => [
                    'full_name' => $order->customer_name,
                    'phone_number' => $order->customer_phone,
                    'address' => $order->shipping_address ? $order->custom_address : 'Chưa có địa chỉ',
                    'full_address' => $order->shipping_address ? $order->custom_address : 'Chưa có địa chỉ',
                ],
                'note' => $order->note ?: '',
                'warehouse_id' => $warehouseId,
                'warehouse_info' => [
                    'name' => 'Kho Lamtime',
                ],
                'total_discount' => 0,
                'shipping_fee' => (float) ($order->shipping_fee ?? 0),
                'custom_id' => 'WEB-' . $order->id,
                'cash' => 0,
            ];

            Log::info('Pancake Order Sync Payload:', $payload);

            if ($mapping && $mapping->pancake_order_id) {
                // Update existing order
                $response = $this->client->patch("/orders/{$mapping->pancake_order_id}", $payload);
                $action = 'update';
            } else {
                // Create new order
                $response = $this->client->post('/orders', $payload);
                $action = 'create';
            }

            if ($response->successful()) {
                $data = $response->json();
                $pancakeOrderId = $data['id'] ?? $data['order']['id'] ?? null;

                if ($pancakeOrderId) {
                    PancakeOrderMapping::updateOrCreate(
                        ['order_id' => $order->id],
                        [
                            'pancake_order_id' => $pancakeOrderId,
                            'last_synced_at' => now(),
                        ]
                    );
                }

                $this->logSync($order, $action, 'success', $payload, $response->json());
                return true;
            }

            $this->logSync($order, $action, 'failed', $payload, $response->json(), $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Pancake Order Sync Error: " . $e->getMessage());
            $this->logSync($order, 'sync', 'failed', $payload ?? [], null, $e->getMessage());
            return false;
        }
    }

    protected function mapStatus(string $localStatus): string
    {
        // Map your local status to Pancake status
        $map = [
            'pending' => 'new',
            'processing' => 'confirmed',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
        ];

        return $map[$localStatus] ?? 'new';
    }

    protected function logSync($order, $action, $status, $payload, $response, $error = null)
    {
        PancakeSyncLog::create([
            'model_type' => Order::class,
            'model_id' => $order->id,
            'action' => $action,
            'status' => $status,
            'payload' => $payload,
            'response' => $response,
            'error_message' => $error,
        ]);
    }
}
