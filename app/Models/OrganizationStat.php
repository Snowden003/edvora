<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationStat extends Model
{
    protected $fillable = [
        'key',
        'value',
        'label',
        'suffix',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static function getActiveStats()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public static function getStatValue($key, $default = null)
    {
        $stat = self::where('key', $key)->where('is_active', true)->first();
        return $stat ? $stat->value . $stat->suffix : $default;
    }
}
