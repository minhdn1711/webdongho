<?php

namespace Modules\PancakeIntegration\Services;

use Modules\PancakeIntegration\Models\PancakeSetting;
use Illuminate\Support\Facades\Log;

class WarehouseService
{
    protected PancakeApiClient $client;

    public function __construct(PancakeApiClient $client)
    {
        $this->client = $client;
    }

    public function listWarehouses(): array
    {
        $response = $this->client->get('/warehouses');
        if ($response->successful()) {
            return $response->json('data', []);
        }
        return [];
    }

    public function createWarehouse(array $data): ?array
    {
        $payload = ['warehouse' => $this->buildPayload($data)];
        $response = $this->client->post('/warehouses', $payload);

        if ($response->successful()) {
            return $response->json('data');
        }

        Log::error('Pancake create warehouse failed', [
            'status'   => $response->status(),
            'response' => $response->json(),
        ]);
        return null;
    }

    public function updateWarehouse(string $warehouseId, array $data): bool
    {
        $payload = ['warehouse' => $this->buildPayload($data)];
        $response = $this->client->patch("/warehouses/{$warehouseId}", $payload);

        if (!$response->successful()) {
            Log::error('Pancake update warehouse failed', [
                'warehouse_id' => $warehouseId,
                'status'       => $response->status(),
                'response'     => $response->json(),
            ]);
        }

        return $response->successful();
    }

    protected function buildPayload(array $data): array
    {
        return array_filter([
            'name'         => $data['name'] ?? null,
            'address'      => $data['address'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
        ], fn($v) => $v !== null);
    }
}
