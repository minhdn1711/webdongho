<?php

namespace Modules\PancakeIntegration\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class PancakeCategoryMapping extends Model
{
    protected $fillable = [
        'category_id',
        'pancake_category_id',
        'last_synced_at'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
