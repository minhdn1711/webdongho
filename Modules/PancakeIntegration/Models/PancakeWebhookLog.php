<?php

namespace Modules\PancakeIntegration\Models;

use Illuminate\Database\Eloquent\Model;

class PancakeWebhookLog extends Model
{
    protected $fillable = [
        'topic',
        'payload',
        'status',
        'error'
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
