<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function enroll(Course $course)
    {
        $user = Auth::user();

        // Check if student profile is complete
        $profile = $user->studentProfile;
        if (!$profile || !$profile->is_complete) {
            return response()->json([
                'status'  => 'profile_incomplete',
                'message' => 'You must complete your profile details before enrolling in a course.',
                'redirect' => route('student.profile-details'),
            ], 403);
        }

        // Check if enrollment is closed
        if ($course->isEnrollmentClosed()) {
            return response()->json([
                'status'  => 'enrollment_closed',
                'message' => 'Enrollment for this course is closed.',
            ], 422);
        }

        // Check if course is full
        if ($course->isFull()) {
            return response()->json([
                'status' => 'course_full',
                'message' => 'This course is full. Maximum students limit reached.',
            ], 422);
        }

        $existing = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 'already_enrolled',
                'message' => 'You are already enrolled in this course.',
            ]);
        }

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'active',
            'progress_percentage' => 0,
        ]);

        $course->increment('enrolled_count');

        // Notify teacher of the new enrollment
        if ($course->teacher_id) {
            NotificationController::notifyStudentEnrolled(
                $course->teacher_id,
                $user->name,
                $course->title,
                $course->id
            );
        }

        // Track teacher referral for this course enrollment
        $refCode = session('teacher_referral_code') ?? request()->cookie('edvora_ref');
        $referral = null;
        if ($refCode) {
            $referral = \App\Models\TeacherReferral::where('code', $refCode)->first();
        }
        if (!$referral) {
            $existingRecord = \App\Models\TeacherReferralRecord::where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->first();
            if ($existingRecord) {
                $referral = $existingRecord->referral;
            }
        }
        if ($referral && (int) $referral->course_id === (int) $course->id) {
            \App\Models\TeacherReferralRecord::updateOrCreate([
                'course_id' => $course->id,
                'user_id'   => $user->id,
            ], [
                'teacher_referral_id' => $referral->id,
                'teacher_id'          => $referral->teacher_id,
                'status'              => 'enrolled',
                'enrolled_at'         => now(),
                'ip_address'          => request()->ip(),
            ]);
        }

        // Check if course should auto-start
        if ($course->shouldAutoStart()) {
            $course->startCourse();
            $message = 'Successfully enrolled! The course has started automatically.';
        } else {
            $message = 'Successfully enrolled in the course!';
        }

        return response()->json([
            'status' => 'enrolled',
            'message' => $message,
        ]);
    }
}
