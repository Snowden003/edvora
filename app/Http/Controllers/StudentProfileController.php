<?php

namespace App\Http\Controllers;

use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $profile = $user->studentProfile;

        return view('student.profile-details', compact('user', 'profile'));
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

            // Education (Required)
            'last_education_level' => ['required', 'string', 'max:100'],
            'last_school_name'     => ['required', 'string', 'max:200'],


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
