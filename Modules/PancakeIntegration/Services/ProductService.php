<?php

namespace Modules\PancakeIntegration\Services;

use App\Models\Product;
use Modules\PancakeIntegration\Models\PancakeProductMapping;
use Modules\PancakeIntegration\Models\PancakeSyncLog;
use Modules\PancakeIntegration\Models\PancakeSetting;
use Illuminate\Support\Facades\Log;

class ProductService
{
    protected PancakeApiClient $client;

    public function __construct(PancakeApiClient $client)
    {
        $this->client = $client;
    }

    public function syncProduct(Product $product)
    {
        $token = PancakeSetting::getValue('pancake_api_token');
        $shopId = PancakeSetting::getValue('pancake_shop_id');

        if (!$token || !$shopId || PancakeSetting::getValue('pancake_sync_products', '1') !== '1') {
            return false;
        }

        try {
            $mapping = PancakeProductMapping::where('product_id', $product->id)->first();
            $categoryMapping = \Modules\PancakeIntegration\Models\PancakeCategoryMapping::where('category_id', $product->category_id)->first();
            
            $sku = $product->sku ?: 'SKU-' . $product->id;

            $payload = [
                'product' => [
                    'name' => $product->name,
                    'short_description' => $product->short_description,
                    'category_id' => $categoryMapping ? $categoryMapping->pancake_category_id : null,
                    'sku' => $sku,
                    'description' => $product->description,
                    'price' => (float) $product->price,
                    'original_price' => (float) ($product->sale_price ?: $product->price),
                    'inventory_quantity' => (int) $product->stock,
                    'images' => $product->image ? [['src' => url($product->image)]] : [],
                    'barcode' => $product->barcode,
                    // Thêm phiên bản mặc định để hiển thị trên POS
                    'variations' => [
                        [
                            'fields' => [],
                            'retail_price' => (float) ($product->sale_price ?: $product->price),
                            'inventory_quantity' => (int) $product->stock,
                            'sku' => $sku,
                            'barcode' => $product->barcode,
                        ]
                    ]
                ]
            ];

            if ($mapping && $mapping->pancake_product_id) {
                // Update existing product
                $response = $this->client->patch("/products/{$mapping->pancake_product_id}", $payload);
                $action = 'update';
            } else {
                // Create new product
                $response = $this->client->post('/products', $payload);
                $action = 'create';
            }

            if ($response->successful()) {
                $data = $response->json();
                $pancakeProductId = $data['id'] ?? $data['product']['id'] ?? null;

                if ($pancakeProductId) {
                    PancakeProductMapping::updateOrCreate(
                        ['product_id' => $product->id],
                        [
                            'pancake_product_id' => $pancakeProductId,
                            'last_synced_at' => now(),
                        ]
                    );
                }

                $this->logSync($product, $action, 'success', $payload, $response->json());
                return true;
            }

            $this->logSync($product, $action, 'failed', $payload, $response->json(), $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Pancake Product Sync Error: " . $e->getMessage());
            $this->logSync($product, 'sync', 'failed', $payload ?? [], null, $e->getMessage());
            return false;
        }
    }

    protected function logSync($product, $action, $status, $payload, $response, $error = null)
    {
        PancakeSyncLog::create([
            'model_type' => Product::class,
            'model_id' => $product->id,
            'action' => $action,
            'status' => $status,
            'payload' => $payload,
            'response' => $response,
            'error_message' => $error,
        ]);
    }
}
