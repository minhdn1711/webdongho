<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'used_bytes',
        'max_bytes',
    ];
}
