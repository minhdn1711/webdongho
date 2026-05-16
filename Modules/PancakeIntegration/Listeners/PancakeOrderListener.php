<?php

namespace Modules\PancakeIntegration\Listeners;

use App\Events\OrderPlaced;
use Modules\PancakeIntegration\Jobs\SyncOrderJob;

class PancakeOrderListener
{
    public function handle(OrderPlaced $event): void
    {
        SyncOrderJob::dispatch($event->order)
            ->delay(now()->addSeconds(5));
    }
}
