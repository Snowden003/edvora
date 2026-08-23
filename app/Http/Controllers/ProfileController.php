<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Check if teacher has completed onboarding and is approved
        if ($user->role === 'teacher') {
            if (!$user->hasSubmittedTeacherProfile()) {
                return redirect()->route('teacher.onboarding')
                    ->with('error', 'Please complete your teacher profile first.');
            }
            if ($user->isPendingApproval()) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Your account is pending admin approval.');
            }
        }

        $user->load(['teacher', 'studentProfile']);
        return view('profile', compact('user'));
    }

    /**
     * Save profile changes.
     */
    public function saveProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Check if teacher has completed onboarding and is approved
        if ($user->role === 'teacher') {
            if (!$user->hasSubmittedTeacherProfile()) {
                return redirect()->route('teacher.onboarding')
                    ->with('error', 'Please complete your teacher profile first.');
            }
            if ($user->isPendingApproval()) {
                return redirect()->route('teacher.dashboard')
                    ->with('error', 'Your account is pending admin approval.');
            }
        }

        \Log::info('saveProfile called', [
            'user_id' => $user->id,
            'has_cover_file' => $request->hasFile('cover_image'),
            'has_avatar_file' => $request->hasFile('avatar'),
            'files' => array_keys($request->allFiles()),
            'all_input_keys' => array_keys($request->all()),
        ]);

        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255'],
            'phone'            => ['nullable', 'string', 'max:20'],
            'bio'              => ['nullable', 'string', 'max:1000'],
            'department'       => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'string'],
            'avatar'           => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048', 'dimensions:max_width=2000,max_height=2000'],
            'cover_image'      => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:5120', 'dimensions:max_width=4000,max_height=2000'],
            'linkedin'         => ['nullable', 'url', 'max:255'],
            'github'           => ['nullable', 'url', 'max:255'],
            'website'          => ['nullable', 'url', 'max:255'],
        ]);

        $data = $request->only(['name', 'email', 'phone', 'bio', 'department', 'experience_years']);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->hasFile('cover_image')) {
            // Delete old cover if exists
            if ($user->cover_image && !str_starts_with($user->cover_image, 'http')) {
                Storage::disk('public')->delete($user->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $user->update($data);

        if ($user->role === 'teacher') {
            $user->teacher()->updateOrCreate(
                ['user_id' => $user->id],
                $request->only(['linkedin', 'github', 'website'])
            );
        }

        return Redirect::route('profile')->with('success', 'Profile updated successfully.');
    }
}
