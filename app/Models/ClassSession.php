<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    protected $fillable = [
        'course_id', 'lesson_id', 'started_by', 'meet_link', 'room_name', 'status',
        'started_at', 'ended_at', 'attendees_count', 'note',
        'last_participant_at', 'participants_count'
    ];

    public function getDurationAttribute(): string
    {
        if (!$this->started_at || !$this->ended_at) return '—';
        $mins = (int) $this->started_at->diffInMinutes($this->ended_at);
        if ($mins < 60) return $mins . ' min';
        return floor($mins / 60) . 'h ' . ($mins % 60) . 'm';
    }

    public function getCurrentDurationAttribute(): string
    {
        if (!$this->started_at) return '—';
        $endTime = $this->ended_at ?? now();
        $mins = (int) $this->started_at->diffInMinutes($endTime);
        if ($mins < 60) return $mins . ' min';
        return floor($mins / 60) . 'h ' . ($mins % 60) . 'm';
    }

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'last_participant_at' => 'datetime',
        'participants_count' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function starter()
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
