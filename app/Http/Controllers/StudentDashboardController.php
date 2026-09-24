<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Event;
use App\Models\Point;
use App\Models\QuizAttempt;
use App\Models\SessionAttendance;
use App\Models\User;
use App\Services\ScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Auto-close any expired courses (cached throttle to avoid table locks on every request)
        if (\Illuminate\Support\Facades\Cache::add('cron_close_expired_courses', 1, 600)) {
            \App\Models\Course::closeExpiredCourses();
        }

        $enrollments = $user->enrollments()
            ->with(['course.category', 'course.lessons'])
            ->where('status', 'active')
            ->latest()
            ->take(5)
            ->get();

        $bannedEnrollments = $user->enrollments()
            ->with(['course.teacher'])
            ->where('status', 'banned')
            ->get();

        // Calculate real progress from CompletedLesson for each enrollment
        $courseIds   = $enrollments->pluck('course_id')->filter()->unique()->values();
        $lessonIds   = $enrollments->flatMap(fn($e) => $e->course->lessons->pluck('id'))->unique();
        $completedIds = \App\Models\CompletedLesson::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessonIds)
            ->pluck('lesson_id')
            ->flip();

        foreach ($enrollments as $enrollment) {
            $total = $enrollment->course->lessons->count();
            if ($total === 0) {
                $enrollment->progress_percentage = 0;
                continue;
            }
            $done = $enrollment->course->lessons->filter(
                fn($l) => $completedIds->has($l->id)
            )->count();
            $enrollment->progress_percentage = (int) round(($done / $total) * 100);
        }

        $upcomingEvents = Event::where('start_date', '>=', now())
            ->orderBy('start_date')
            ->take(3)
            ->get();

        $leaderboard = self::getLeaderboardQuery()->take(5)->get();

        $achievements = $user->achievements()->get();

        $activities = $user->activities()
            ->where('type', '!=', 'test_activity')
            ->where('message', 'not like', 'Test%')
            ->latest()
            ->take(5)
            ->get();

        // Certificates from DB; fall back to completed enrollments
        $certificates = Certificate::with('course')
            ->where('user_id', $user->id)
            ->latest('issued_at')
            ->get();

        if ($certificates->isEmpty()) {
            $completedEnrollments = $user->enrollments()
                ->with('course')
                ->where(function ($q) {
                    $q->where('status', 'completed')
                      ->orWhere('progress_percentage', 100);
                })
                ->latest()
                ->get();
        } else {
            $completedEnrollments = collect();
        }

        // Statistics for dashboard
        $stats = $this->getDashboardStats($user);

        // Weekly progress data for chart
        $weeklyProgress = $this->getWeeklyProgress($user);

        // Course completion data
        $courseStats = $this->getCourseStats($user);

        // Exam stats
        $examStats = $this->getExamStats($user);

        // Notifications - Show only 5 recent
        $notifications = \App\Models\Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Recent scoring history
        $scoreHistory = $user->points()->take(10)->get();
        $unreadNotifCount = \App\Models\Notification::where('user_id', $user->id)
            ->unread()
            ->count();

        $enrollmentsList = $enrollments->map(function ($enrollment) {
            $course = $enrollment->course;
            return [
                'id' => $enrollment->id,
                'status' => $enrollment->status,
                'progress_percentage' => (int) $enrollment->progress_percentage,
                'course' => [
                    'id' => $course?->id,
                    'title' => $course?->title,
                    'slug' => $course?->slug,
                    'duration_weeks' => $course?->duration_weeks ?? 'Self-paced',
                    'category' => $course?->category ? ['name' => $course->category->name] : null,
                    'thumbnail' => $course?->thumbnail ? (str_starts_with($course->thumbnail, 'http') ? $course->thumbnail : asset('storage/' . $course->thumbnail)) : null,
                ],
            ];
        });

        $bannedList = $bannedEnrollments->map(function ($banned) {
            return [
                'id' => $banned->id,
                'course_title' => $banned->course?->title,
                'teacher_name' => $banned->course?->teacher?->name ?? 'Instructor',
            ];
        });

        $eventsList = $upcomingEvents->map(fn($e) => [
            'id' => $e->id,
            'title' => $e->title,
            'start_date' => $e->start_date ? $e->start_date->format('M d, Y \a\t g:i A') : '',
            'type' => $e->type,
        ]);

        $leaderboardList = $leaderboard->map(fn($entry, $idx) => [
            'rank' => $idx + 1,
            'user_id' => $entry->user_id,
            'name' => $entry->user?->name,
            'avatar' => $entry->user?->publicAvatarUrl(),
            'xp' => (int) $entry->xp,
            'is_me' => $entry->user_id === $user->id,
        ]);

        $achievementsList = $achievements->map(fn($a) => [
            'id' => $a->id,
            'title' => $a->title,
            'description' => $a->description,
            'icon' => $a->icon,
            'earned_at' => optional($a->pivot->earned_at ?? null)?->format('M d, Y') ?? now()->format('M d, Y'),
        ]);

        $certsList = $certificates->map(fn($c) => [
            'id' => $c->id,
            'title' => $c->title,
            'issued_at' => $c->issued_at ? $c->issued_at->format('M d, Y') : $c->created_at->format('M d, Y'),
            'course_title' => $c->course?->title,
        ]);

        $completedList = $completedEnrollments->map(fn($e) => [
            'id' => $e->id,
            'course_title' => $e->course?->title,
        ]);

        $activitiesList = $activities->map(fn($a) => [
            'id' => $a->id,
            'message' => $a->message,
            'type' => $a->type,
            'created_at' => $a->created_at?->diffForHumans(),
        ]);

        $notificationsList = $notifications->map(fn($n) => [
            'id' => $n->id,
            'title' => $n->title,
            'message' => $n->message,
            'is_read' => (bool) $n->is_read,
            'created_at' => $n->created_at?->diffForHumans(),
        ]);

        return Inertia::render('Student/Dashboard', [
            'stats' => $stats,
            'level' => $user->level(),
            'enrollments' => $enrollmentsList,
            'bannedEnrollments' => $bannedList,
            'upcomingEvents' => $eventsList,
            'leaderboard' => $leaderboardList,
            'achievements' => $achievementsList,
            'certificates' => $certsList,
            'completedEnrollments' => $completedList,
            'weeklyProgress' => $weeklyProgress,
            'courseStats' => $courseStats,
            'examStats' => $examStats,
            'activities' => $activitiesList,
            'notifications' => $notificationsList,
            'unreadNotifCount' => $unreadNotifCount,
        ]);
    }

    public function certificates()
    {
        $user = Auth::user();

        $certificates = Certificate::with('course.category')
            ->where('user_id', $user->id)
            ->latest('issued_at')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'certificate_number' => $c->certificate_number ?? ('EDV-' . str_pad($c->id, 6, '0', STR_PAD_LEFT)),
                'issued_at' => $c->issued_at ? $c->issued_at->format('F d, Y') : $c->created_at->format('F d, Y'),
                'course_title' => $c->course?->title ?? 'Certificate of Completion',
                'category' => $c->course?->category?->name ?? 'General',
            ]);

        $completedEnrollments = collect();
        if ($certificates->isEmpty()) {
            $completedEnrollments = $user->enrollments()
                ->with(['course.category'])
                ->where(function ($q) {
                    $q->where('status', 'completed')
                      ->orWhere('progress_percentage', 100);
                })
                ->latest()
                ->get()
                ->map(fn($e) => [
                    'id' => $e->id,
                    'course_title' => $e->course?->title,
                    'category' => $e->course?->category?->name ?? 'General',
                    'completed_at' => $e->updated_at ? $e->updated_at->format('F d, Y') : now()->format('F d, Y'),
                ]);
        }

        return Inertia::render('Student/Certificates', [
            'certificates' => $certificates,
            'completedEnrollments' => $completedEnrollments,
        ]);
    }

    private function getDashboardStats($user)
    {
        $enrollmentCounts = $user->enrollments()
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'completed' OR progress_percentage >= 100 THEN 1 ELSE 0 END) as completed
            ")
            ->first();

        $totalCourses = (int) ($enrollmentCounts->total ?? 0);
        $completedCourses = (int) ($enrollmentCounts->completed ?? 0);
        $activeCourses = max(0, $totalCourses - $completedCourses);

        $quizCounts = QuizAttempt::where('user_id', $user->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN passed = 1 THEN 1 ELSE 0 END) as passed
            ")
            ->first();

        $totalExams = (int) ($quizCounts->total ?? 0);
        $passedExams = (int) ($quizCounts->passed ?? 0);

        $currentStreak = $this->calculateStreak($user);
        $totalScore = $user->totalScore();
        $earnedPoints = $user->earnedPoints();
        $deductedPoints = $user->deductedPoints();
        $rank = $user->leaderboardRank();

        return [
            'total_courses' => $totalCourses,
            'completed_courses' => $completedCourses,
            'active_courses' => $activeCourses,
            'total_exams' => $totalExams,
            'passed_exams' => $passedExams,
            'current_streak' => $currentStreak,
            'total_xp' => $totalScore,
            'total_score' => $totalScore,
            'earned_points' => $earnedPoints,
            'deducted_points' => $deductedPoints,
            'rank' => $rank,
            'certificates' => $completedCourses,
            'completion_rate' => $totalCourses > 0 ? round(($completedCourses / $totalCourses) * 100) : 0,
        ];
    }

    private function calculateStreak($user)
    {
        // Get activities from last 30 days grouped by date
        $activities = $user->activities()
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'))
            ->distinct()
            ->pluck('date')
            ->toArray();

        if (empty($activities)) {
            return 0;
        }

        $activityDates = array_map(fn($date) => strtotime($date), $activities);
        rsort($activityDates);

        $streak = 0;
        $today = strtotime(date('Y-m-d'));
        $yesterday = strtotime('-1 day', $today);

        // Check if user was active today or yesterday
        $lastActivity = $activityDates[0];
        if ($lastActivity != $today && $lastActivity != $yesterday) {
            return 0;
        }

        // Calculate streak
        $expectedDate = $lastActivity;
        foreach ($activityDates as $date) {
            if ($date == $expectedDate || $date == strtotime('+1 day', $expectedDate)) {
                $streak++;
                $expectedDate = strtotime('-1 day', max($date, $expectedDate));
            } elseif ($date < strtotime('-1 day', $expectedDate)) {
                break;
            }
        }

        return $streak;
    }

    // Log daily activity for streak (only once per day)
    private function logActivity($user, $type, $icon = 'bi-circle', $message = null, $color = '#6366f1')
    {
        // Check if user already has activity today
        $todayActivity = $user->activities()
            ->whereDate('created_at', now()->toDateString())
            ->first();

        // If no activity today, create one
        if (!$todayActivity) {
            return $user->activities()->create([
                'type'    => $type,
                'icon'    => $icon,
                'message' => $message ?? ucfirst(str_replace('_', ' ', $type)),
                'color'   => $color,
            ]);
        }

        return null;
    }

    private function getWeeklyProgress($user)
    {
        $startDate = now()->subDays(6)->startOfDay();
        $activities = $user->activities()
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        $labels = [];
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $labels[] = $d->format('D');
            $data[] = (int) ($activities[$d->toDateString()] ?? 0);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getCourseStats($user)
    {
        $completed = $user->enrollments()->where('progress_percentage', 100)->count();
        $inProgress = $user->enrollments()
            ->where('progress_percentage', '>', 0)
            ->where('progress_percentage', '<', 100)
            ->count();
        $notStarted = $user->enrollments()->where('progress_percentage', 0)->count();

        return [
            'completed' => $completed,
            'in_progress' => $inProgress,
            'not_started' => $notStarted,
        ];
    }

    private function getExamStats($user)
    {
        $attempts = QuizAttempt::where('user_id', $user->id);

        return [
            'total' => $attempts->count(),
            'passed' => $attempts->clone()->where('passed', true)->count(),
            'failed' => $attempts->clone()->where('passed', false)->count(),
            'avg_score' => $attempts->clone()->avg('score') ?? 0,
        ];
    }

    public function courseLearning($slug)
    {
        $user = Auth::user();
        $course = \App\Models\Course::where('slug', $slug)->with('teacher')->firstOrFail();

        // Make sure user is enrolled and not banned
        $enrollment = $user->enrollments()->where('course_id', $course->id)->firstOrFail();

        if ($enrollment->status === 'banned') {
            return redirect()->route('student.courses')->with('error', 'Your access to this course has been restricted by Instructor ' . ($course->teacher?->name ?? 'Instructor') . '. You cannot access this course dashboard.');
        }

        // Load relationships
        $lessons = $course->lessons()->orderBy('order')->get();
        $documents = $course->documents()->with('lesson')->orderByDesc('created_at')->get();
        $classNotes = $course->classNotes()->with('teacher')->get();
        $sessions = $course->sessions()->get();

        // Clean up any sessions older than 3 minutes without attendance
        \App\Models\ClassSession::checkAndCloseExpiredSessions($course->id);

        // Get active session for live class join (with lesson info)
        $activeSession = \App\Models\ClassSession::where('course_id', $course->id)
            ->where('status', 'active')
            ->with('lesson')
            ->latest()
            ->first();
        $quizzes = $course->quizzes()->withCount('questions')->get();
        $quizAttempts = QuizAttempt::where('user_id', $user->id)
            ->whereIn('quiz_id', $quizzes->pluck('id'))
            ->get()
            ->keyBy('quiz_id');

        // Get student's completed lessons for this course (FIRST - so we have accurate data)
        $completedLessonIds = \App\Models\CompletedLesson::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->pluck('lesson_id')
            ->toArray();

        // Calculate progress based on actual completed lessons
        $totalLessons = $lessons->count();
        $completedLessonsCount = count($completedLessonIds);
        $progressPercent = $totalLessons > 0 ? round(($completedLessonsCount / $totalLessons) * 100) : 0;

        // Build lesson completion map
        $lessonCompletion = [];
        foreach ($lessons as $index => $lesson) {
            $lessonCompletion[$lesson->id] = in_array($lesson->id, $completedLessonIds);
        }

        // Calculate total learning time for this course (total time teacher has taught)
        $totalLearningMinutes = 0;
        foreach ($sessions as $session) {
            if ($session->started_at && $session->ended_at) {
                $totalLearningMinutes += (int) $session->started_at->diffInMinutes($session->ended_at);
            } elseif ($session->started_at && $session->status === 'active') {
                // For active sessions, calculate duration up to now
                $totalLearningMinutes += (int) $session->started_at->diffInMinutes(now());
            }
        }

        // Convert to hours and minutes
        $totalLearningHours = floor($totalLearningMinutes / 60);
        $remainingMinutes = $totalLearningMinutes % 60;

        // Stats - use calculated progress from actual completed lessons
        $stats = [
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessonsCount,
            'total_documents' => $documents->count(),
            'total_notes' => $classNotes->count(),
            'total_sessions' => $sessions->count(),
            'total_quizzes' => $quizzes->count(),
            'progress' => $progressPercent,
            'total_learning_hours' => $totalLearningHours,
            'total_learning_minutes' => $remainingMinutes,
            'total_learning_minutes_raw' => $totalLearningMinutes,
        ];

        // Reviews
        $reviews = \App\Models\Review::with('user')
            ->where('course_id', $course->id)
            ->latest()
            ->get();

        $userReview = $reviews->firstWhere('user_id', $user->id);

        // Get current user's likes on reviews
        $userReviewLikes = \App\Models\ReviewLike::where('user_id', $user->id)
            ->whereIn('review_id', $reviews->pluck('id'))
            ->pluck('type', 'review_id');

        $courseData = [
            'id' => $course->id,
            'title' => $course->title,
            'slug' => $course->slug,
            'description' => $course->description,
            'level' => $course->level,
            'category' => $course->category ? ['id' => $course->category->id, 'name' => $course->category->name] : null,
            'teacher' => $course->teacher ? [
                'id' => $course->teacher->id,
                'name' => $course->teacher->name,
                'avatar' => method_exists($course->teacher, 'publicAvatarUrl') ? $course->teacher->publicAvatarUrl() : null,
            ] : null,
            'schedule' => [
                'start_time' => $course->primary_class_start ? \Carbon\Carbon::parse($course->primary_class_start)->format('h:i A') : null,
                'end_time' => $course->primary_class_end ? \Carbon\Carbon::parse($course->primary_class_end)->format('h:i A') : null,
                'days' => $course->primary_class_days ?? [],
                'start_date' => $course->start_date?->format('Y/m/d'),
                'end_date' => $course->end_date?->format('Y/m/d'),
            ],
        ];

        $lessonsData = $lessons->map(fn($l) => [
            'id' => $l->id,
            'title' => $l->title,
            'description' => $l->description,
            'content' => $l->content,
            'order' => $l->order,
            'duration_minutes' => $l->duration_minutes,
            'is_free' => (bool) $l->is_free,
            'is_completed' => in_array($l->id, $completedLessonIds),
            'documents' => $documents->where('lesson_id', $l->id)->map(fn($d) => [
                'id' => $d->id,
                'title' => $d->title ?: $d->file_name,
                'file_name' => $d->file_name,
                'file_path' => asset('storage/' . $d->file_path),
            ])->values(),
        ]);

        $documentsData = $documents->map(fn($d) => [
            'id' => $d->id,
            'title' => $d->title ?: $d->file_name,
            'file_name' => $d->file_name,
            'file_path' => asset('storage/' . $d->file_path),
            'file_size' => $d->file_size,
            'lesson_id' => $d->lesson_id,
            'lesson_title' => $d->lesson?->title,
            'created_at' => $d->created_at?->diffForHumans(),
        ]);

        $classNotesData = $classNotes->map(fn($n) => [
            'id' => $n->id,
            'title' => $n->title,
            'content' => $n->content,
            'teacher_name' => $n->teacher?->name ?? 'Instructor',
            'created_at' => $n->created_at?->diffForHumans(),
        ]);

        $sessionsData = $sessions->map(fn($s) => [
            'id' => $s->id,
            'title' => $s->title ?? 'جلسه درسی',
            'status' => $s->status,
            'room_name' => $s->room_name,
            'meet_link' => $s->meet_link,
            'started_at' => $s->started_at?->format('Y/m/d H:i'),
            'ended_at' => $s->ended_at?->format('Y/m/d H:i'),
            'duration' => $s->started_at && $s->ended_at ? $s->started_at->diffInMinutes($s->ended_at) : null,
        ]);

        $quizzesData = $quizzes->map(function($q) use ($quizAttempts) {
            $att = $quizAttempts->get($q->id);
            return [
                'id' => $q->id,
                'title' => $q->title,
                'description' => $q->description,
                'duration_minutes' => $q->duration_minutes ?? 30,
                'xp_reward' => $q->xp_reward ?? 50,
                'questions_count' => $q->questions_count ?? 0,
                'attempt' => $att ? [
                    'score' => $att->score,
                    'total_points' => $att->total_points,
                    'passed' => (bool) $att->passed,
                    'earned_xp' => $att->earned_xp,
                ] : null,
            ];
        });

        $activeSessionData = $activeSession ? [
            'id' => $activeSession->id,
            'title' => $activeSession->title,
            'room_name' => $activeSession->room_name,
            'meet_link' => $activeSession->meet_link,
            'lesson' => $activeSession->lesson ? [
                'id' => $activeSession->lesson->id,
                'title' => $activeSession->lesson->title,
                'order' => $activeSession->lesson->order,
            ] : null,
        ] : null;

        $reviewsData = $reviews->map(fn($r) => [
            'id' => $r->id,
            'rating' => $r->rating,
            'comment' => $r->comment,
            'user_name' => $r->user?->name ?? 'Student',
            'user_avatar' => method_exists($r->user, 'publicAvatarUrl') ? $r->user->publicAvatarUrl() : null,
            'created_at' => $r->created_at?->diffForHumans(),
            'likes_count' => $r->likes_count ?? 0,
            'user_liked' => $userReviewLikes->get($r->id) ?? null,
        ]);

        return Inertia::render('Student/CourseLearning', [
            'course' => $courseData,
            'lessons' => $lessonsData,
            'documents' => $documentsData,
            'classNotes' => $classNotesData,
            'sessions' => $sessionsData,
            'quizzes' => $quizzesData,
            'activeSession' => $activeSessionData,
            'stats' => $stats,
            'reviews' => $reviewsData,
            'userReview' => $userReview ? [
                'id' => $userReview->id,
                'rating' => $userReview->rating,
                'comment' => $userReview->comment,
            ] : null,
        ]);
    }

    public function joinClass(\Illuminate\Http\Request $request, $slug)
    {
        $user   = Auth::user();
        $course = Course::where('slug', $slug)->firstOrFail();

        // Verify enrollment and check not banned
        $enrollment = $user->enrollments()->where('course_id', $course->id)->firstOrFail();

        abort_if($enrollment->status === 'banned', 403, 'You have been removed from this course.');

        // Find the active session for this course
        $session = ClassSession::where('course_id', $course->id)
            ->where('status', 'active')
            ->latest('started_at')
            ->first();

        if (is_null($session)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'No active class session currently.'], 404);
            }
            abort(404, 'No active class session currently.');
        }

        // Record attendance - joined session
        SessionAttendance::updateOrCreate(
            [
                'session_id' => $session->id,
                'user_id'    => $user->id,
            ],
            [
                'status'    => 'present',
                'joined_at' => now(),
            ]
        );

        $session->update([
            'last_participant_at' => now(),
            'participants_count'  => SessionAttendance::where('session_id', $session->id)->count(),
        ]);

        if ($request->ajax()) {
            return response()->json(['url' => $session->meet_link]);
        }

        return redirect()->away($session->meet_link);
    }

    public function leaveClass($slug)
    {
        $user   = Auth::user();
        $course = Course::where('slug', $slug)->firstOrFail();

        // Find the active session for this course
        $activeSession = ClassSession::where('course_id', function($query) use ($slug) {
            $query->select('id')->from('courses')->where('slug', $slug);
        })
        ->where('status', 'active')
        ->first();

        // Record leave time for the session
        if ($activeSession) {
            $attendance = \App\Models\SessionAttendance::where('session_id', $activeSession->id)
                ->where('user_id', $user->id)
                ->whereNull('left_at')
                ->latest()
                ->first();

            if ($attendance) {
                $attendance->left_at = now();
                // Calculate duration in minutes
                if ($attendance->joined_at) {
                    $attendance->duration_minutes = $attendance->joined_at->diffInMinutes(now());
                }
                $attendance->status = 'absent';
                $attendance->save();
            }
        }

        // Check if there's still an active session
        $activeSession = ClassSession::where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        if (!$activeSession) {
            // Class has ended, show message
            return redirect()->route('student.courses.learn', $slug)
                ->with('info', 'The class has been ended by the teacher.');
        }

        return redirect()->route('student.courses.learn', $slug);
    }

    public function checkActiveClass()
    {
        $user = Auth::user();

        // Close any abandoned sessions older than 3 minutes without attendance
        ClassSession::checkAndCloseExpiredSessions();

        $enrolledCourseIds = $user->enrollments()
            ->where('status', 'active')
            ->pluck('course_id');

        $session = ClassSession::whereIn('course_id', $enrolledCourseIds)
            ->where('status', 'active')
            ->with('course')
            ->first();

        if ($session) {
            return response()->json([
                'has_active_class' => true,
                'course_slug'  => $session->course->slug ?? null,
                'course_title' => $session->course->title ?? null,
                'room_name'    => 'Google Meet',
                'room_url'     => $session->meet_link,
                'join_url'     => route('student.courses.sessions.join', $session->course->slug),
                'started_at'   => $session->started_at,
            ]);
        }

        return response()->json([
            'has_active_class' => false,
            'course_slug'  => null,
            'course_title' => null,
        ]);
    }

    public function checkSession($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        // Close any abandoned sessions older than 3 minutes without attendance
        ClassSession::checkAndCloseExpiredSessions($course->id);

        $session = ClassSession::where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        return response()->json([
            'is_active' => !is_null($session),
            'course_slug' => $slug,
            'course_title' => $course->title,
            'room_name' => $session ? 'Google Meet' : null,
            'room_url' => $session ? $session->meet_link : null,
            'join_url' => $session ? route('student.courses.sessions.join', $course->slug) : null,
            'started_at' => $session ? $session->started_at : null,
        ]);
    }

    public function getClassNotes($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        $notes = \App\Models\ClassNote::where('course_id', $course->id)
            ->orderBy('class_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($note) {
                return [
                    'id' => $note->id,
                    'title' => $note->title,
                    'content' => $note->content,
                    'class_date' => $note->class_date ? $note->class_date->format('M d, Y') : null,
                    'created_at' => $note->created_at->format('M d, Y'),
                    'teacher_name' => $note->teacher ? $note->teacher->name : 'Instructor',
                ];
            });

        return response()->json([
            'success' => true,
            'notes' => $notes,
            'count' => $notes->count(),
        ]);
    }

    public function yourCourses(Request $request)
    {
        $user = Auth::user();

        // Automatically sync any courses whose time/end_date has expired
        \App\Models\Course::closeExpiredCourses();

        $baseQuery = $user->enrollments()->with(['course.category', 'course.lessons']);

        // Calculate counts for the tabs
        $allCount = (clone $baseQuery)->count();
        $bannedCount = (clone $baseQuery)->where('status', 'banned')->count();
        $completedCount = (clone $baseQuery)->where('status', '!=', 'banned')->where(function ($q) {
            $q->where('status', 'completed')
              ->orWhere('progress_percentage', 100)
              ->orWhereHas('course', function ($c) {
                  $c->whereIn('status', ['completed', 'archived'])
                    ->orWhere(fn($sub) => $sub->whereNotNull('end_date')->where('end_date', '<', now()->startOfDay()));
              });
        })->count();

        $inProgressCount = (clone $baseQuery)->where('status', 'active')
            ->where('progress_percentage', '<', 100)
            ->whereDoesntHave('course', function ($c) {
                $c->whereIn('status', ['completed', 'archived'])
                  ->orWhere(fn($sub) => $sub->whereNotNull('end_date')->where('end_date', '<', now()->startOfDay()));
            })->count();

        $query = clone $baseQuery;

        $statusFilter = $request->get('status');
        if ($statusFilter === 'completed') {
            $query->where('status', '!=', 'banned')->where(function ($q) {
                $q->where('status', 'completed')
                  ->orWhere('progress_percentage', 100)
                  ->orWhereHas('course', function ($c) {
                      $c->whereIn('status', ['completed', 'archived'])
                        ->orWhere(fn($sub) => $sub->whereNotNull('end_date')->where('end_date', '<', now()->startOfDay()));
                  });
            });
        } elseif ($statusFilter === 'in_progress' || $statusFilter === 'active') {
            $query->where('status', 'active')
                  ->where('progress_percentage', '<', 100)
                  ->whereDoesntHave('course', function ($c) {
                      $c->whereIn('status', ['completed', 'archived'])
                        ->orWhere(fn($sub) => $sub->whereNotNull('end_date')->where('end_date', '<', now()->startOfDay()));
                  });
        } elseif ($statusFilter === 'banned') {
            $query->where('status', 'banned');
        }

        if ($search = $request->get('search')) {
            $query->whereHas('course', fn($q) => $q->where('title', 'like', "%{$search}%"));
        }

        if ($category = $request->get('category')) {
            $query->whereHas('course.category', fn($q) => $q->where('name', $category));
        }

        $enrollments = $query->latest()->paginate(9)->withQueryString();

        // Calculate actual progress for each enrollment based on completed lessons
        $enrollments->getCollection()->transform(function ($enrollment) use ($user) {
            $course = $enrollment->course;
            $lessons = $course->lessons ?? collect();
            $totalLessons = $lessons->count();

            if ($totalLessons > 0) {
                // Get completed lessons for this course
                $completedLessonIds = \App\Models\CompletedLesson::where('user_id', $user->id)
                    ->whereIn('lesson_id', $lessons->pluck('id'))
                    ->pluck('lesson_id')
                    ->toArray();

                $completedLessonsCount = count($completedLessonIds);
                $actualProgress = round(($completedLessonsCount / $totalLessons) * 100);

                // Set calculated values as attributes
                $enrollment->actual_progress = $actualProgress;
                $enrollment->completed_lessons_count = $completedLessonsCount;
                $enrollment->total_lessons_count = $totalLessons;
            } else {
                $enrollment->actual_progress = 0;
                $enrollment->completed_lessons_count = 0;
                $enrollment->total_lessons_count = 0;
            }

            // Flag if course is completed
            $enrollment->is_course_completed = ($enrollment->status === 'completed' || $enrollment->actual_progress >= 100 || ($course && $course->isCompleted()));

            if ($course) {
                $enrollment->course->thumbnail_url = $course->thumbnail
                    ? (str_starts_with($course->thumbnail, 'http') ? $course->thumbnail : asset('storage/' . $course->thumbnail))
                    : null;
            }

            return $enrollment;
        });

        // Calculate average progress from actual calculated progress
        $avgProgress = $enrollments->getCollection()->avg('actual_progress') ?? 0;

        $categories = \App\Models\Category::select('id', 'name', 'slug')->get();

        return Inertia::render('Student/YourCourses', [
            'enrollments' => $enrollments,
            'categories' => $categories,
            'avgProgress' => round($avgProgress),
            'counts' => [
                'all' => $allCount,
                'inProgress' => $inProgressCount,
                'completed' => $completedCount,
                'banned' => $bannedCount,
            ],
            'filters' => [
                'status' => $statusFilter ?? '',
                'search' => $request->get('search', ''),
                'category' => $request->get('category', ''),
            ],
        ]);
    }

    public function storeReview(Request $request, $slug)
    {
        $user   = Auth::user();
        $course = Course::where('slug', $slug)->firstOrFail();

        // Ensure enrolled
        $user->enrollments()->where('course_id', $course->id)->firstOrFail();

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:2000',
        ]);

        // Check if user already reviewed this course
        $existing = \App\Models\Review::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            $existing->update([
                'rating'  => $request->rating,
                'comment' => $request->comment,
            ]);
            $message = 'Review updated successfully.';
        } else {
            \App\Models\Review::create([
                'user_id'     => $user->id,
                'course_id'   => $course->id,
                'rating'      => $request->rating,
                'comment'     => $request->comment,
                'is_approved' => true,
            ]);
            $message = 'Review submitted successfully.';
        }

        // Update course rating stats
        $avgRating    = \App\Models\Review::where('course_id', $course->id)->avg('rating');
        $totalReviews = \App\Models\Review::where('course_id', $course->id)->count();
        $course->update([
            'rating'        => round($avgRating, 1),
            'total_reviews' => $totalReviews,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => $message]);
        }

        return back()->with('review_success', $message);
    }

    public function toggleReviewLike(Request $request, $reviewId)
    {
        $user   = Auth::user();
        $review = \App\Models\Review::findOrFail($reviewId);

        $request->validate([
            'type' => 'required|in:like,dislike',
        ]);

        $existing = \App\Models\ReviewLike::where('user_id', $user->id)
            ->where('review_id', $review->id)
            ->first();

        if ($existing) {
            if ($existing->type === $request->type) {
                // Same type → remove (toggle off)
                $existing->delete();
                $action = 'removed';
            } else {
                // Different type → switch
                $existing->update(['type' => $request->type]);
                $action = 'switched';
            }
        } else {
            \App\Models\ReviewLike::create([
                'user_id'   => $user->id,
                'review_id' => $review->id,
                'type'      => $request->type,
            ]);
            $action = 'added';
        }

        // Recalculate counts
        $review->update([
            'likes_count'    => $review->likes()->where('type', 'like')->count(),
            'dislikes_count' => $review->likes()->where('type', 'dislike')->count(),
        ]);

        return response()->json([
            'status'         => $action,
            'likes_count'    => $review->likes_count,
            'dislikes_count' => $review->dislikes_count,
        ]);
    }

    /**
     * Build a leaderboard query from aggregated point totals.
     */
    public static function getLeaderboardQuery(?int $courseId = null, ?\Carbon\Carbon $from = null, ?\Carbon\Carbon $to = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = Point::query()
            ->selectRaw('user_id, SUM(amount) as xp')
            ->with('user')
            ->groupBy('user_id');

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        } elseif ($from) {
            $query->where('created_at', '>=', $from);
        } elseif ($to) {
            $query->where('created_at', '<=', $to);
        }

        return $query
            ->orderByDesc('xp');
    }
}
