<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeacherOnboarded
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only apply to teachers
        if (!$user || $user->role !== 'teacher') {
            return $next($request);
        }

        // Allow specific routes
        $allowedRoutes = [
            'teacher.onboarding',
            'teacher.onboarding.submit',
            'teacher.dashboard',
            'otp.show',
            'otp.verify',
            'otp.resend',
            'logout',
        ];

        $currentRoute = $request->route()?->getName();

        if (in_array($currentRoute, $allowedRoutes)) {
            return $next($request);
        }

        // Also allow onboarding URL patterns
        if ($request->is('teacher/onboarding') || $request->is('teacher/onboarding/*')) {
            return $next($request);
        }

        // If teacher hasn't submitted profile, redirect to onboarding
        if (!$user->hasSubmittedTeacherProfile()) {
            return redirect()->route('teacher.onboarding')
                ->with('error', 'Please complete your teacher profile first.');
        }

        // If teacher is pending approval, redirect to dashboard (which shows pending page)
        if ($user->isPendingApproval()) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Your account is pending admin approval.');
        }

        return $next($request);
    }
}
