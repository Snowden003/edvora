<?php

namespace App\Http\Controllers;

use App\Mail\EnrollmentApproved;
use App\Mail\EnrollmentRejected;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EnrollmentRequest;
use App\Models\Point;
use App\Models\User;
use App\Services\ScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EnrollmentRequestController extends Controller
{
    /**
     * Student sends enrollment request for a course.
     */
    public function store(Course $course, Request $request)
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

        // Check if enrollment is closed (5 days after start, manually closed, completed, or full)
        if ($course->isEnrollmentClosed()) {
            return response()->json([
                'status'  => 'enrollment_closed',
                'message' => 'Enrollment for this course is closed.',
            ], 422);
        }

        // Check if already enrolled
        $alreadyEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($alreadyEnrolled) {
            return response()->json([
                'status' => 'already_enrolled',
                'message' => 'You are already enrolled in this course.',
            ]);
        }

        // Check schedule conflict
        $conflict = $this->checkScheduleConflict($user, $course);
        if ($conflict) {
            return response()->json([
                'status' => 'conflict',
                'message' => $conflict,
            ]);
        }

        // Check if request already exists
        $existing = EnrollmentRequest::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            if ($existing->status === 'pending') {
                return response()->json([
                    'status' => 'already_requested',
                    'message' => 'Your request is already pending review.',
                ]);
            }

            if ($existing->status === 'approved') {
                return response()->json([
                    'status' => 'already_enrolled',
                    'message' => 'You are already enrolled in this course.',
                ]);
            }

            if ($existing->status === 'rejected') {
                // Allow re-request after rejection
                $existing->update([
                    'status' => 'pending',
                    'student_message' => $request->input('message', ''),
                    'rejection_reason' => null,
                    'reviewed_at' => null,
                ]);

                return response()->json([
                    'status' => 'requested',
                    'message' => 'Your enrollment request has been re-submitted!',
                ]);
            }
        }

        EnrollmentRequest::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'student_message' => $request->input('message', ''),
        ]);

        if ($course->teacher_id) {
            NotificationController::notifyEnrollmentRequest(
                $course->teacher_id,
                $user->name,
                $course->title,
                $course->id
            );
        }

        // Track teacher referral for course request
        $refCode = session('teacher_referral_code') ?? $request->cookie('edvora_ref');
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
                'status'              => 'requested',
                'ip_address'          => $request->ip(),
            ]);
        }

        return response()->json([
            'status' => 'requested',
            'message' => 'Your enrollment request has been submitted! The instructor will review it.',
        ]);
    }

    /**
     * Student cancels their own pending enrollment request.
     */
    public function cancel(EnrollmentRequest $enrollmentRequest)
    {
        $user = Auth::user();

        if ($enrollmentRequest->user_id !== $user->id) {
            abort(403);
        }

        if ($enrollmentRequest->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only pending requests can be cancelled.',
            ]);
        }

        $enrollmentRequest->delete();

        return response()->json([
            'status' => 'cancelled',
            'message' => 'Your enrollment request has been cancelled.',
        ]);
    }

    /**
     * Teacher: list enrolled students and enrollment requests for their courses.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $courseIds = Course::where('teacher_id', $user->id)->pluck('id');
        $activeTab = $request->get('tab', 'enrolled');

        // All courses of this teacher for filter dropdown
        $courses = Course::where('teacher_id', $user->id)->get();

        // 1. Enrolled Students Query (from enrollments table)
        $enrollQuery = Enrollment::with(['user.studentProfile', 'course'])
            ->whereIn('course_id', $courseIds);

        if ($courseId = $request->get('course_id')) {
            $enrollQuery->where('course_id', $courseId);
        }

        if ($status = $request->get('status')) {
            if ($activeTab === 'enrolled') {
                $enrollQuery->where('status', $status);
            }
        }

        if ($search = $request->get('search')) {
            $enrollQuery->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $enrollments = $enrollQuery->latest()->paginate(15, ['*'], 'enrolled_page')->withQueryString();

        // 2. Enrollment Requests Query (from enrollment_requests table)
        $requestQuery = EnrollmentRequest::with(['user.studentProfile', 'course'])
            ->whereIn('course_id', $courseIds);

        if ($courseId = $request->get('course_id')) {
            $requestQuery->where('course_id', $courseId);
        }

        if ($status = $request->get('status')) {
            if ($activeTab === 'requests') {
                $requestQuery->where('status', $status);
            }
        }

        if ($search = $request->get('search')) {
            $requestQuery->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $requests = $requestQuery->latest()->paginate(15, ['*'], 'requests_page')->withQueryString();

        // Overall statistics
        $enrolledTotalCount = Enrollment::whereIn('course_id', $courseIds)->count();
        $activeEnrolledCount = Enrollment::whereIn('course_id', $courseIds)->where('status', 'active')->count();
        $completedCount = Enrollment::whereIn('course_id', $courseIds)->where('status', 'completed')->count();
        $pendingCount = EnrollmentRequest::whereIn('course_id', $courseIds)
            ->where('status', 'pending')
            ->count();
        $requestsTotalCount = EnrollmentRequest::whereIn('course_id', $courseIds)->count();
        $totalPointsAwarded = (int) Point::whereIn('course_id', $courseIds)->where('amount', '>', 0)->sum('amount');

        return view('teacher.enrollment-requests', compact(
            'enrollments',
            'requests',
            'courses',
            'user',
            'pendingCount',
            'enrolledTotalCount',
            'activeEnrolledCount',
            'completedCount',
            'requestsTotalCount',
            'totalPointsAwarded',
            'activeTab'
        ));
    }

    /**
     * Teacher: view student profile for a specific request.
     */
    public function studentProfile(EnrollmentRequest $enrollmentRequest)
    {
        $user = Auth::user();
        $courseIds = Course::where('teacher_id', $user->id)->pluck('id');

        if (!$courseIds->contains($enrollmentRequest->course_id)) {
            abort(403);
        }

        $student = $enrollmentRequest->user;
        return $this->buildFullStudentProfileResponse($student, $courseIds);
    }

    /**
     * Teacher: view student profile for an enrolled student by user ID.
     */
    public function studentProfileUser(User $user)
    {
        $teacher = Auth::user();
        $courseIds = Course::where('teacher_id', $teacher->id)->pluck('id');

        // Verify this student is enrolled in or has requested any course of this teacher
        $isEnrolled = Enrollment::where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->exists();
        $hasRequest = EnrollmentRequest::where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->exists();

        if (!$isEnrolled && !$hasRequest && !$teacher->isAdmin()) {
            abort(403, 'Unauthorized access to student profile.');
        }

        return $this->buildFullStudentProfileResponse($user, $courseIds);
    }

    /**
     * Helper to build complete student profile response.
     */
    private function buildFullStudentProfileResponse(User $student, $teacherCourseIds)
    {
        $profile = $student->studentProfile;

        // Student's enrollments
        $enrollments = Enrollment::with('course:id,title,slug,thumbnail')
            ->where('user_id', $student->id)
            ->get()
            ->map(function ($en) use ($teacherCourseIds) {
                return [
                    'course_id' => $en->course_id,
                    'course_title' => $en->course?->title ?? 'Course',
                    'status' => $en->status,
                    'progress' => (int) ($en->progress_percentage ?? 0),
                    'is_teacher_course' => $teacherCourseIds->contains($en->course_id),
                    'enrolled_at' => $en->created_at ? $en->created_at->format('M d, Y') : 'N/A',
                ];
            });

        // Points history
        $pointsHistory = Point::where('user_id', $student->id)
            ->with('creator:id,name')
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($pt) {
                return [
                    'id' => $pt->id,
                    'amount' => (int) $pt->amount,
                    'reason' => $pt->reason ?? 'Manual adjustment',
                    'type' => $pt->type ?? 'manual',
                    'created_by' => $pt->creator?->name ?? 'Instructor',
                    'date' => $pt->created_at ? $pt->created_at->format('M d, Y - H:i') : '',
                    'relative_date' => $pt->created_at ? $pt->created_at->diffForHumans() : '',
                ];
            });

        // Teacher courses where student is enrolled
        $teacherCourses = Course::whereIn('id', $teacherCourseIds)
            ->whereHas('enrollments', function ($q) use ($student) {
                $q->where('user_id', $student->id);
            })
            ->get(['id', 'title']);

        return response()->json([
            'id' => $student->id,
            'name' => $student->name,
            'email' => $student->email,
            'phone' => $profile?->phone_number ?? $student->phone ?? null,
            'avatar' => $student->avatar
                ? (str_starts_with($student->avatar, 'http') ? $student->avatar : asset('storage/' . $student->avatar))
                : ($profile?->profile_photo ? asset('storage/' . $profile->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&size=150'),
            'bio' => $student->bio ?? $profile?->about_me ?? null,
            'department' => $student->department,
            'status' => $student->status ?? 'active',
            'role' => $student->role ?? 'student',
            'joined_at' => $student->created_at ? $student->created_at->format('M d, Y') : 'N/A',
            'email_verified' => (bool) $student->email_verified_at,

            // Gamification / Scores
            'total_score' => $student->totalScore(),
            'xp' => $student->totalScore(),
            'earned_points' => $student->earnedPoints(),
            'deducted_points' => $student->deductedPoints(),
            'points_history' => $pointsHistory,

            // Courses
            'enrolled_courses_count' => $enrollments->count(),
            'enrollments' => $enrollments,
            'teacher_courses' => $teacherCourses,

            // Personal Information
            'profile_complete' => $profile ? (bool) $profile->is_complete : false,
            'first_name' => $profile?->first_name,
            'last_name' => $profile?->last_name,
            'father_name' => $profile?->father_name,
            'mother_name' => $profile?->mother_name,
            'nickname' => $profile?->nickname,
            'gender' => $profile?->gender,
            'date_of_birth' => $profile?->date_of_birth ? $profile->date_of_birth->format('Y-m-d') : null,
            'marital_status' => $profile?->marital_status,
            'blood_type' => $profile?->blood_type,
            'national_id' => $profile?->national_id,
            'passport_number' => $profile?->passport_number,
            'whatsapp_number' => $profile?->whatsapp_number,

            // Address Information
            'province' => $profile?->province,
            'district' => $profile?->district,
            'current_address' => $profile?->current_address,
            'permanent_address' => $profile?->permanent_address,
            'postal_code' => $profile?->postal_code,

            // Academic & Education Information
            'education_level' => $profile?->last_education_level,
            'school_name' => $profile?->last_school_name,
            'university_name' => $profile?->university_name,
            'field_of_study' => $profile?->field_of_study,
            'graduation_year' => $profile?->graduation_year,
            'gpa' => $profile?->gpa,
            'other_certifications' => $profile?->other_certifications,

            // Emergency Contact Information
            'emergency_contact_name' => $profile?->emergency_contact_name,
            'emergency_contact_phone' => $profile?->emergency_contact_phone,
            'emergency_contact_relation' => $profile?->emergency_contact_relation,

            // Skills & Languages & About
            'skills' => $profile?->skills,
            'languages' => $profile?->languages,
            'about_me' => $profile?->about_me,
        ]);
    }

    /**
     * Teacher: adjust student points (add or deduct).
     */
    public function adjustPoints(Request $request, User $user)
    {
        $teacher = Auth::user();
        $courseIds = Course::where('teacher_id', $teacher->id)->pluck('id');

        // Verify student is enrolled in this teacher's courses
        $isEnrolled = Enrollment::where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->exists();

        if (!$isEnrolled && !$teacher->isAdmin()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized. This student is not enrolled in any of your courses.',
            ], 403);
        }

        $request->validate([
            'amount' => ['required', 'integer', 'not_in:0'],
            'reason' => ['required', 'string', 'max:500'],
            'course_id' => ['nullable', 'integer'],
        ]);

        $amount = (int) $request->input('amount');
        $reason = $request->input('reason');
        $courseId = $request->input('course_id');

        if ($courseId && !$courseIds->contains($courseId) && !$teacher->isAdmin()) {
            $courseId = null;
        }

        if (!$courseId) {
            $courseId = Enrollment::where('user_id', $user->id)
                ->whereIn('course_id', $courseIds)
                ->value('course_id');
        }

        $point = ScoreService::adjust(
            user: $user,
            amount: $amount,
            type: 'manual',
            reason: $reason,
            courseId: $courseId,
            createdBy: $teacher,
            notify: true
        );

        $fresh = $user->fresh();

        return response()->json([
            'status' => 'success',
            'message' => ($amount > 0 ? "Added +{$amount}" : "Deducted " . abs($amount)) . " points for {$user->name}.",
            'new_score' => $fresh->totalScore(),
            'earned_points' => $fresh->earnedPoints(),
            'deducted_points' => $fresh->deductedPoints(),
            'point' => [
                'id' => $point->id,
                'amount' => $point->amount,
                'reason' => $point->reason,
                'type' => $point->type,
                'created_by' => $teacher->name,
                'date' => $point->created_at ? $point->created_at->format('M d, Y - H:i') : '',
                'relative_date' => 'Just now',
            ],
        ]);
    }

    /**
     * Teacher: approve enrollment request.
     */
    public function approve(EnrollmentRequest $enrollmentRequest)
    {
        $user = Auth::user();
        $courseIds = Course::where('teacher_id', $user->id)->pluck('id');

        if (!$courseIds->contains($enrollmentRequest->course_id)) {
            abort(403);
        }

        if (!$enrollmentRequest->isPending()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This request has already been reviewed.',
            ]);
        }

        if ($enrollmentRequest->course->isCompleted()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This course has ended. You cannot approve enrollments for completed courses.',
            ], 422);
        }

        // Approve the request
        $enrollmentRequest->update([
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);

        // Create or update enrollment
        $enrollment = Enrollment::firstOrCreate([
            'user_id' => $enrollmentRequest->user_id,
            'course_id' => $enrollmentRequest->course_id,
        ], [
            'status' => 'active',
            'progress_percentage' => 0,
        ]);

        // Ensure enrollment status is active
        if ($enrollment->status !== 'active') {
            $enrollment->update(['status' => 'active']);
        }

        // Increment enrolled_count
        $enrollmentRequest->course->increment('enrolled_count');

        // Update referral record to enrolled if student was referred
        \App\Models\TeacherReferralRecord::where('course_id', $enrollmentRequest->course_id)
            ->where('user_id', $enrollmentRequest->user_id)
            ->update([
                'status'      => 'enrolled',
                'enrolled_at' => now(),
            ]);

        // Send approval email
        Mail::to($enrollmentRequest->user->email)->send(
            new EnrollmentApproved($enrollmentRequest->user, $enrollmentRequest->course)
        );

        // Send in-app push notification
        NotificationController::notifyEnrollmentApproved(
            $enrollmentRequest->user_id,
            $enrollmentRequest->course->title,
            $enrollmentRequest->course->slug
        );

        return response()->json([
            'status' => 'approved',
            'message' => 'Student has been approved and notified via email.',
        ]);
    }

    /**
     * Teacher: reject enrollment request.
     */
    public function reject(EnrollmentRequest $enrollmentRequest, Request $request)
    {
        $user = Auth::user();
        $courseIds = Course::where('teacher_id', $user->id)->pluck('id');

        if (!$courseIds->contains($enrollmentRequest->course_id)) {
            abort(403);
        }

        if (!$enrollmentRequest->isPending()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This request has already been reviewed.',
            ]);
        }

        $request->validate([
            'reason' => 'required|string|min:5|max:1000',
        ]);

        $reason = $request->input('reason');

        // Reject the request
        $enrollmentRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_at' => now(),
        ]);

        // Send rejection email
        Mail::to($enrollmentRequest->user->email)->send(
            new EnrollmentRejected($enrollmentRequest->user, $enrollmentRequest->course, $reason)
        );

        // Send in-app push notification
        NotificationController::notifyEnrollmentRejected(
            $enrollmentRequest->user_id,
            $enrollmentRequest->course->title,
            $reason
        );

        return response()->json([
            'status' => 'rejected',
            'message' => 'Request rejected. Student has been notified via email.',
        ]);
    }

    /**
     * Check if the student has a schedule conflict with the requested course.
     * Returns conflict message or null if no conflict.
     */
    private function checkScheduleConflict($user, Course $course): ?string
    {
        // If the course has no schedule, no conflict possible
        if (!$course->primary_class_days || !$course->primary_class_start || !$course->primary_class_end) {
            return null;
        }

        $courseDays = is_array($course->primary_class_days) ? $course->primary_class_days : (array) json_decode((string) $course->primary_class_days, true);
        $courseStart = $course->primary_class_start;
        $courseEnd = $course->primary_class_end;

        if (empty($courseDays)) {
            return null;
        }

        // Get all courses the student is enrolled in OR has pending requests for
        $enrolledCourseIds = Enrollment::where('user_id', $user->id)
            ->pluck('course_id');

        $pendingRequestCourseIds = EnrollmentRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->pluck('course_id');

        $allCourseIds = $enrolledCourseIds->merge($pendingRequestCourseIds)->unique();

        if ($allCourseIds->isEmpty()) {
            return null;
        }

        // Get courses with schedule info
        $existingCourses = Course::whereIn('id', $allCourseIds)
            ->whereNotNull('primary_class_days')
            ->whereNotNull('primary_class_start')
            ->whereNotNull('primary_class_end')
            ->get();

        foreach ($existingCourses as $existingCourse) {
            $existingDays = is_array($existingCourse->primary_class_days)
                ? $existingCourse->primary_class_days
                : json_decode($existingCourse->primary_class_days, true);

            if (empty($existingDays)) {
                continue;
            }

            // Check if days overlap
            $commonDays = array_intersect($courseDays, $existingDays);
            if (empty($commonDays)) {
                continue;
            }

            // Check if time overlaps
            $existingStart = $existingCourse->primary_class_start;
            $existingEnd = $existingCourse->primary_class_end;

            if ($this->timesOverlap($courseStart, $courseEnd, $existingStart, $existingEnd)) {
                $daysList = implode(', ', $commonDays);
                return "Schedule conflict! This course overlaps with \"{$existingCourse->title}\" on {$daysList} ({$existingStart} - {$existingEnd}). You cannot be in two classes at the same time.";
            }
        }

        return null;
    }

    /**
     * Check if two time ranges overlap.
     */
    private function timesOverlap(string $start1, string $end1, string $start2, string $end2): bool
    {
        $s1 = strtotime($start1);
        $e1 = strtotime($end1);
        $s2 = strtotime($start2);
        $e2 = strtotime($end2);

        // Overlap exists if one starts before the other ends
        return $s1 < $e2 && $s2 < $e1;
    }
}
