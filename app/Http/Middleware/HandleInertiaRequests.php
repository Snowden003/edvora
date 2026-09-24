<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'avatar' => $user->avatar ?? null,
                    'avatar_url' => $user->avatar_url ?? null,
                    'is_admin' => $user->role === 'admin',
                    'is_pending_approval' => method_exists($user, 'isPendingApproval') ? $user->isPendingApproval() : ($user->status === 'pending'),
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'info' => fn () => $request->session()->get('info'),
            ],
            'unreadNotificationsCount' => $user ? \App\Models\Notification::where('user_id', $user->id)->where('is_read', false)->count() : 0,
            'teacherNav' => fn () => $user && $user->role === 'teacher' ? [
                'courses' => \App\Models\Course::where('teacher_id', $user->id)->select('id', 'title', 'slug', 'status')->latest()->take(10)->get(),
                'totalCourses' => \App\Models\Course::where('teacher_id', $user->id)->count(),
                'pendingEnrollmentsCount' => \App\Models\EnrollmentRequest::whereIn('course_id', \App\Models\Course::where('teacher_id', $user->id)->pluck('id'))->where('status', 'pending')->count(),
            ] : null,
            'studentNav' => fn () => $user && $user->role === 'student' ? [
                'courses' => $user->enrollments()
                    ->where('status', '!=', 'banned')
                    ->with('course:id,title,slug')
                    ->latest()
                    ->take(8)
                    ->get()
                    ->map(fn($e) => [
                        'id' => $e->course?->id,
                        'title' => $e->course?->title,
                        'slug' => $e->course?->slug,
                    ])
                    ->filter(fn($c) => !empty($c['id']))
                    ->values(),
                'totalCourses' => $user->enrollments()->where('status', '!=', 'banned')->count(),
                'level' => method_exists($user, 'level') ? $user->level() : ['level' => 1, 'title' => 'Beginner', 'progress' => 0],
            ] : null,
            'appName' => config('app.name', 'Edvora Tech'),
        ];
    }
}
