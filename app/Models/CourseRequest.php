<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id', 'category_id', 'course_id', 'title', 'description', 'status', 'admin_notes',
    ];

    public function course()
    {
        return $this->belongsTo(\App\Models\Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(\App\Models\User::class, 'teacher_id');
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }
}
