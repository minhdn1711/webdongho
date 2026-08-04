<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteFeature extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'icon_svg', 'order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
