<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TeacherReferral extends Model
{
    protected $fillable = [
        'teacher_id',
        'course_id',
        'code',
        'clicks_count',
    ];

    protected $casts = [
        'clicks_count' => 'integer',
    ];

    protected $attributes = [
        'clicks_count' => 0,
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(TeacherReferralRecord::class);
    }

    public function getInviteUrlAttribute(): string
    {
        return route('referral.join', $this->code);
    }

    /**
     * Generate a unique referral code for a teacher and course.
     */
    public static function generateCode(int $teacherId, int $courseId): string
    {
        do {
            $code = 'T' . $teacherId . 'C' . $courseId . '-' . strtoupper(Str::random(5));
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
