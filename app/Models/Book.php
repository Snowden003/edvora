<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'author',
        'category_id',
        'cover_image',
        'pdf_file',
        'is_published',
        'download_count',
        'view_count',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'download_count' => 'integer',
        'view_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $book): void {
            if (empty($book->slug)) {
                $book->slug = Str::slug($book->title);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $q) use ($term): void {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('author', 'like', "%{$term}%");
        });
    }

    public function coverUrl(): string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }

        return asset('assets/images/book-cover-placeholder.svg');
    }

    public function pdfUrl(): string
    {
        return asset('storage/' . $this->pdf_file);
    }

    public function downloadFileName(): string
    {
        $base = Str::slug($this->title);
        return "{$base}.pdf";
    }
}
