<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionAttendance extends Model
{
    use HasFactory;

    protected $table = 'session_attendances';

    protected $fillable = [
        'session_id',
        'user_id',
        'status',
        'joined_at',
        'left_at',
        'duration_minutes',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(ClassSession::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
