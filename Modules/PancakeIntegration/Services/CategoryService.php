<?php

namespace Modules\PancakeIntegration\Services;

use App\Models\Category;
use Modules\PancakeIntegration\Models\PancakeCategoryMapping;
use Modules\PancakeIntegration\Models\PancakeSyncLog;
use Modules\PancakeIntegration\Models\PancakeSetting;
use Illuminate\Support\Facades\Log;

class CategoryService
{
    protected PancakeApiClient $client;

    public function __construct(PancakeApiClient $client)
    {
        $this->client = $client;
    }

    public function syncCategory(Category $category)
    {
        $token = PancakeSetting::getValue('pancake_api_token');
        $shopId = PancakeSetting::getValue('pancake_shop_id');

        $syncEnabled = PancakeSetting::getValue('pancake_sync_products', '1');
        if (!$token || !$shopId || !($syncEnabled == '1' || $syncEnabled === true || $syncEnabled === 'true')) {
            return false;
        }

        try {
            $mapping = PancakeCategoryMapping::where('category_id', $category->id)->first();

            $payload = [
                'category' => [
                    'name' => $category->name,
                ]
            ];

            if ($mapping && $mapping->pancake_category_id) {
                $response = $this->client->patch("/categories/{$mapping->pancake_category_id}", $payload);
                $action = 'update';
            } else {
                $response = $this->client->post('/categories', $payload);
                $action = 'create';
            }

            if ($response->successful()) {
                $data = $response->json();
                $pancakeCategoryId = $data['id'] ?? $data['category']['id'] ?? null;

                if ($pancakeCategoryId) {
                    PancakeCategoryMapping::updateOrCreate(
                        ['category_id' => $category->id],
                        [
                            'pancake_category_id' => $pancakeCategoryId,
                            'last_synced_at' => now(),
                        ]
                    );
                }

                $this->logSync($category, $action, 'success', $payload, $response->json());
                return $pancakeCategoryId;
            }

            $this->logSync($category, $action, 'failed', $payload, $response->json(), $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Pancake Category Sync Error: " . $e->getMessage());
            $this->logSync($category, 'sync', 'failed', $payload ?? [], null, $e->getMessage());
            return false;
        }
    }

    protected function logSync($category, $action, $status, $payload, $response, $error = null)
    {
        PancakeSyncLog::create([
            'model_type' => Category::class,
            'model_id' => $category->id,
            'action' => $action,
            'status' => $status,
            'payload' => $payload,
            'response' => $response,
            'error_message' => $error,
        ]);
    }
}
