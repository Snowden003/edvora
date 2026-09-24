<?php

namespace App\Http\Controllers;

use App\Models\TeacherReferral;
use App\Models\TeacherReferralRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    /**
     * Handle referral link visits: /ref/{code}
     */
    public function join(Request $request, string $code)
    {
        $referral = TeacherReferral::with(['course', 'teacher'])
            ->where('code', $code)
            ->firstOrFail();

        // Increment click counter
        $referral->increment('clicks_count');

        // Store referral context in session
        session([
            'teacher_referral_code' => $referral->code,
            'referral_course_id'    => $referral->course_id,
            'referral_teacher_id'   => $referral->teacher_id,
        ]);

        // 30-day persistent cookie
        cookie()->queue('edvora_ref', $referral->code, 60 * 24 * 30);

        // If user is already logged in as student, track their visit
        if (Auth::check() && Auth::user()->isStudent()) {
            TeacherReferralRecord::firstOrCreate([
                'course_id' => $referral->course_id,
                'user_id'   => Auth::id(),
            ], [
                'teacher_referral_id' => $referral->id,
                'teacher_id'          => $referral->teacher_id,
                'status'              => 'visited',
                'registered_at'       => now(),
                'ip_address'          => $request->ip(),
            ]);
        }

        return redirect()->route('courses.detail', $referral->course->slug)
            ->with('referral_welcome', [
                'teacher_name' => $referral->teacher->name,
                'course_title' => $referral->course->title,
                'code'         => $referral->code,
            ]);
    }
}
