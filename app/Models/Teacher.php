<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'specialization',
        'expertise',
        'years_of_experience',
        'linkedin',
        'github',
        'website',
        'rating',
        'total_students',
        'total_courses',
        'is_verified',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
