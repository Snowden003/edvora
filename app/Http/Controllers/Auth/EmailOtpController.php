<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\StudentEmailVerification;
use App\Models\EmailVerificationCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailOtpController extends Controller
{
    public function show(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect($user->dashboardRoute());
        }

        return view('auth.verify-otp', ['email' => $user->email]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect($user->dashboardRoute());
        }

        $record = EmailVerificationCode::where('user_id', $user->id)
            ->where('code', $request->code)
            ->latest()
            ->first();

        if (! $record) {
            return back()->withErrors(['code' => 'The verification code is incorrect.']);
        }

        if ($record->isExpired()) {
            $record->delete();
            return back()->withErrors(['code' => 'The verification code has expired. Please request a new one.']);
        }

        $user->markEmailAsVerified();
        $record->delete();

        return redirect($user->dashboardRoute())->with('verified', true);
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect($user->dashboardRoute());
        }

        $throttle = EmailVerificationCode::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMinute())
            ->exists();

        if ($throttle) {
            return back()->withErrors(['code' => 'Please wait 1 minute before requesting another code.']);
        }

        self::sendOtp($user);

        return back()->with('status', 'otp-sent');
    }

    public static function sendOtp(\App\Models\User $user): void
    {
        EmailVerificationCode::where('user_id', $user->id)->delete();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailVerificationCode::create([
            'user_id'    => $user->id,
            'code'       => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new StudentEmailVerification($user, $code));
    }
}
