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
            ->onQueue('pancake')
            ->delay(now()->addSeconds(5));
    }
}
