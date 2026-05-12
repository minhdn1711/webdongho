<?php

namespace Modules\PancakeIntegration\Models;

use Illuminate\Database\Eloquent\Model;

class PancakeSetting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    public static function getValue(string $key, $default = null)
    {
        return self::where('key', $key)->value('value') ?? $default;
    }

    public static function setValue(string $key, $value, string $group = 'general')
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
    }
}
