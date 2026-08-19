<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Notification types constants
    const TYPE_COURSE_REMINDER = 'course_reminder';
    const TYPE_ADMIN_MESSAGE = 'admin_message';
    const TYPE_STUDENT_MESSAGE = 'student_message';
    const TYPE_MEETING_STATUS = 'meeting_status';
    const TYPE_EXAM_PUBLISHED = 'exam_published';
    const TYPE_CLASS_STARTED = 'class_started';
    const TYPE_DOCUMENT_UPLOADED = 'document_uploaded';
    const TYPE_CLASS_NOTE_ADDED = 'class_note_added';
    const TYPE_ENROLLMENT_REQUEST = 'enrollment_request';
    const TYPE_POINTS_EARNED = 'points_earned';
    const TYPE_POINTS_DEDUCTED = 'points_deducted';

    public function getIconAttribute()
    {
        return match($this->type) {
            self::TYPE_COURSE_REMINDER => 'bi bi-calendar-event-fill text-primary',
            self::TYPE_ADMIN_MESSAGE => 'bi bi-shield-fill-check text-warning',
            self::TYPE_STUDENT_MESSAGE => 'bi bi-person-fill text-info',
            self::TYPE_MEETING_STATUS => 'bi bi-camera-video-fill text-success',
            self::TYPE_EXAM_PUBLISHED => 'bi bi-pencil-square text-danger',
            self::TYPE_CLASS_STARTED => 'bi bi-camera-video-fill text-success',
            self::TYPE_DOCUMENT_UPLOADED => 'bi bi-file-earmark-arrow-down-fill text-primary',
            self::TYPE_CLASS_NOTE_ADDED => 'bi bi-sticky-fill text-info',
            self::TYPE_ENROLLMENT_REQUEST => 'bi bi-person-plus-fill text-success',
            self::TYPE_POINTS_EARNED => 'bi bi-plus-circle-fill text-success',
            self::TYPE_POINTS_DEDUCTED => 'bi bi-dash-circle-fill text-danger',
            default => 'bi bi-bell-fill text-secondary',
        };
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            self::TYPE_COURSE_REMINDER => 'Course Reminder',
            self::TYPE_ADMIN_MESSAGE => 'Admin Message',
            self::TYPE_STUDENT_MESSAGE => 'Student Message',
            self::TYPE_MEETING_STATUS => 'Meeting Update',
            self::TYPE_EXAM_PUBLISHED => 'New Exam',
            self::TYPE_CLASS_STARTED => 'Class Started',
            self::TYPE_DOCUMENT_UPLOADED => 'New Document',
            self::TYPE_CLASS_NOTE_ADDED => 'New Class Note',
            self::TYPE_ENROLLMENT_REQUEST => 'Enrollment Request',
            self::TYPE_POINTS_EARNED => 'Points Earned',
            self::TYPE_POINTS_DEDUCTED => 'Points Deducted',
            default => 'Notification',
        };
    }
}
