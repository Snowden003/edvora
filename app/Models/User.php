<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'status', 'avatar', 'cover_image', 'bio', 'xp', 'phone', 'department', 'experience_years', 'cv_path', 'tazkira_image', 'identity_status', 'identity_rejection_reason'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'status', 'avatar', 'cover_image', 'bio', 'xp',
        'phone', 'department', 'experience_years', 'cv_path',
        'tazkira_image', 'identity_status', 'identity_rejection_reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistedCourses()
    {
        return $this->belongsToMany(Course::class, 'wishlists')->withTimestamps();
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
                    ->withPivot('earned_at')
                    ->withTimestamps();
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function points()
    {
        return $this->hasMany(Point::class, 'user_id')->latest();
    }

    public function leaderboard()
    {
        return $this->hasOne(Leaderboard::class);
    }

    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function registeredEvents()
    {
        return $this->belongsToMany(Event::class, 'event_registrations')
            ->withPivot('status', 'created_at')
            ->withTimestamps();
    }

    public function hasSubmittedTeacherProfile(): bool
    {
        return $this->teacher()->exists();
    }

    public function isPendingApproval(): bool
    {
        return $this->status === 'pending';
    }

    public function isIdentityApproved(): bool
    {
        return $this->identity_status === 'approved';
    }

    public function isIdentityPending(): bool
    {
        return $this->identity_status === 'pending';
    }

    public function hasSubmittedIdentity(): bool
    {
        return in_array($this->identity_status, ['pending', 'approved', 'rejected']);
    }

    public function isActiveTeacher(): bool
    {
        return $this->role === 'teacher' && $this->status === 'active';
    }

    public function dashboardRoute(): string
    {
        return match($this->role) {
            'teacher' => route('teacher.dashboard'),
            'admin'   => url('/admin-panel'),
            default   => route('student.dashboard'),
        };
    }

    public function totalScore(): int
    {
        return (int) $this->points()->sum('amount');
    }

    public function earnedPoints(): int
    {
        return (int) $this->points()->where('amount', '>', 0)->sum('amount');
    }

    public function deductedPoints(): int
    {
        return (int) abs($this->points()->where('amount', '<', 0)->sum('amount'));
    }

    public function currentScore(): int
    {
        return $this->totalScore();
    }

    public function leaderboardRank(?int $courseId = null): ?int
    {
        $rows = Point::selectRaw('user_id, SUM(amount) as total')
            ->when($courseId, fn($q) => $q->where('course_id', $courseId))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->get();

        $position = $rows->search(fn($row) => $row->user_id === $this->id);

        return $position !== false ? $position + 1 : null;
    }

    /**
     * Simple level progression based on total score.
     */
    public function level(): array
    {
        $score = $this->totalScore();
        $level = (int) floor($score / 100) + 1;
        $progress = $score % 100;
        $nextThreshold = $level * 100;

        return [
            'level' => $level,
            'progress' => $progress,
            'next_threshold' => $nextThreshold,
            'title' => match(true) {
                $level >= 10 => 'Master',
                $level >= 7 => 'Expert',
                $level >= 4 => 'Skilled',
                default => 'Beginner',
            },
        ];
    }

    public function completedLessons()
    {
        return $this->hasMany(CompletedLesson::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function publicAvatarUrl(): string
    {
        if (!$this->avatar) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?: 'Teacher') . '&size=300&background=2563eb&color=fff';
        }

        if (str_starts_with($this->avatar, 'http')) {
            return $this->avatar;
        }

        if (str_starts_with($this->avatar, '/storage/') || str_starts_with($this->avatar, 'storage/')) {
            return asset(ltrim($this->avatar, '/'));
        }

        return asset('storage/' . $this->avatar);
    }

    public function hasCompleteTeacherPublicProfile(): bool
    {
        $teacherProfile = $this->relationLoaded('teacher')
            ? $this->teacher
            : $this->teacher()->first();

        if (!$teacherProfile) {
            return false;
        }

        $hasPublishedCourses = $this->relationLoaded('courses')
            ? $this->courses->contains(fn ($course) => $course->status === 'published')
            : $this->courses()->where('status', 'published')->exists();

        return filled($this->name)
            && filled($this->department)
            && filled($this->bio)
            && mb_strlen(trim(strip_tags($this->bio))) >= 30
            && filled($teacherProfile->specialization)
            && filled($teacherProfile->expertise)
            && $hasPublishedCourses;
    }
}
