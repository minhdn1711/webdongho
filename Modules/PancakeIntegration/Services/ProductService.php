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
        $token       = PancakeSetting::getValue('pancake_api_token');
        $shopId      = PancakeSetting::getValue('pancake_shop_id');
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

            // Ảnh chính + gallery — đã lưu là full URL, dùng trực tiếp
            $mainImages = [];
            if ($product->image) {
                $mainImages[] = $product->image;
            }
            foreach ($product->productImages as $img) {
                if ($img->image_url) {
                    $mainImages[] = $img->image_url;
                }
            }

            // Thuộc tính & biến thể
            $productAttributes = [];
            $variations        = [];
            $pavCollection     = $product->productAttributeValues;

            if ($pavCollection->isNotEmpty()) {
                $grouped = $pavCollection->groupBy('attribute_id');

                foreach ($grouped as $items) {
                    $attribute = $items->first()->attribute;
                    $values    = $items->map(fn($i) => $i->attributeValue->value)
                                       ->unique()->values()->toArray();
                    $productAttributes[] = [
                        'name'   => $attribute->name,
                        'values' => $values,
                    ];
                }

                $groups = $grouped->map(fn($items) =>
                    $items->map(fn($item) => [
                        'attribute_name' => $item->attribute->name,
                        'value'          => $item->attributeValue->value,
                        'image_url'      => $item->image_url, // đã là full URL
                    ])->values()->toArray()
                )->values()->toArray();

                $combinations = $this->cartesianProduct($groups);

                foreach ($combinations as $combo) {
                    $variationImages = [];
                    foreach ($combo as $field) {
                        if (!empty($field['image_url'])) {
                            $variationImages[] = $field['image_url'];
                            break;
                        }
                    }
                    // Fallback về ảnh chính nếu biến thể không có ảnh riêng
                    if (empty($variationImages) && !empty($mainImages)) {
                        $variationImages = [$mainImages[0]];
                    }

                    $fields     = array_map(fn($f) => ['name' => $f['attribute_name'], 'value' => $f['value']], $combo);
                    $labelParts = array_map(fn($f) => $f['value'], $combo);

                    $variations[] = [
                        'fields'              => $fields,
                        'images'              => $variationImages, // mảng URL string theo Pancake API
                        'retail_price'        => $retailPrice,
                        'last_imported_price' => (float) $product->price,
                        'custom_id'           => $sku . '-' . implode('-', $labelParts),
                        'is_hidden'           => false,
                    ];
                }
            } else {
                // Sản phẩm đơn giản — một biến thể mặc định
                $variations[] = [
                    'fields'              => [],
                    'images'              => !empty($mainImages) ? [$mainImages[0]] : [], // URL string theo Pancake API
                    'retail_price'        => $retailPrice,
                    'last_imported_price' => (float) $product->price,
                    'custom_id'           => $sku,
                    'barcode'             => $product->barcode,
                    'is_hidden'           => false,
                ];
            }

            $warehouseId = \Modules\PancakeIntegration\Models\PancakeSetting::getValue('pancake_warehouse_id');

            // Gắn tồn kho vào từng variation nếu có warehouse_id
            if ($warehouseId) {
                $totalStock = (int) $product->stock;
                $variationCount = count($variations);
                foreach ($variations as &$v) {
                    $v['variations_warehouses'] = [[
                        'warehouse_id'    => $warehouseId,
                        'remain_quantity' => $variationCount > 1 ? 0 : $totalStock,
                    ]];
                }
                unset($v);
            }

            $payload = [
                'product' => [
                    'name'               => $product->name,
                    'note_product'       => $product->short_description,
                    'category_ids'       => $categoryMapping ? [(int) $categoryMapping->pancake_category_id] : [],
                    'custom_id'          => $sku,
                    'description'        => $product->description,
                    'product_attributes' => $productAttributes,
                    'variations'         => $variations,
                    'is_published'       => true,
                ],
            ];

            if ($mapping && $mapping->pancake_product_id) {
                // Lấy variation IDs từ Pancake để update ảnh đúng variation
                $pancakeGet  = $this->client->get("/products/{$mapping->pancake_product_id}");
                $pancakeVars = $pancakeGet->successful()
                    ? $pancakeGet->json('data.variations', [])
                    : [];

                // Map: display_id (= custom_id ta gửi lúc tạo) → Pancake variation UUID
                $pancakeVarByCustomId = [];
                foreach ($pancakeVars as $pv) {
                    $key = $pv['display_id'] ?? null;
                    if ($key) {
                        $pancakeVarByCustomId[$key] = $pv['id'];
                    }
                }

                // Build variation updates: chỉ gửi id + images cho đúng variation
                $varImageUpdates = [];
                foreach ($payload['product']['variations'] as $localVar) {
                    $localCustomId = $localVar['custom_id'] ?? null;
                    $pancakeVarId  = $localCustomId ? ($pancakeVarByCustomId[$localCustomId] ?? null) : null;
                    if ($pancakeVarId) {
                        $varImageUpdates[] = [
                            'id'     => $pancakeVarId,
                            'images' => $localVar['images'] ?? [],
                        ];
                    }
                }

                $updatePayload = [
                    'product' => [
                        'name'         => $payload['product']['name'],
                        'note_product' => $payload['product']['note_product'],
                        'category_ids' => $payload['product']['category_ids'],
                        'description'  => $payload['product']['description'],
                        'is_published' => true,
                    ],
                ];

                if (!empty($varImageUpdates)) {
                    $updatePayload['product']['variations'] = $varImageUpdates;
                }

                $response = $this->client->patch("/products/{$mapping->pancake_product_id}", $updatePayload);
                $action   = 'update';

                // Sản phẩm bị xóa trên POS → tạo lại từ đầu
                if ($response->status() === 404) {
                    $mapping->delete();
                    $response = $this->client->post('/products', $payload);
                    $action   = 'create';
                }
            } else {
                $response = $this->client->post('/products', $payload);
                $action   = 'create';
            }

            if ($response->successful()) {
                $data             = $response->json();
                $pancakeProductId = $data['data']['id'] ?? $data['id'] ?? null;

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

    public function hideProduct(Product $product): bool
    {
        return $this->setPublished($product, false);
    }

    public function showProduct(Product $product): bool
    {
        return $this->setPublished($product, true);
    }

    protected function setPublished(Product $product, bool $published): bool
    {
        $mapping = PancakeProductMapping::where('product_id', $product->id)
            ->whereNotNull('pancake_product_id')
            ->first();

        if (!$mapping) {
            return false;
        }

        $response = $this->client->patch("/products/{$mapping->pancake_product_id}", [
            'product' => ['is_published' => $published],
        ]);

        return $response->successful();
    }

    public function updateStock(Product $product): bool
    {
        $mapping = PancakeProductMapping::where('product_id', $product->id)
            ->whereNotNull('pancake_product_id')
            ->first();

        if (!$mapping) {
            return false;
        }

        $warehouseId = \Modules\PancakeIntegration\Models\PancakeSetting::getValue('pancake_warehouse_id');
        if (!$warehouseId) {
            return false;
        }

        // Lấy danh sách variation IDs từ Pancake để update đúng variation
        $getResponse = $this->client->get("/products/{$mapping->pancake_product_id}");
        if (!$getResponse->successful()) {
            return false;
        }

        $pancakeVariations = $getResponse->json('data.variations', []);
        if (empty($pancakeVariations)) {
            return false;
        }

        $stock      = (int) $product->stock;
        $countVars  = count($pancakeVariations);

        $variationsPayload = array_map(function ($v) use ($warehouseId, $stock, $countVars) {
            return [
                'id'                  => $v['id'],
                'variations_warehouses' => [[
                    'warehouse_id'    => $warehouseId,
                    'remain_quantity' => $countVars > 1 ? 0 : $stock,
                ]],
            ];
        }, $pancakeVariations);

        $response = $this->client->patch("/products/{$mapping->pancake_product_id}", [
            'product' => ['variations' => $variationsPayload],
        ]);

        return $response->successful();
    }

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
