<?php

namespace App\Http\Controllers;

use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Inertia\Inertia;

class StudentProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $profile = $user->studentProfile;

        return Inertia::render('Student/ProfileDetails', [
            'profile' => $profile ? [
                'first_name' => $profile->first_name,
                'last_name' => $profile->last_name,
                'father_name' => $profile->father_name,
                'gender' => $profile->gender ?? 'female',
                'date_of_birth' => $profile->date_of_birth ? $profile->date_of_birth->format('Y-m-d') : '',
                'national_id' => $profile->national_id ?? '',
                'passport_number' => $profile->passport_number ?? '',
                'phone_number' => $profile->phone_number ?? '',
                'whatsapp_number' => $profile->whatsapp_number ?? '',
                'province' => $profile->province ?? '',
                'district' => $profile->district ?? '',
                'postal_code' => $profile->postal_code ?? '',
                'last_education_level' => $profile->last_education_level ?? '',
                'last_school_name' => $profile->last_school_name ?? '',
                'emergency_contact_name' => $profile->emergency_contact_name ?? '',
                'emergency_contact_phone' => $profile->emergency_contact_phone ?? '',
                'emergency_contact_relation' => $profile->emergency_contact_relation ?? '',
                'skills' => $profile->skills ?? '',
                'languages' => $profile->languages ?? '',
                'about_me' => $profile->about_me ?? '',
                'profile_photo_url' => $profile->profile_photo ? asset('storage/' . $profile->profile_photo) : null,
                'is_complete' => (bool) $profile->is_complete,
            ] : null,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            // Personal Information (Required)
            'first_name'       => ['required', 'string', 'max:100'],
            'last_name'        => ['required', 'string', 'max:100'],
            'father_name'      => ['required', 'string', 'max:100'],
            'gender'           => ['nullable', 'in:female'],
            'date_of_birth'    => ['required', 'date', 'before:today'],
            'national_id'      => ['required', 'string', 'max:50'],
            'phone_number'     => ['required', 'string', 'max:20'],
            'whatsapp_number'  => ['nullable', 'string', 'max:20'],

            // Personal Information (Optional)
            'passport_number'  => ['nullable', 'string', 'max:50'],

            // Address (Required)
            'province'         => ['required', 'string', 'max:100'],
            'district'         => ['required', 'string', 'max:100'],
            'postal_code'       => ['nullable', 'string', 'max:20'],

            // Education
            'last_education_level' => ['required', 'string', 'max:100'],
            'last_school_name'     => ['nullable', 'string', 'max:200'],


            // Emergency Contact (Required)
            'emergency_contact_name'     => ['required', 'string', 'max:100'],
            'emergency_contact_phone'    => ['required', 'string', 'max:20'],
            'emergency_contact_relation' => ['required', 'string', 'max:50'],

            // Additional (Optional)
            'skills'        => ['nullable', 'string', 'max:1000'],
            'languages'     => ['nullable', 'string', 'max:500'],
            'about_me'      => ['nullable', 'string', 'max:2000'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('student-profiles', 'public');
        }

        $validated['is_complete'] = true;
        $validated['user_id'] = $user->id;

        StudentProfile::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('student.profile-details')
            ->with('success', 'Your profile has been saved successfully!');
    }
}
