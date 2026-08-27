<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PageContent extends Model
{
    protected $fillable = [
        'key',
        'title',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    /**
     * Get structured page content by key (cached).
     */
    public static function get(string $key, array $default = []): array
    {
        $cached = Cache::remember("page_content_{$key}", 60 * 60 * 24, function () use ($key) {
            $record = static::where('key', $key)->first();
            return $record ? $record->content : null;
        });

        return $cached ?? $default;
    }

    /**
     * Set/update page content by key.
     */
    public static function set(string $key, array $content, ?string $title = null): static
    {
        $data = ['content' => $content];
        if ($title !== null) {
            $data['title'] = $title;
        }

        $record = static::updateOrCreate(
            ['key' => $key],
            $data
        );

        Cache::forget("page_content_{$key}");

        return $record;
    }

    /**
     * Clear cache for a specific key or all page content.
     */
    public static function clearCache(?string $key = null): void
    {
        if ($key) {
            Cache::forget("page_content_{$key}");
        } else {
            $keys = static::pluck('key');
            foreach ($keys as $k) {
                Cache::forget("page_content_{$k}");
            }
        }
    }
}
