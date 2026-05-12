<?php

namespace Modules\PancakeIntegration\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class PancakeOrderMapping extends Model
{
    protected $fillable = [
        'order_id',
        'pancake_order_id',
        'last_synced_at'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
