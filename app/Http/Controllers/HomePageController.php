<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Donation;
use App\Models\Event;
use App\Models\Enrollment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomePageController extends Controller
{
    public function index()
    {
        $homeData = Cache::remember('home:page-data:v2', now()->addMinutes(10), function () {
            return [
                'totalCourses' => Course::where('status', '!=', 'draft')->count(),
                'totalTeachers' => User::where('role', 'teacher')->count(),
                'totalStudents' => User::where('role', 'student')->count(),
                'completedCourses' => Enrollment::where('status', 'completed')->count(),
                'featuredCourseIds' => Course::where('status', '!=', 'draft')
                    ->where('is_featured', true)
                    ->latest()
                    ->limit(1)
                    ->pluck('id')
                    ->all(),
                'popularCourseIds' => Course::withCount(['enrollments' => function ($query) {
                        $query->where('status', 'active');
                    }])
                    ->where('status', '!=', 'draft')
                    ->orderByDesc('enrollments_count')
                    ->orderByDesc('rating')
                    ->orderByDesc('total_reviews')
                    ->latest()
                    ->limit(3)
                    ->pluck('id')
                    ->all(),
                'recentCourseIds' => Course::where('status', '!=', 'draft')
                    ->latest()
                    ->limit(6)
                    ->pluck('id')
                    ->all(),
                'upcomingEventIds' => Event::where('start_date', '>=', now())
                    ->where('status', 'active')
                    ->orderBy('start_date')
                    ->limit(3)
                    ->pluck('id')
                    ->all(),
                'approvedReviewIds' => Review::where('is_approved', true)
                    ->latest()
                    ->limit(6)
                    ->pluck('id')
                    ->all(),
                'topStudentIds' => User::where('role', 'student')
                    ->where('xp', '>', 0)
                    ->orderByDesc('xp')
                    ->limit(3)
                    ->pluck('id')
                    ->all(),
                'totalDonations' => Donation::where('status', 'completed')->sum('amount'),
                'totalSupporters' => Donation::where('status', 'completed')->distinct('email')->count(),
            ];
        });

        $totalCourses = $homeData['totalCourses'];
        $totalTeachers = $homeData['totalTeachers'];
        $totalStudents = $homeData['totalStudents'];
        $completedCourses = $homeData['completedCourses'];
        $totalDonations = $homeData['totalDonations'];
        $totalSupporters = $homeData['totalSupporters'];

        $featuredCourses = $this->loadOrderedModels(
            Course::class,
            $homeData['featuredCourseIds'],
            fn (Builder $query) => $query->with(['teacher', 'category'])
        );

        $popularCourses = $this->loadOrderedModels(
            Course::class,
            $homeData['popularCourseIds'],
            fn (Builder $query) => $query
                ->withCount(['enrollments' => function ($enrollmentsQuery) {
                    $enrollmentsQuery->where('status', 'active');
                }])
                ->with(['teacher', 'category'])
        );

        $recentCourses = $this->loadOrderedModels(
            Course::class,
            $homeData['recentCourseIds'],
            fn (Builder $query) => $query->with(['teacher', 'category'])
        );

        $upcomingEvents = $this->loadOrderedModels(
            Event::class,
            $homeData['upcomingEventIds']
        );

        $approvedReviews = $this->loadOrderedModels(
            Review::class,
            $homeData['approvedReviewIds'],
            fn (Builder $query) => $query->with(['user', 'course'])
        );

        $topStudents = $this->loadOrderedModels(
            User::class,
            $homeData['topStudentIds']
        );

        // Get courses that the logged-in student is currently learning
        $continueLearning = collect();
        if (Auth::check() && Auth::user()->role === 'student') {
            $continueLearning = Enrollment::with(['course.teacher', 'course.category'])
                ->where('user_id', Auth::id())
                ->where('status', 'active')
                ->orderBy('updated_at', 'desc')
                ->limit(4)
                ->get();
        }

        $hasTopStudents = $topStudents->isNotEmpty();

        return view('home', compact(
            'totalCourses',
            'totalTeachers',
            'totalStudents',
            'completedCourses',
            'featuredCourses',
            'popularCourses',
            'recentCourses',
            'continueLearning',
            'upcomingEvents',
            'approvedReviews',
            'hasTopStudents',
            'topStudents',
            'totalDonations',
            'totalSupporters'
        ));
    }

    private function loadOrderedModels(string $modelClass, array $ids, ?callable $configure = null): EloquentCollection
    {
        if ($ids === []) {
            return new EloquentCollection();
        }

        $query = $modelClass::query()->whereIn('id', $ids);

        if ($configure !== null) {
            $configure($query);
        }

        $modelsById = $query->get()->keyBy('id');

        return new EloquentCollection(
            collect($ids)
                ->map(fn ($id) => $modelsById->get($id))
                ->filter()
                ->values()
                ->all()
        );
    }
}
