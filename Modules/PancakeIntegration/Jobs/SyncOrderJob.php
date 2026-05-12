<?php

namespace Modules\PancakeIntegration\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\PancakeIntegration\Services\OrderService;

class SyncOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $backoff = 300;

    public function __construct(public Order $order) {}

    public function handle(OrderService $orderService): void
    {
        $orderService->syncOrder($this->order);
    }
}
