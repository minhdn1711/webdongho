<?php

namespace Modules\PancakeIntegration\Models;

use Illuminate\Database\Eloquent\Model;

class PancakeSyncLog extends Model
{
    protected $fillable = [
        'model_type',
        'model_id',
        'action',
        'status',
        'payload',
        'response',
        'error_message'
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
    ];
}
