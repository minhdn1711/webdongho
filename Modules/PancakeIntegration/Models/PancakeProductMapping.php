<?php

namespace Modules\PancakeIntegration\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class PancakeProductMapping extends Model
{
    protected $fillable = [
        'product_id',
        'pancake_product_id',
        'pancake_variant_id',
        'last_synced_at'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
