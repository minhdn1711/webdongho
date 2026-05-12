<?php

namespace App\Events;

use App\Models\Category;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CategorySaved
{
    use Dispatchable, SerializesModels;

    public $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }
}
