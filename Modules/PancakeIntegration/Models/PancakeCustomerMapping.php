<?php

namespace Modules\PancakeIntegration\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PancakeCustomerMapping extends Model
{
    protected $fillable = [
        'user_id',
        'pancake_customer_id',
        'last_synced_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
