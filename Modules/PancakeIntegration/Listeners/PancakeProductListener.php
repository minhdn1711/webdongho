<?php

namespace Modules\PancakeIntegration\Listeners;

use App\Events\ProductSaved;
use Modules\PancakeIntegration\Jobs\SyncProductJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class PancakeProductListener
{
    public function handle(ProductSaved $event): void
    {
        // Chỉ sync khi thêm mới — bỏ qua nếu sản phẩm đã có mapping trên Pancake
        $alreadySynced = \Modules\PancakeIntegration\Models\PancakeProductMapping
            ::where('product_id', $event->product->id)
            ->whereNotNull('pancake_product_id')
            ->exists();

        if ($alreadySynced) {
            return;
        }

        SyncProductJob::dispatch($event->product)
            ->delay(now()->addSeconds(2));
    }
}
