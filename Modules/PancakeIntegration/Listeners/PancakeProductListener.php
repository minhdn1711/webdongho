<?php

namespace Modules\PancakeIntegration\Listeners;

use App\Events\ProductSaved;
use Modules\PancakeIntegration\Jobs\SyncProductJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class PancakeProductListener
{
    public function handle(ProductSaved $event): void
    {
        SyncProductJob::dispatch($event->product)
            ->delay(now()->addSeconds(2));
    }
}
