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

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $enrollments = $user->enrollments()
            ->with(['course.category', 'course.lessons'])
            ->where('status', 'active')
            ->latest()
            ->take(5)
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

        return view('student.dashboard', compact(
            'user',
            'enrollments',
            'upcomingEvents',
            'leaderboard',
            'achievements',
            'activities',
            'certificates',
            'completedEnrollments',
            'stats',
            'weeklyProgress',
            'courseStats',
            'examStats',
            'notifications',
            'unreadNotifCount',
            'scoreHistory'
        ));
    }

    public function certificates()
    {
        $user = Auth::user();

        $certificates = Certificate::with('course')
            ->where('user_id', $user->id)
            ->latest('issued_at')
            ->get();

        $completedEnrollments = collect();
        if ($certificates->isEmpty()) {
            $completedEnrollments = $user->enrollments()
                ->with(['course.category'])
                ->where(function ($q) {
                    $q->where('status', 'completed')
                      ->orWhere('progress_percentage', 100);
                })
                ->latest()
                ->get();
        }

        return view('student.certificates', compact('user', 'certificates', 'completedEnrollments'));
    }

    private function getDashboardStats($user)
    {
        $totalCourses = $user->enrollments()->count();
        $completedCourses = $user->enrollments()->where(function ($q) {
            $q->where('status', 'completed')->orWhere('progress_percentage', 100);
        })->count();
        $activeCourses = $user->enrollments()->where('status', 'active')->count();

        $quizAttempts = QuizAttempt::where('user_id', $user->id);
        $totalExams = $quizAttempts->count();
        $passedExams = $quizAttempts->clone()->where('passed', true)->count();

        $currentStreak = $this->calculateStreak($user);
        $totalScore = $user->totalScore();
        $earnedPoints = $user->earnedPoints();
        $deductedPoints = $user->deductedPoints();
        $rank = $user->leaderboardRank();

        // Certificates (completed courses count as certificates)
        $certificates = $completedCourses;

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
            'certificates' => $certificates,
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
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('D'); // Mon, Tue, etc.

            $count = $user->activities()
                ->whereDate('created_at', $date)
                ->count();

            $data[] = $count;
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
            return redirect()->route('student.courses')->with('error', 'You have been removed from this course by the instructor.');
        }

        // Load relationships
        $lessons = $course->lessons()->orderBy('order')->get();
        $documents = $course->documents()->with('lesson')->orderByDesc('created_at')->get();
        $classNotes = $course->classNotes()->with('teacher')->get();
        $sessions = $course->sessions()->get();

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

        return view('student.course-learning', compact(
            'user', 'course', 'enrollment', 'lessons', 'documents',
            'classNotes', 'sessions', 'quizzes', 'quizAttempts',
            'lessonCompletion', 'stats', 'activeSession',
            'completedLessonIds', 'completedLessonsCount', 'totalLessons', 'progressPercent',
            'reviews', 'userReview', 'userReviewLikes'
        ));
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

        $session = ClassSession::where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        return response()->json([
            'is_active' => !is_null($session),
            'course_slug' => $slug,
            'course_title' => $course->title,
            'room_name' => $session ? 'Google Meet' : null,
            'room_url' => $session ? $session->meet_link : null,
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

        $query = $user->enrollments()->with(['course.category', 'course.lessons']);

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

            return $enrollment;
        });

        // Calculate average progress from actual calculated progress
        $avgProgress = $enrollments->getCollection()->avg('actual_progress') ?? 0;

        $categories = \App\Models\Category::all();

        return view('student.your-courses', compact('enrollments', 'categories', 'user', 'avgProgress'));
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
