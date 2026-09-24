<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherReferralRecord extends Model
{
    protected $fillable = [
        'teacher_referral_id',
        'teacher_id',
        'course_id',
        'user_id',
        'status',
        'registered_at',
        'enrolled_at',
        'ip_address',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'enrolled_at'   => 'datetime',
    ];

    public function referral(): BelongsTo
    {
        return $this->belongsTo(TeacherReferral::class, 'teacher_referral_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
