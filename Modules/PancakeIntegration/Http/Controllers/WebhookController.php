<?php

namespace Modules\PancakeIntegration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\PancakeIntegration\Models\PancakeWebhookLog;
use Modules\PancakeIntegration\Jobs\ProcessPancakeWebhookJob;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        $topic = $request->header('X-Pancake-Topic') ?? 'unknown';
        $signature = $request->header('X-Pancake-Signature');

        // Log the webhook
        $webhookLog = PancakeWebhookLog::create([
            'topic' => $topic,
            'payload' => $payload,
            'status' => 'pending',
        ]);

        try {
            // Verify signature if configured
            if (!$this->verifySignature($request->getContent(), $signature)) {
                $webhookLog->update(['status' => 'failed', 'error' => 'Invalid signature']);
                return response()->json(['message' => 'Invalid signature'], 401);
            }

            // Dispatch job for asynchronous processing
            ProcessPancakeWebhookJob::dispatch($webhookLog)
                ->onQueue('pancake');

            return response()->json(['message' => 'Webhook received']);
        } catch (\Exception $e) {
            Log::error("Pancake Webhook Error: " . $e->getMessage());
            $webhookLog->update(['status' => 'failed', 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Internal error'], 500);
        }
    }

    protected function verifySignature($payload, $signature): bool
    {
        $secret = config('services.pancake.webhook_secret');
        if (!$secret) return true; // Skip if not configured

        $computedSignature = base64_encode(hash_hmac('sha256', $payload, $secret, true));
        return hash_equals($computedSignature, $signature ?? '');
    }
}
