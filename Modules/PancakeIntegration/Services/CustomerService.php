<?php

namespace Modules\PancakeIntegration\Services;

use App\Models\User;
use Modules\PancakeIntegration\Models\PancakeCustomerMapping;
use Modules\PancakeIntegration\Models\PancakeSyncLog;
use Illuminate\Support\Facades\Log;

class CustomerService
{
    protected PancakeApiClient $client;

    public function __construct(PancakeApiClient $client)
    {
        $this->client = $client;
    }

    public function syncCustomer(User $user)
    {
        try {
            $mapping = PancakeCustomerMapping::where('user_id', $user->id)->first();

            $payload = [
                'full_name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone,
                'address' => $user->address,
            ];

            if ($mapping && $mapping->pancake_customer_id) {
                $response = $this->client->patch("/customers/{$mapping->pancake_customer_id}", $payload);
                $action = 'update';
            } else {
                $response = $this->client->post('/customers', $payload);
                $action = 'create';
            }

            if ($response->successful()) {
                $data = $response->json();
                $pancakeCustomerId = $data['id'] ?? $data['customer']['id'] ?? null;

                if ($pancakeCustomerId) {
                    PancakeCustomerMapping::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'pancake_customer_id' => $pancakeCustomerId,
                            'last_synced_at' => now(),
                        ]
                    );
                }

                $this->logSync($user, $action, 'success', $payload, $response->json());
                return true;
            }

            $this->logSync($user, $action, 'failed', $payload, $response->json(), $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Pancake Customer Sync Error: " . $e->getMessage());
            $this->logSync($user, 'sync', 'failed', $payload ?? [], null, $e->getMessage());
            return false;
        }
    }

    protected function logSync($user, $action, $status, $payload, $response, $error = null)
    {
        PancakeSyncLog::create([
            'model_type' => User::class,
            'model_id' => $user->id,
            'action' => $action,
            'status' => $status,
            'payload' => $payload,
            'response' => $response,
            'error_message' => $error,
        ]);
    }
}
