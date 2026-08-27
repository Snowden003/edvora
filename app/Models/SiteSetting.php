<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type', 'label'];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::remember('site_settings', 60 * 60, function () {
            return static::all()->pluck('value', 'key');
        });

        return $settings->get($key, $default);
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget('site_settings');
    }

    /**
     * Get all settings as a collection keyed by key.
     */
    public static function allCached(): \Illuminate\Support\Collection
    {
        return Cache::remember('site_settings', 60 * 60, function () {
            return static::all()->pluck('value', 'key');
        });
    }

    /**
     * Get all settings in a specific group.
     */
    public static function getGroup(string $group): \Illuminate\Support\Collection
    {
        return static::where('group', $group)->pluck('value', 'key');
    }

    /**
     * Clear the settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('site_settings');
    }
}
