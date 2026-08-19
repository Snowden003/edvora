<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassNote extends Model
{
    protected $fillable = ['course_id', 'teacher_id', 'class_date', 'title', 'content'];

    protected $casts = ['class_date' => 'date'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
