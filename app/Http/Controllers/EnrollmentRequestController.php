<?php

namespace App\Http\Controllers;

use App\Mail\EnrollmentApproved;
use App\Mail\EnrollmentRejected;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EnrollmentRequest;
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
     * Teacher: list enrollment requests for their courses.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $courseIds = Course::where('teacher_id', $user->id)->pluck('id');

        $query = EnrollmentRequest::with(['user', 'course'])
            ->whereIn('course_id', $courseIds);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($courseId = $request->get('course_id')) {
            $query->where('course_id', $courseId);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        $courses = Course::where('teacher_id', $user->id)
            ->where('status', 'published')
            ->get();

        $pendingCount = EnrollmentRequest::whereIn('course_id', $courseIds)
            ->where('status', 'pending')
            ->count();

        return view('teacher.enrollment-requests', compact('requests', 'courses', 'user', 'pendingCount'));
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
        $profile = $student->studentProfile;

        return response()->json([
            'id' => $student->id,
            'name' => $student->name,
            'email' => $student->email,
            'avatar' => $student->avatar
                ? (str_starts_with($student->avatar, 'http') ? $student->avatar : asset('storage/' . $student->avatar))
                : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&size=150',
            'bio' => $student->bio,
            'department' => $student->department,
            'xp' => $student->xp ?? 0,
            'enrolled_courses' => $student->enrollments()->count(),
            'joined_at' => $student->created_at->format('M d, Y'),
            'profile_complete' => $profile ? $profile->is_complete : false,
            'father_name' => $profile->father_name ?? null,
            'education' => $profile->last_education_level ?? null,
            'school' => $profile->last_school_name ?? null,
            'national_id' => $profile->national_id ?? null,
            'phone' => $profile->phone_number ?? null,
            'address' => $profile->current_address ?? null,
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

        // Send approval email
        Mail::to($enrollmentRequest->user->email)->send(
            new EnrollmentApproved($enrollmentRequest->user, $enrollmentRequest->course)
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
