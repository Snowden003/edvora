<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->role !== 'student') {
            return $next($request);
        }

        $allowedRoutes = [
            'student.identity.show',
            'student.identity.upload',
            'student.identity.pending',
            'otp.show',
            'otp.verify',
            'otp.resend',
            'logout',
            'logout.get',
            'profile',
            'profile.save',
        ];

        $currentRoute = $request->route()?->getName();

        if (in_array($currentRoute, $allowedRoutes)) {
            return $next($request);
        }

        // Not submitted yet → go upload tazkira
        if ($user->identity_status === 'not_submitted' || $user->identity_status === null) {
            return redirect()->route('student.identity.show')
                ->with('info', 'لطفاً ابتدا عکس تذکره خود را آپلود کنید تا هویت‌تان تأیید شود.');
        }

        // Submitted but waiting for admin
        if ($user->identity_status === 'pending') {
            return redirect()->route('student.identity.pending');
        }

        // Rejected
        if ($user->identity_status === 'rejected') {
            return redirect()->route('student.identity.show')
                ->with('error', 'هویت شما رد شد. لطفاً دوباره تصویر تذکره را آپلود کنید.');
        }

        return $next($request);
    }
}
