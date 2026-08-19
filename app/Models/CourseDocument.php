<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseDocument extends Model
{
    protected $fillable = [
        'course_id', 'lesson_id', 'uploaded_by',
        'title', 'description', 'file_path', 'file_name', 'file_size',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $kb = $this->file_size / 1024;
        if ($kb < 1024) return round($kb, 1) . ' KB';
        return round($kb / 1024, 2) . ' MB';
    }
}
