<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\EmailOtpController;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'status' => 'active',
        ]);

        Auth::login($user);

        // Check for teacher referral
        $refCode = session('teacher_referral_code') ?? $request->cookie('edvora_ref');
        if ($refCode) {
            $referral = \App\Models\TeacherReferral::with('course')->where('code', $refCode)->first();
            if ($referral) {
                \App\Models\TeacherReferralRecord::firstOrCreate([
                    'course_id' => $referral->course_id,
                    'user_id'   => $user->id,
                ], [
                    'teacher_referral_id' => $referral->id,
                    'teacher_id'          => $referral->teacher_id,
                    'status'              => 'registered',
                    'registered_at'       => now(),
                    'ip_address'          => $request->ip(),
                ]);

                \App\Http\Controllers\NotificationController::createStudentMessage(
                    $referral->teacher_id,
                    $user->name,
                    "{$user->name} joined Edvora using your invitation link for course '{$referral->course->title}'! 🎉",
                    $referral->course_id
                );
            }
        }

        EmailOtpController::sendOtp($user);
        return redirect()->route('otp.show');
    }
}
