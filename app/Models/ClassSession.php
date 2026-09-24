<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SessionAttendance;

class ClassSession extends Model
{
    protected $fillable = [
        'course_id', 'lesson_id', 'started_by', 'meet_link', 'room_name', 'status',
        'is_cancelled', 'started_at', 'ended_at', 'attendees_count', 'note',
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
        'is_cancelled' => 'boolean',
    ];

    /**
     * Auto-cancel any active sessions where no students joined within 3 minutes.
     */
    public static function checkAndCloseExpiredSessions($courseId = null): int
    {
        $cutoff = now()->subMinutes(3);
        $query = static::where('status', 'active')
            ->where('started_at', '<=', $cutoff);

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        $sessions = $query->get();
        $closedCount = 0;

        foreach ($sessions as $session) {
            $hasAttendance = SessionAttendance::where('session_id', $session->id)->exists();
            if (!$hasAttendance && ($session->participants_count ?? 0) === 0) {
                $session->update([
                    'status'          => 'ended',
                    'ended_at'        => now(),
                    'attendees_count' => 0,
                    'is_cancelled'    => true,
                    'note'            => $session->note ?: 'Cancelled automatically: No students joined within 3 minutes.',
                ]);
                $closedCount++;
            }
        }

        return $closedCount;
    }

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

    public function attendances()
    {
        return $this->hasMany(SessionAttendance::class, 'session_id');
    }
}
