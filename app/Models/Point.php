<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected static function booted(): void
    {
        static::deleted(function (Point $point) {
            if ($point->user) {
                $total = (int) $point->user->points()->sum('amount');
                $point->user->update(['xp' => max(0, $total)]);
            }
        });

        static::updated(function (Point $point) {
            if ($point->user) {
                $total = (int) $point->user->points()->sum('amount');
                $point->user->update(['xp' => max(0, $total)]);
            }
        });
    }

    protected $fillable = [
        'user_id',
        'course_id',
        'amount',
        'reason',
        'type',
        'related_type',
        'related_id',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeEarned($query)
    {
        return $query->where('amount', '>', 0);
    }

    public function scopeDeducted($query)
    {
        return $query->where('amount', '<', 0);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    public function isPositive(): bool
    {
        return $this->amount > 0;
    }
}
