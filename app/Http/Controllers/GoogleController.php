<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            } else {
                $user = User::create([
                    'email' => $googleUser->getEmail(),
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role' => 'student',
                    'status' => 'active',
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(24)),
                ]);

                // Mark email as verified since it's from Google
                $user->email_verified_at = now();
                $user->save();

                // Track teacher referral if present
                $refCode = session('teacher_referral_code') ?? request()->cookie('edvora_ref');
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
                            'ip_address'          => request()->ip(),
                        ]);

                        NotificationController::createStudentMessage(
                            $referral->teacher_id,
                            $user->name,
                            "{$user->name} joined Edvora using your referral link for '{$referral->course->title}'! 🎉",
                            $referral->course_id
                        );
                    }
                }
            }

            Auth::login($user);

            return redirect()->intended($user->dashboardRoute());
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['error' => 'Google authentication failed. Please try again.']);
        }
    }
}