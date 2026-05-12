<?php

namespace Modules\PancakeIntegration\Listeners;

use App\Events\CategorySaved;
use Modules\PancakeIntegration\Jobs\SyncCategoryJob;

class PancakeCategoryListener
{
    public function handle(CategorySaved $event)
    {
        SyncCategoryJob::dispatch($event->category)->onQueue('pancake');
    }
}
