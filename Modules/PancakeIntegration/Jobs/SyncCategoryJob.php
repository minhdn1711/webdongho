<?php

namespace Modules\PancakeIntegration\Jobs;

use App\Models\Category;
use Modules\PancakeIntegration\Services\CategoryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncCategoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function handle(CategoryService $categoryService)
    {
        $categoryService->syncCategory($this->category);
    }
}
