<?php

namespace Modules\PancakeIntegration\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\PancakeIntegration\Models\PancakeWebhookLog;
use Modules\PancakeIntegration\Models\PancakeOrderMapping;
use Modules\PancakeIntegration\Models\PancakeProductMapping;
use App\Models\Order;
use App\Models\Product;

class ProcessPancakeWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public PancakeWebhookLog $log) {}

    public function handle(): void
    {
        $topic = $this->log->topic;
        $payload = $this->log->payload;

        try {
            switch ($topic) {
                case 'orders/update':
                case 'orders/status_changed':
                    $this->handleOrderUpdate($payload);
                    break;

                case 'products/update':
                case 'inventory/update':
                    $this->handleProductUpdate($payload);
                    break;
            }

            $this->log->update(['status' => 'processed']);
        } catch (\Exception $e) {
            $this->log->update(['status' => 'failed', 'error' => $e->getMessage()]);
        }
    }

    protected function handleOrderUpdate(array $payload)
    {
        $pancakeOrderId = $payload['id'] ?? null;
        if (!$pancakeOrderId) return;

        $mapping = PancakeOrderMapping::where('pancake_order_id', $pancakeOrderId)->first();
        if ($mapping && $mapping->order) {
            $order = $mapping->order;
            $newStatus = $this->mapPancakeStatus($payload['status'] ?? '');
            
            if ($newStatus && $order->status !== $newStatus) {
                $order->update(['status' => $newStatus]);
            }
        }
    }

    protected function handleProductUpdate(array $payload)
    {
        $pancakeProductId = $payload['id'] ?? null;
        if (!$pancakeProductId) return;

        $mapping = PancakeProductMapping::where('pancake_product_id', $pancakeProductId)->first();
        if ($mapping && $mapping->product) {
            $product = $mapping->product;
            
            // Sync inventory if provided
            if (isset($payload['inventory_quantity'])) {
                $product->update(['stock' => (int) $payload['inventory_quantity']]);
            }
        }
    }

    protected function mapPancakeStatus(string $pancakeStatus): ?string
    {
        $map = [
            'new' => 'pending',
            'confirmed' => 'processing',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            'returned' => 'cancelled',
        ];

        return $map[$pancakeStatus] ?? null;
    }
}
