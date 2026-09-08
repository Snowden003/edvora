<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Event;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\Book;
use App\Models\ContactMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function index(Request $request): Response
    {
        // 1. KPI Stats Overview
        $activeStudents = User::where('role', 'student')->where('status', 'active')->count();
        $totalStudents  = User::where('role', 'student')->count();

        $activeCourses  = Course::whereIn('status', ['active', 'published'])->count();
        $totalCourses   = Course::count();

        $verifiedTeachers = Teacher::where('is_verified', true)->count();
        $totalTeachers    = Teacher::count();
        $pendingTeachersCount = Teacher::where('is_verified', false)->count();

        $activeEvents   = Event::where('status', 'active')->count();
        $totalEvents    = Event::count();

        $classes24h = ClassSession::where('started_at', '>=', Carbon::now()->subHours(24))->count();
        $eventsWeek = Event::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $totalBooks = Book::count();
        $unreadMessagesCount = ContactMessage::where('status', 'new')->orWhereNull('status')->count();

        $stats = [
            'active_students' => $activeStudents,
            'total_students'  => $totalStudents,
            'active_courses'  => $activeCourses,
            'total_courses'   => $totalCourses,
            'verified_teachers' => $verifiedTeachers,
            'total_teachers'    => $totalTeachers,
            'pending_teachers_count' => $pendingTeachersCount,
            'active_events'   => $activeEvents,
            'total_events'    => $totalEvents,
            'classes_24h'     => $classes24h,
            'events_week'     => $eventsWeek,
            'total_books'     => $totalBooks,
            'unread_messages' => $unreadMessagesCount,
        ];

        // 2. Registrations Chart (Last 12 Months)
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i));
        }

        $allRegistrations = User::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, role, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(12)->startOfMonth())
            ->groupByRaw('YEAR(created_at), MONTH(created_at), role')
            ->get();

        $chartLabels = $months->map(fn($m) => $m->format('M Y'))->toArray();

        $allData = [];
        $studentData = [];
        $teacherData = [];

        foreach ($months as $m) {
            $keyYear = $m->year;
            $keyMonth = $m->month;

            $monthRows = $allRegistrations->filter(fn($r) => (int)$r->year === $keyYear && (int)$r->month === $keyMonth);

            $allCount = $monthRows->sum('total');
            $studentCount = $monthRows->where('role', 'student')->sum('total');
            $teacherCount = $monthRows->where('role', 'teacher')->sum('total');

            $allData[] = $allCount;
            $studentData[] = $studentCount;
            $teacherData[] = $teacherCount;
        }

        $registrationsChart = [
            'labels' => $chartLabels,
            'datasets' => [
                'all' => $allData,
                'student' => $studentData,
                'teacher' => $teacherData,
            ],
        ];

        // 3. Course Enrollments Chart (Last 12 Months)
        $enrollments = Enrollment::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(12)->startOfMonth())
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->get()
            ->keyBy(fn($row) => $row->year . '-' . str_pad($row->month, 2, '0', STR_PAD_LEFT));

        $enrollmentsData = $months->map(fn($m) => $enrollments->get($m->format('Y-m'))?->total ?? 0)->toArray();

        $enrollmentsChart = [
            'labels' => $chartLabels,
            'data' => $enrollmentsData,
        ];

        // 4. Courses by Category Breakdown
        $coursesByCategory = Course::selectRaw('categories.name as category_name, COUNT(courses.id) as total')
            ->join('categories', 'categories.id', '=', 'courses.category_id')
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $categoriesChart = [
            'labels' => $coursesByCategory->pluck('category_name')->toArray(),
            'data' => $coursesByCategory->pluck('total')->toArray(),
        ];

        // 5. Teacher Status Chart
        $teachersStatusCounts = User::where('role', 'teacher')
            ->whereHas('teacher')
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $teacherStatusChart = [
            'active' => $teachersStatusCounts->get('active', 0),
            'pending' => $teachersStatusCounts->get('pending', 0),
            'rejected' => $teachersStatusCounts->get('rejected', 0),
            'suspended' => $teachersStatusCounts->get('suspended', 0),
        ];

        // 6. Live Class Sessions (Last 30 Days)
        $days = collect();
        for ($i = 29; $i >= 0; $i--) {
            $days->push(Carbon::now()->subDays($i)->startOfDay());
        }

        $sessions = ClassSession::selectRaw('DATE(started_at) as day, COUNT(*) as total')
            ->where('started_at', '>=', Carbon::now()->subDays(30)->startOfDay())
            ->groupByRaw('DATE(started_at)')
            ->get()
            ->keyBy('day');

        $sessionsLabels = $days->map(fn($d) => $d->format('M d'))->toArray();
        $sessionsData   = $days->map(fn($d) => $sessions->get($d->toDateString())?->total ?? 0)->toArray();

        $sessionsChart = [
            'labels' => $sessionsLabels,
            'data' => $sessionsData,
        ];

        // 7. Recent Pending Teachers awaiting verification
        $pendingTeachers = Teacher::with('user')
            ->where('is_verified', false)
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'user_id' => $t->user_id,
                'name' => $t->user?->name ?? 'Unknown Teacher',
                'email' => $t->user?->email ?? '-',
                'avatar' => $t->user?->avatar_url ?? null,
                'specialization' => $t->specialization ?? 'General',
                'created_at' => $t->created_at?->diffForHumans() ?? '-',
            ]);

        // 8. Recent Courses
        $recentCourses = Course::with(['category', 'teacher'])
            ->withCount('enrollments')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'slug' => $c->slug,
                'category' => $c->category?->name ?? 'Uncategorized',
                'teacher' => $c->teacher?->name ?? 'Edvora Staff',
                'status' => $c->status,
                'level' => $c->level,
                'enrollments_count' => $c->enrollments_count ?? 0,
                'created_at' => $c->created_at?->diffForHumans() ?? '-',
            ]);

        // 9. Recent Contact Messages
        $recentMessages = ContactMessage::latest()
            ->limit(5)
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'email' => $m->email,
                'subject' => $m->subject,
                'status' => $m->status ?? 'new',
                'priority' => $m->priority ?? 'normal',
                'created_at' => $m->created_at?->diffForHumans() ?? '-',
            ]);

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'registrationsChart' => $registrationsChart,
            'enrollmentsChart' => $enrollmentsChart,
            'categoriesChart' => $categoriesChart,
            'teacherStatusChart' => $teacherStatusChart,
            'sessionsChart' => $sessionsChart,
            'pendingTeachers' => $pendingTeachers,
            'recentCourses' => $recentCourses,
            'recentMessages' => $recentMessages,
        ]);
    }

    public function verifyTeacher($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->update(['is_verified' => true]);
        if ($teacher->user) {
            $teacher->user->update(['status' => 'active']);
        }
        \Illuminate\Support\Facades\Cache::forget('home:page-data:v2');
        \Illuminate\Support\Facades\Cache::forget('home:page-data:v3');

        return back()->with('success', 'استاد با موفقیت تایید و فعال شد.');
    }

    public function rejectTeacher($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->update(['is_verified' => false]);
        if ($teacher->user) {
            $teacher->user->update(['status' => 'rejected']);
        }
        \Illuminate\Support\Facades\Cache::forget('home:page-data:v2');
        \Illuminate\Support\Facades\Cache::forget('home:page-data:v3');

        return back()->with('success', 'درخواست تایید استاد رد شد.');
    }
}
