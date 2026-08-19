<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Competition;
use App\Models\Review;
use App\Models\Donation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomePageController extends Controller
{
    public function index()
    {
        $homeData = Cache::remember('home:page-data', now()->addMinutes(10), function () {
            return [
                'totalCourses' => Course::where('status', 'published')->count(),
                'totalTeachers' => User::where('role', 'teacher')->count(),
                'totalStudents' => User::where('role', 'student')->count(),
                'completedCourses' => Enrollment::where('status', 'completed')->count(),
                'featuredCourses' => Course::with(['teacher', 'category'])
                    ->where('status', 'published')
                    ->where('is_featured', true)
                    ->latest()
                    ->limit(1)
                    ->get(),
                'popularCourses' => Course::withCount(['enrollments' => function ($query) {
                        $query->where('status', 'active');
                    }])
                    ->with(['teacher', 'category'])
                    ->where('status', 'published')
                    ->orderByDesc('enrollments_count')
                    ->orderByDesc('rating')
                    ->orderByDesc('total_reviews')
                    ->latest()
                    ->limit(3)
                    ->get(),
                'recentCourses' => Course::with(['teacher', 'category'])
                    ->where('status', 'published')
                    ->latest()
                    ->limit(6)
                    ->get(),
                'upcomingEvents' => Event::where('start_date', '>=', now())
                    ->where('status', 'active')
                    ->orderBy('start_date')
                    ->limit(3)
                    ->get(),
                'upcomingCompetitions' => Competition::where('start_date', '>=', now())
                    ->where('status', 'active')
                    ->orderBy('start_date')
                    ->limit(3)
                    ->get(),
                'approvedReviews' => Review::where('is_approved', true)
                    ->with(['user', 'course'])
                    ->latest()
                    ->limit(6)
                    ->get(),
                'topStudents' => User::where('role', 'student')
                    ->where('xp', '>', 0)
                    ->orderByDesc('xp')
                    ->limit(3)
                    ->get(),
                'totalDonations' => Donation::where('status', 'completed')->sum('amount'),
                'totalSupporters' => Donation::where('status', 'completed')->distinct('email')->count(),
            ];
        });

        extract($homeData);

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
            'upcomingCompetitions',
            'approvedReviews',
            'hasTopStudents',
            'topStudents',
            'totalDonations',
            'totalSupporters'
        ));
    }
}
