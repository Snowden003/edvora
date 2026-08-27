<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = Course::with(['category', 'teacher'])
            ->where('status', '!=', 'draft');

        // Apply filter types (VIP, Upcoming, Finished, Popular)
        $filter = $request->get('filter', 'all');

        $query = clone $baseQuery;

        if ($filter === 'vip') {
            $query->where('is_featured', true);
        } elseif ($filter === 'upcoming') {
            $query->where(function ($q) {
                $q->where('start_date', '>=', now()->startOfDay())
                  ->orWhere(function ($sub) {
                      $sub->whereNull('started_at')
                          ->where('status', 'published')
                          ->where(function ($d) {
                              $d->whereNull('end_date')->orWhere('end_date', '>=', now()->startOfDay());
                          });
                  });
            });
        } elseif ($filter === 'finished') {
            $query->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNotNull('end_date')->where('end_date', '<', now()->startOfDay());
                })->orWhereIn('status', ['archived', 'completed']);
            });
        } elseif ($filter === 'popular') {
            $query->orderByDesc('enrolled_count');
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categorySlug = $request->get('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $categorySlug));
        }

        if ($level = $request->get('level')) {
            $query->where('level', $level);
        }

        $sort = $request->get('sort', $filter === 'popular' ? 'popular' : 'newest');
        if ($filter !== 'popular') {
            match ($sort) {
                'popular' => $query->orderByDesc('enrolled_count'),
                'rating' => $query->orderByDesc('rating'),
                default => $query->latest(),
            };
        }

        $courses = $query->paginate(8)->withQueryString();
        $categories = Category::all();

        // Calculate filter counts for the filter chips
        $counts = [
            'all' => (clone $baseQuery)->count(),
            'vip' => (clone $baseQuery)->where('is_featured', true)->count(),
            'upcoming' => (clone $baseQuery)->where(function ($q) {
                $q->where('start_date', '>=', now()->startOfDay())
                  ->orWhere(function ($sub) {
                      $sub->whereNull('started_at')
                          ->where('status', 'published')
                          ->where(function ($d) {
                              $d->whereNull('end_date')->orWhere('end_date', '>=', now()->startOfDay());
                          });
                  });
            })->count(),
            'finished' => (clone $baseQuery)->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNotNull('end_date')->where('end_date', '<', now()->startOfDay());
                })->orWhereIn('status', ['archived', 'completed']);
            })->count(),
            'popular' => (clone $baseQuery)->where('enrolled_count', '>', 0)->count(),
        ];

        $totalStudents = User::where('role', 'student')->count();
        $totalCourses = (clone $baseQuery)->count();
        $totalCertificates = Certificate::count();

        if ($request->ajax()) {
            return view('courses.partials.course-list', compact(
                'courses',
                'categories',
                'counts',
                'filter',
                'totalStudents',
                'totalCourses',
                'totalCertificates'
            ))->render();
        }

        return view('courses.index', compact(
            'courses',
            'categories',
            'counts',
            'filter',
            'totalStudents',
            'totalCourses',
            'totalCertificates'
        ));
    }

    public function show($slug)
    {
        $course = Course::with(['category', 'teacher', 'lessons'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $related = Course::with('category')
            ->where('status', 'published')
            ->where('category_id', $course->category_id)
            ->where('id', '!=', $course->id)
            ->limit(3)
            ->get();

        $isEnrolled = false;
        $isWishlisted = false;

        if (Auth::check()) {
            $isEnrolled = Enrollment::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->exists();

            $isWishlisted = Wishlist::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->exists();
        }

        return view('courses.detail', compact('course', 'related', 'isEnrolled', 'isWishlisted'));
    }
}
