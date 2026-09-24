<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page.
     */
    public function show(Request $request): View|RedirectResponse|Response
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

            $user->load(['teacher', 'studentProfile']);
            return view('profile', compact('user'));
        }

        // Student Profile (Inertia 3D Minimal Modern)
        $user->load(['studentProfile']);

        $coverUrl = null;
        if ($user->cover_image) {
            if (str_starts_with($user->cover_image, 'http')) {
                $coverUrl = $user->cover_image;
            } elseif (str_starts_with($user->cover_image, 'storage/') || str_starts_with($user->cover_image, '/storage/')) {
                $coverUrl = asset(ltrim($user->cover_image, '/'));
            } else {
                $coverUrl = asset('storage/' . $user->cover_image);
            }
        }

        $avatarUrl = method_exists($user, 'publicAvatarUrl') ? $user->publicAvatarUrl() : null;
        $level = method_exists($user, 'level') ? $user->level() : ['level' => 1, 'title' => 'Scholar', 'progress' => 0];
        $enrolledCount = method_exists($user, 'enrollments') ? $user->enrollments()->where('status', '!=', 'banned')->count() : 0;
        $completedCount = method_exists($user, 'enrollments') ? $user->enrollments()->where('status', 'completed')->count() : 0;
        $totalXp = method_exists($user, 'totalScore') ? $user->totalScore() : ($user->xp ?? 0);

        return Inertia::render('Student/Profile', [
            'profileUser' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'bio' => $user->bio,
                'department' => $user->department,
                'avatar' => $avatarUrl,
                'cover_image' => $coverUrl,
                'linkedin' => $user->linkedin ?? null,
                'github' => $user->github ?? null,
                'website' => $user->website ?? null,
                'member_since' => $user->created_at?->format('M Y'),
            ],
            'stats' => [
                'enrolled_courses' => $enrolledCount,
                'completed_courses' => $completedCount,
                'level' => $level['level'] ?? 1,
                'level_title' => $level['title'] ?? 'Scholar',
                'progress' => $level['progress'] ?? 0,
                'total_xp' => $totalXp,
            ],
            'studentProfile' => $user->studentProfile ? [
                'is_complete' => (bool) $user->studentProfile->is_complete,
                'first_name' => $user->studentProfile->first_name,
                'last_name' => $user->studentProfile->last_name,
                'national_id' => $user->studentProfile->national_id,
                'province' => $user->studentProfile->province,
            ] : null,
        ]);
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

        return Redirect::back()->with('success', 'Profile updated successfully.');
    }
}
