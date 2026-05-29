<?php

namespace Modules\PancakeIntegration\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
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
        $token      = PancakeSetting::getValue('pancake_api_token');
        $shopId     = PancakeSetting::getValue('pancake_shop_id');
        $syncEnabled = PancakeSetting::getValue('pancake_sync_products', '1');

        if (!$token || !$shopId || !($syncEnabled == '1' || $syncEnabled === true || $syncEnabled === 'true')) {
            return false;
        }

        try {
            // Eager-load tất cả quan hệ cần dùng
            $product->load([
                'productAttributeValues.attribute',
                'productAttributeValues.attributeValue',
                'productImages',
            ]);

            $mapping         = PancakeProductMapping::where('product_id', $product->id)->first();
            $categoryMapping = \Modules\PancakeIntegration\Models\PancakeCategoryMapping
                                ::where('category_id', $product->category_id)->first();

            $sku         = $product->sku ?: 'SKU-' . $product->id;
            $retailPrice = (float) ($product->sale_price ?: $product->price);

            // ── Ảnh chính của sản phẩm ────────────────────────────────────────
            $mainImages = [];
            if ($product->image) {
                $mainImages[] = Storage::url($product->image);
            }
            foreach ($product->productImages as $img) {
                $mainImages[] = Storage::url($img->image_url);
            }

            // ── Thuộc tính & biến thể ─────────────────────────────────────────
            $productAttributes = [];
            $variations        = [];

            $pavCollection = $product->productAttributeValues;

            if ($pavCollection->isNotEmpty()) {
                // Nhóm các giá trị theo attribute
                $grouped = $pavCollection->groupBy('attribute_id');

                // Xây product_attributes cho Pancake
                foreach ($grouped as $items) {
                    $attribute = $items->first()->attribute;
                    $values    = $items->map(fn($i) => $i->attributeValue->value)
                                       ->unique()->values()->toArray();
                    $productAttributes[] = [
                        'name'   => $attribute->name,
                        'values' => $values,
                    ];
                }

                // Chuẩn bị dữ liệu từng nhóm để tích Descartes
                $groups = $grouped->map(fn($items) =>
                    $items->map(fn($item) => [
                        'attribute_name' => $item->attribute->name,
                        'value'          => $item->attributeValue->value,
                        'image_url'      => $item->image_url,
                    ])->values()->toArray()
                )->values()->toArray();

                // Tích Descartes → danh sách tổ hợp
                $combinations = $this->cartesianProduct($groups);

                foreach ($combinations as $combo) {
                    // Lấy ảnh đầu tiên có trong combo (ảnh biến thể)
                    $variationImages = [];
                    foreach ($combo as $field) {
                        if (!empty($field['image_url'])) {
                            $variationImages[] = Storage::url($field['image_url']);
                            break;
                        }
                    }
                    // Fallback về ảnh chính nếu biến thể không có ảnh riêng
                    if (empty($variationImages) && !empty($mainImages)) {
                        $variationImages = [$mainImages[0]];
                    }

                    $fields    = array_map(fn($f) => ['name' => $f['attribute_name'], 'value' => $f['value']], $combo);
                    $labelParts = array_map(fn($f) => $f['value'], $combo);
                    $variationSku = $sku . '-' . implode('-', $labelParts);

                    $variations[] = [
                        'fields'               => $fields,
                        'images'               => $variationImages,
                        'retail_price'         => $retailPrice,
                        'last_imported_price'  => (float) $product->price,
                        'custom_id'            => $variationSku,
                        'is_hidden'            => false,
                    ];
                }
            } else {
                // Sản phẩm đơn giản — một biến thể mặc định
                $variations[] = [
                    'fields'               => [],
                    'images'               => !empty($mainImages) ? [$mainImages[0]] : [],
                    'retail_price'         => $retailPrice,
                    'last_imported_price'  => (float) $product->price,
                    'custom_id'            => $sku,
                    'barcode'              => $product->barcode,
                    'is_hidden'            => false,
                ];
            }

            // ── Payload gửi Pancake ───────────────────────────────────────────
            $payload = [
                'product' => [
                    'name'               => $product->name,
                    'short_description'  => $product->short_description,
                    'category_ids'       => $categoryMapping ? [(int) $categoryMapping->pancake_category_id] : [],
                    'sku'                => $sku,
                    'description'        => $product->description,
                    'price'              => $retailPrice,
                    'original_price'     => (float) $product->price,
                    'inventory_quantity' => (int) $product->stock,
                    'images'             => array_map(fn($url) => ['src' => $url], $mainImages),
                    'barcode'            => $product->barcode,
                    'product_attributes' => $productAttributes,
                    'variations'         => $variations,
                ],
            ];

            if ($mapping && $mapping->pancake_product_id) {
                $response = $this->client->put("/products/{$mapping->pancake_product_id}", $payload);
                $action   = 'update';
            } else {
                $response = $this->client->post('/products', $payload);
                $action   = 'create';
            }

            if ($response->successful()) {
                $data             = $response->json();
                $pancakeProductId = $data['id'] ?? $data['product']['id'] ?? null;

                if ($pancakeProductId) {
                    PancakeProductMapping::updateOrCreate(
                        ['product_id' => $product->id],
                        [
                            'pancake_product_id' => $pancakeProductId,
                            'last_synced_at'     => now(),
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

    /**
     * Tích Descartes của các nhóm giá trị thuộc tính.
     *
     * Input:  [[{attr:'Màu', value:'Đen', ...}, {attr:'Màu', value:'Trắng'}],
     *           [{attr:'Size', value:'S'},  {attr:'Size', value:'M'}]]
     * Output: [[Đen,S], [Đen,M], [Trắng,S], [Trắng,M]]
     */
    protected function cartesianProduct(array $groups): array
    {
        $result = [[]];

        foreach ($groups as $group) {
            $newResult = [];
            foreach ($result as $existing) {
                foreach ($group as $item) {
                    $newResult[] = array_merge($existing, [$item]);
                }
            }
            $result = $newResult;
        }

        return $result;
    }

    public function updateHideStatus(array $productIds, bool $isHide = true): bool
    {
        $token      = PancakeSetting::getValue('pancake_api_token');
        $shopId     = PancakeSetting::getValue('pancake_shop_id');
        $syncEnabled = PancakeSetting::getValue('pancake_sync_products', '1');

        if (!$token || !$shopId || !($syncEnabled == '1' || $syncEnabled === true || $syncEnabled === 'true')) {
            return false;
        }

        // Map local product IDs to Pancake product IDs
        $pancakeIds = PancakeProductMapping::whereIn('product_id', $productIds)
            ->whereNotNull('pancake_product_id')
            ->pluck('pancake_product_id')
            ->toArray();

        if (empty($pancakeIds)) {
            return false;
        }

        $payload = [
            'is_hide' => $isHide,
            'product_ids' => $pancakeIds
        ];

        try {
            $response = $this->client->put('/products/update_hide', $payload);

            if ($response->successful()) {
                // Log success for the first product for reference
                $this->logSync(Product::find($productIds[0]), 'update_hide', 'success', $payload, $response->json());
                return true;
            }

            // Log failure
            $this->logSync(Product::find($productIds[0]), 'update_hide', 'failed', $payload, $response->json(), $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("Pancake Product Hide Sync Error: " . $e->getMessage());
            return false;
        }
    }

    protected function logSync($product, $action, $status, $payload, $response, $error = null): void
    {
        if (!$product) return;
        
        PancakeSyncLog::create([
            'model_type'    => Product::class,
            'model_id'      => $product->id,
            'action'        => $action,
            'status'        => $status,
            'payload'       => $payload,
            'response'      => $response,
            'error_message' => $error,
        ]);
    }
}
