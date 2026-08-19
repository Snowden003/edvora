<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'course_id', 'title', 'description', 'content', 'video_url',
        'duration_minutes', 'order', 'is_free',
    ];

    protected $casts = [
        'is_free' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
