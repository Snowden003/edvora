<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'category_id', 'teacher_id', 'title', 'slug', 'description',
        'thumbnail', 'level', 'duration_hours', 'status', 'is_featured',
        'enrolled_count', 'rating', 'total_reviews',
        // Course Features
        'has_certificate', 'has_lifetime_access', 'has_money_back',
        'has_downloadable_resources', 'has_community_access', 'has_mobile_access',
        // Course Info badges
        'show_certificate_badge', 'show_duration_badge', 'show_students_badge',
        'show_level_badge', 'show_category_badge',
        // Class Schedule
        'primary_class_start', 'primary_class_end', 'primary_class_days', 'primary_class_note',
        'secondary_class_start', 'secondary_class_end', 'secondary_class_days', 'secondary_class_note',
        // Course Dates
        'start_date',
        'end_date',
        'started_at',
        // Auto-start Settings
        'min_students',
        'max_students',
        'auto_start_enabled',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'has_certificate' => 'boolean',
        'has_lifetime_access' => 'boolean',
        'has_money_back' => 'boolean',
        'has_downloadable_resources' => 'boolean',
        'has_community_access' => 'boolean',
        'has_mobile_access' => 'boolean',
        'show_certificate_badge' => 'boolean',
        'show_duration_badge' => 'boolean',
        'show_students_badge' => 'boolean',
        'show_level_badge' => 'boolean',
        'show_category_badge' => 'boolean',
        'primary_class_days'   => 'array',
        'secondary_class_days' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'started_at' => 'datetime',
        'auto_start_enabled' => 'boolean',
        'min_students' => 'integer',
        'max_students' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'user_id')
            ->withPivot(['id', 'status', 'progress_percentage', 'completed_at', 'created_at', 'updated_at'])
            ->withTimestamps();
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function documents()
    {
        return $this->hasMany(CourseDocument::class);
    }

    public function classNotes()
    {
        return $this->hasMany(ClassNote::class)->orderByDesc('class_date');
    }

    public function sessions()
    {
        return $this->hasMany(ClassSession::class)->orderByDesc('started_at');
    }

    /**
     * Check if course should auto-start based on enrolled students
     */
    public function shouldAutoStart(): bool
    {
        if (!$this->auto_start_enabled) {
            return false;
        }

        if ($this->started_at !== null) {
            return false;
        }

        if ($this->min_students === null) {
            return false;
        }

        return $this->enrolled_count >= $this->min_students;
    }

    /**
     * Check if course has reached maximum students
     */
    public function isFull(): bool
    {
        if ($this->max_students === null) {
            return false;
        }

        return $this->enrolled_count >= $this->max_students;
    }

    /**
     * Check if course has ended or is marked completed/archived
     */
    public function isCompleted(): bool
    {
        if (in_array($this->status, ['completed', 'archived'])) {
            return true;
        }

        if ($this->end_date) {
            $endDate = $this->end_date instanceof \Carbon\CarbonInterface
                ? $this->end_date
                : \Carbon\Carbon::parse($this->end_date);

            if ($endDate->startOfDay()->lessThan(now()->startOfDay())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Start the course
     */
    public function startCourse(): void
    {
        $this->update([
            'started_at' => now(),
            'status' => 'started',
        ]);
    }
}
