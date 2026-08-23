<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\AdminTeacherRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(Request $request)
    {
        $role = $request->query('role', 'student');
        session(['google_auth_role' => $role]);

        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $role = session('google_auth_role', 'student');

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
                    'role' => $role,
                    'status' => $role === 'teacher' ? 'pending' : 'active',
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(24)),
                ]);

                // Mark email as verified since it's from Google
                $user->email_verified_at = now();
                $user->save();

                if ($user->role === 'teacher') {
                    Mail::to(config('app.admin_notification_email'))->send(new AdminTeacherRegistered($user));
                }
            }

            Auth::login($user);

            return redirect()->intended($user->dashboardRoute());
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['error' => 'Google authentication failed. Please try again.']);
        }
    }
}