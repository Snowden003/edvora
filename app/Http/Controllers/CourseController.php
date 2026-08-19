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
        $query = Course::with(['category', 'teacher'])
            ->where('status', 'published');

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

        $sort = $request->get('sort', 'popular');
        match ($sort) {
            'newest'  => $query->latest(),
            'rating'  => $query->orderByDesc('rating'),
            default   => $query->orderByDesc('enrolled_count'),
        };

        $courses    = $query->paginate(6)->withQueryString();
        $categories = Category::all();

        $totalStudents    = User::where('role', 'student')->count();
        $totalCourses     = Course::where('status', 'published')->count();
        $totalCertificates = Certificate::count();

        return view('courses.index', compact('courses', 'categories', 'totalStudents', 'totalCourses', 'totalCertificates'));
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
