<?php

namespace Modules\PancakeIntegration\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\PancakeIntegration\Models\PancakeSetting;

class PancakeApiClient
{
    protected string $baseUrl = 'https://pos.pages.fm/api/v1';
    protected ?string $apiToken;
    protected ?string $shopId;

    public function __construct()
    {
        $this->apiToken = PancakeSetting::getValue('pancake_api_token');
        $this->shopId = PancakeSetting::getValue('pancake_shop_id');
        $this->baseUrl = PancakeSetting::getValue('pancake_api_url', 'https://pos.pages.fm/api/v1');
    }

    protected function getHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function get(string $endpoint, array $query = [])
    {
        $query['api_key'] = $this->apiToken;
        return $this->request('GET', $endpoint, ['query' => $query]);
    }

    public function post(string $endpoint, array $data = [])
    {
        return $this->request('POST', $endpoint, [
            'query' => ['api_key' => $this->apiToken],
            'json' => $data
        ]);
    }

    public function patch(string $endpoint, array $data = [])
    {
        return $this->request('PATCH', $endpoint, [
            'query' => ['api_key' => $this->apiToken],
            'json' => $data
        ]);
    }

    public function delete(string $endpoint)
    {
        return $this->request('DELETE', $endpoint, [
            'query' => ['api_key' => $this->apiToken]
        ]);
    }

    protected function request(string $method, string $endpoint, array $options = [])
    {
        if (!$this->apiToken || !$this->shopId) {
            Log::error('Pancake API Token or Shop ID is missing.');
            throw new \Exception('Pancake API Token or Shop ID is missing.');
        }

        // Auto-prepend shop ID
        $url = rtrim($this->baseUrl, '/') . "/shops/{$this->shopId}/" . ltrim($endpoint, '/');
        
        $response = Http::withHeaders($this->getHeaders())
            ->timeout(30)
            ->retry(3, 1000)
            ->send($method, $url, $options);

        if ($response->failed()) {
            Log::error("Pancake API Request Failed: {$method} {$url}", [
                'status' => $response->status(),
                'payload' => $options,
                'response' => $response->json(),
            ]);
            
            // Handle specific errors if needed
        }

        return $response;
    }
}
