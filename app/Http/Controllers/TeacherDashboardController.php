<?php

namespace App\Http\Controllers;

use App\Mail\TeacherApplicationSubmitted;
use App\Models\ClassNote;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Point;
use App\Models\ScoringRule;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Services\ScoreService;
use Barryvdh\DomPDF\Facade\Pdf;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Step 1: Never submitted profile → show onboarding form
        if (!$user->hasSubmittedTeacherProfile()) {
            return view('teacher.onboarding');
        }

        // Step 2: Active or Pending → show dashboard (pending status will show modal popup)
        $teacher = $user->teacher;
        $courses = Course::where('teacher_id', $user->id)->get();
        $totalStudents = $courses->sum('enrolled_count');
        $activeCourses = $courses->whereIn('status', ['active', 'published']);
        $avgRating = $courses->where('rating', '>', 0)->avg('rating') ?? 0;

        return view('teacher.dashboard', compact(
            'user', 'teacher', 'courses', 'totalStudents', 'activeCourses', 'avgRating'
        ));
    }

    public function yourCourses(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasSubmittedTeacherProfile() || $user->isPendingApproval()) {
            return redirect()->route('teacher.dashboard');
        }

        $query = Course::where('teacher_id', $user->id);

        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($category = $request->get('category')) {
            $query->whereHas('category', fn($q) => $q->where('name', $category));
        }

        $courses    = $query->with('category')->latest()->paginate(9)->withQueryString();
        $categories = \App\Models\Category::all();

        $totalStudents  = Course::where('teacher_id', $user->id)->sum('enrolled_count');
        $totalCourses   = Course::where('teacher_id', $user->id)->count();
        $avgRating      = Course::where('teacher_id', $user->id)->where('rating', '>', 0)->avg('rating') ?? 0;
        $activeCourses  = Course::where('teacher_id', $user->id)->where('status', 'active')->count();

        return view('teacher.your-courses', compact(
            'courses', 'categories', 'user',
            'totalStudents', 'totalCourses', 'avgRating', 'activeCourses'
        ));
    }

    public function onboarding()
    {
        $user = Auth::user();

        if ($user->hasSubmittedTeacherProfile()) {
            return redirect()->route('teacher.dashboard');
        }

        return view('teacher.onboarding');
    }

    public function courseDetail($id)
    {
        $user = Auth::user();

        $course = Course::with([
            'category',
            'teacher',
            'lessons' => fn($q) => $q->orderBy('order'),
        ])->where('teacher_id', $user->id)->findOrFail($id);

        $enrollments = \DB::table('enrollments')
            ->join('users', 'users.id', '=', 'enrollments.user_id')
            ->where('enrollments.course_id', $id)
            ->select(
                'users.id', 'users.name', 'users.avatar',
                'enrollments.status', 'enrollments.progress_percentage',
                'enrollments.created_at as enrolled_at'
            )
            ->get();

        $reviews = \DB::table('reviews')
            ->where('reviews.course_id', $id)
            ->select('reviews.rating', 'reviews.comment', 'reviews.created_at', 'reviews.is_approved', 'reviews.likes_count', 'reviews.dislikes_count')
            ->orderByDesc('reviews.created_at')
            ->get();

        $documents = \App\Models\CourseDocument::with('lesson')
            ->where('course_id', $id)
            ->orderByDesc('created_at')
            ->get();

        $activeSession = \App\Models\ClassSession::where('course_id', $id)
            ->where('status', 'active')
            ->latest()
            ->first();

        $pastSessions = \App\Models\ClassSession::where('course_id', $id)
            ->where('status', 'ended')
            ->orderByDesc('started_at')
            ->get();

        $sessionIds = $pastSessions->pluck('id');

        $attendanceSummary = $enrollments->map(function ($en) use ($sessionIds) {
            $total   = $sessionIds->count();
            $present = \DB::table('session_attendances')
                ->whereIn('session_id', $sessionIds)
                ->where('user_id', $en->id)
                ->where('status', 'present')
                ->count();
            $late    = \DB::table('session_attendances')
                ->whereIn('session_id', $sessionIds)
                ->where('user_id', $en->id)
                ->where('status', 'late')
                ->count();
            $absent  = $total - $present - $late;
            return (object)[
                'id'      => $en->id,
                'user_id' => $en->id,
                'name'    => $en->name,
                'avatar'  => $en->avatar,
                'total'   => $total,
                'present' => $present,
                'late'    => $late,
                'absent'  => max(0, $absent),
                'rate'    => $total > 0 ? round((($present + $late) / $total) * 100) : 0,
            ];
        });

        $classNotes = ClassNote::where('course_id', $id)
            ->orderByDesc('class_date')
            ->orderByDesc('created_at')
            ->get();

        $lessons = $course->lessons()->orderBy('order')->get();

        // Get teacher's completed lessons for this course
        $completedLessonIds = \App\Models\CompletedLesson::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->pluck('lesson_id')
            ->toArray();

        // Calculate progress
        $totalLessons = $lessons->count();
        $completedLessonsCount = count($completedLessonIds);
        $progressPercent = $totalLessons > 0 ? round(($completedLessonsCount / $totalLessons) * 100) : 0;

        // Student Scoring / Gamification
        $scoringRules = \App\Models\ScoringRule::active()->orderBy('label')->get();
        $studentIds = $enrollments->pluck('id');
        $coursePoints = \App\Models\Point::with('creator')
            ->whereIn('user_id', $studentIds)
            ->where(function ($q) use ($course) {
                $q->where('course_id', $course->id)->orWhereNull('course_id');
            })
            ->latest()
            ->take(50)
            ->get();

        return view('teacher.courses-detail', compact(
            'course', 'user', 'enrollments', 'reviews', 'documents',
            'activeSession', 'pastSessions', 'attendanceSummary', 'classNotes', 'lessons',
            'completedLessonIds', 'completedLessonsCount', 'totalLessons', 'progressPercent',
            'scoringRules', 'coursePoints'
        ));
    }

    public function saveAttendance(Request $request, $id, $sessionId)
    {
        $user   = Auth::user();
        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        $session = \App\Models\ClassSession::where('course_id', $course->id)->findOrFail($sessionId);

        $records = $request->input('attendance', []);

        foreach ($records as $userId => $status) {
            if (!in_array($status, ['present', 'absent', 'late'])) continue;
            \DB::table('session_attendances')->updateOrInsert(
                ['session_id' => $session->id, 'user_id' => $userId],
                ['status' => $status, 'updated_at' => now(), 'created_at' => now()]
            );

            // Sync attendance scoring for this student and session
            $student = User::where('role', 'student')->find($userId);
            if ($student) {
                // Remove previous attendance points for this session to avoid duplicates
                Point::where('user_id', $student->id)
                    ->where('type', 'like', 'attendance_%')
                    ->where('related_type', \App\Models\ClassSession::class)
                    ->where('related_id', $session->id)
                    ->delete();

                $action = match($status) {
                    'present' => 'attendance_on_time',
                    'late' => 'attendance_late',
                    'absent' => 'attendance_absent',
                    default => null,
                };

                if ($action) {
                    $reason = match($status) {
                        'present' => "Present for session #{$session->id}",
                        'late' => "Late for session #{$session->id}",
                        'absent' => "Absent from session #{$session->id}",
                    };
                    ScoreService::award(
                        user: $student,
                        actionName: $action,
                        reason: $reason,
                        courseId: $course->id,
                        createdBy: $user,
                        related: ['type' => \App\Models\ClassSession::class, 'id' => $session->id],
                        notify: false
                    );
                }
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            $sessionIds = \App\Models\ClassSession::where('course_id', $course->id)
                ->where('status', 'ended')->pluck('id');

            $enrollments = $course->enrollments()->where('status', 'active')->with('user')->get();

            $summary = $enrollments->map(function ($en) use ($sessionIds) {
                $total   = $sessionIds->count();
                $present = \DB::table('session_attendances')->whereIn('session_id', $sessionIds)->where('user_id', $en->user_id)->where('status', 'present')->count();
                $late    = \DB::table('session_attendances')->whereIn('session_id', $sessionIds)->where('user_id', $en->user_id)->where('status', 'late')->count();
                $absent  = \DB::table('session_attendances')->whereIn('session_id', $sessionIds)->where('user_id', $en->user_id)->where('status', 'absent')->count();
                $rate    = $total > 0 ? round((($present + $late) / $total) * 100) : 0;
                return ['user_id' => $en->user_id, 'present' => $present, 'late' => $late, 'absent' => $absent, 'rate' => $rate];
            });

            $overallRate  = $summary->count() > 0 ? round($summary->avg('rate')) : 0;
            $riskCount    = $summary->where('rate', '<', 60)->count();

            return response()->json([
                'success'     => true,
                'message'     => 'Attendance saved successfully.',
                'summary'     => $summary->values(),
                'overallRate' => $overallRate,
                'riskCount'   => $riskCount,
            ]);
        }

        return back()->with('att_success', 'Attendance saved successfully.');
    }

    public function exportPdf($id)
    {
        $user = Auth::user();

        $course = Course::with([
            'category',
            'teacher',
            'lessons' => fn($q) => $q->orderBy('order'),
        ])->where('teacher_id', $user->id)->findOrFail($id);

        $teacher = \DB::table('teachers')->where('user_id', $user->id)->first();

        $enrollments = \DB::table('enrollments')
            ->join('users', 'users.id', '=', 'enrollments.user_id')
            ->where('enrollments.course_id', $id)
            ->select(
                'users.name', 'users.email',
                'enrollments.status', 'enrollments.progress_percentage',
                'enrollments.created_at as enrolled_at'
            )
            ->get();

        $pastSessions = \App\Models\ClassSession::where('course_id', $id)
            ->where('status', 'ended')
            ->orderBy('started_at')
            ->get();

        $documents = \App\Models\CourseDocument::with('lesson')
            ->where('course_id', $id)
            ->orderByDesc('created_at')
            ->get();

        $totalSessionMinutes = (int) $pastSessions->sum(fn($s) =>
            $s->started_at && $s->ended_at
                ? (int) $s->started_at->diffInMinutes($s->ended_at)
                : 0
        );

        $totalCourseMinutes  = ($course->duration_hours ?? 0) * 60;
        $remainingMinutes    = max(0, $totalCourseMinutes - $totalSessionMinutes);

        $avgProgress = $enrollments->count()
            ? round($enrollments->avg('progress_percentage'), 1)
            : 0;

        $pdf = Pdf::loadView('teacher.course-report-pdf', compact(
            'course', 'teacher', 'user',
            'enrollments', 'pastSessions', 'documents',
            'totalSessionMinutes', 'remainingMinutes', 'avgProgress'
        ))->setPaper('a4', 'portrait');

        $filename = 'course-report-' . \Str::slug($course->title) . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    public function exportAttendancePdf($id)
    {
        $user = Auth::user();

        $course = Course::with(['category', 'lessons' => fn($q) => $q->orderBy('order')])
            ->where('teacher_id', $user->id)
            ->findOrFail($id);

        $enrollments = \DB::table('enrollments')
            ->join('users', 'users.id', '=', 'enrollments.user_id')
            ->where('enrollments.course_id', $id)
            ->select('users.id', 'users.name', 'users.email', 'enrollments.status', 'enrollments.progress_percentage', 'enrollments.created_at as enrolled_at')
            ->get();

        $pastSessions = \App\Models\ClassSession::where('course_id', $id)
            ->where('status', 'ended')
            ->orderBy('started_at')
            ->get();

        $sessionIds = $pastSessions->pluck('id');

        // Per-student attendance summary
        $attendanceSummary = $enrollments->map(function ($en) use ($sessionIds, $pastSessions) {
            $total   = $sessionIds->count();
            $rows    = \DB::table('session_attendances')
                ->whereIn('session_id', $sessionIds)
                ->where('user_id', $en->id)
                ->pluck('status', 'session_id');
            $present = collect($rows)->filter(fn($v) => $v === 'present')->count();
            $late    = collect($rows)->filter(fn($v) => $v === 'late')->count();
            $absent  = $total - $present - $late;

            // per-session detail
            $perSession = $pastSessions->map(fn($s) => [
                'session_id' => $s->id,
                'status'     => $rows[$s->id] ?? 'absent',
            ]);

            return (object)[
                'id'         => $en->id,
                'name'       => $en->name,
                'email'      => $en->email,
                'total'      => $total,
                'present'    => $present,
                'late'       => $late,
                'absent'     => max(0, $absent),
                'rate'       => $total > 0 ? round((($present + $late) / $total) * 100) : 0,
                'perSession' => $perSession,
            ];
        });

        $overallRate = $attendanceSummary->count() > 0 ? round($attendanceSummary->avg('rate')) : 0;

        $pdf = Pdf::loadView('teacher.attendance-pdf', compact(
            'course', 'user', 'enrollments', 'pastSessions', 'attendanceSummary', 'overallRate'
        ))->setPaper('a4', 'landscape');

        $filename = 'attendance-' . \Str::slug($course->title) . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    public function startClass(Request $request, $id)
    {
        $request->validate([
            'meet_link' => 'required|string'
        ]);

        $user   = Auth::user();
        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        $roomUrl = trim($request->input('meet_link'));
        if (!preg_match("~^(?:f|ht)tps?://~i", $roomUrl)) {
            $roomUrl = "https://" . $roomUrl;
        }

        // End any active sessions first
        \App\Models\ClassSession::where('course_id', $course->id)
            ->where('status', 'active')
            ->update(['status' => 'ended', 'ended_at' => now()]);

        // Get lesson_id if provided
        $lessonId = $request->input('lesson_id');

        $session = \App\Models\ClassSession::create([
            'course_id'  => $course->id,
            'lesson_id'  => $lessonId,
            'started_by' => $user->id,
            'meet_link'  => $roomUrl,
            'room_name'  => 'Google Meet',
            'status'     => 'active',
            'started_at' => now(),
        ]);

        // Notify enrolled students via database notifications
        $enrolledUserIds = $course->enrollments()->where('status', 'active')->pluck('user_id');
        foreach ($enrolledUserIds as $studentId) {
            NotificationController::notifyClassStarted($studentId, $course->title, $roomUrl, $course->slug);
        }

        // Prepare success message with lesson info
        $lessonInfo = '';
        if ($lessonId) {
            $lesson = \App\Models\Lesson::find($lessonId);
            if ($lesson) {
                $lessonInfo = " (Lesson: {$lesson->title})";
            }
        }

        return back()->with('session_success', "Class started with Google Meet!{$lessonInfo} Students notified.");
    }

    public function joinClass(Request $request, $id)
    {
        $user   = Auth::user();
        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        $session = \App\Models\ClassSession::where('course_id', $course->id)
            ->where('status', 'active')
            ->latest('started_at')
            ->first();

        abort_if(is_null($session), 404, 'No active session found.');

        return redirect()->away($session->meet_link);
    }

    public function leaveClass(Request $request, $id)
    {
        return redirect()->route('teacher.courses.detail', $id);
    }

    public function endClass(Request $request, $id)
    {
        $user   = Auth::user();
        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        $request->validate([
            'attendees_count' => ['nullable', 'integer', 'min:0'],
            'note'            => ['nullable', 'string', 'max:300'],
        ]);

        $attendeesCount = $request->input('attendees_count');
        $attendeesCount = is_numeric($attendeesCount) ? (int)$attendeesCount : 0;

        $session = \App\Models\ClassSession::where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        if ($session) {
            $session->update([
                'status'          => 'ended',
                'ended_at'        => now(),
                'attendees_count' => $attendeesCount,
                'note'            => $request->input('note'),
            ]);

            // Mark lesson as completed for teacher and all enrolled students (if lesson was specified)
            $completedCount = 0;
            if ($session->lesson_id) {
                // Mark for teacher
                \App\Models\CompletedLesson::firstOrCreate([
                    'user_id' => $user->id,
                    'lesson_id' => $session->lesson_id,
                ], [
                    'class_session_id' => $session->id,
                    'completed_at' => now(),
                ]);
                $completedCount++;

                // Mark for all enrolled students and sync progress_percentage
                $totalLessons = $course->lessons()->count();
                $enrolledStudents = $course->enrollments()->where('status', 'active')->get();
                foreach ($enrolledStudents as $studentEnrollment) {
                    \App\Models\CompletedLesson::firstOrCreate([
                        'user_id' => $studentEnrollment->user_id,
                        'lesson_id' => $session->lesson_id,
                    ], [
                        'class_session_id' => $session->id,
                        'completed_at' => now(),
                    ]);
                    $completedCount++;

                    // Recalculate and persist progress_percentage
                    if ($totalLessons > 0) {
                        $lessonIds = $course->lessons()->pluck('id');
                        $doneCount = \App\Models\CompletedLesson::where('user_id', $studentEnrollment->user_id)
                            ->whereIn('lesson_id', $lessonIds)
                            ->count();
                        $newProgress = (int) round(($doneCount / $totalLessons) * 100);
                        $studentEnrollment->update([
                            'progress_percentage' => $newProgress,
                            'status' => $newProgress >= 100 ? 'completed' : 'active',
                            'completed_at' => $newProgress >= 100 ? now() : null,
                        ]);
                    }
                }
                $enrolledUserIds = $enrolledStudents->pluck('user_id');

                // Log activity for streak
                if (!$user->activities()->whereDate('created_at', now()->toDateString())->first()) {
                    $user->activities()->create([
                        'type' => 'lesson_completed',
                        'icon' => 'bi-check-circle-fill',
                        'message' => "Taught lesson: " . \App\Models\Lesson::find($session->lesson_id)->title,
                        'color' => '#22c55e',
                    ]);
                }
            }

            $lessonInfo = '';
            if ($session->lesson_id) {
                $lesson = \App\Models\Lesson::find($session->lesson_id);
                if ($lesson) {
                    $lessonInfo = " Lesson '{$lesson->title}' marked as completed for {$completedCount} users.";
                }
            }
        }

        return back()->with('session_success', 'Class session ended.' . $lessonInfo);
    }

    /**
     * Auto-close inactive sessions (no participants for 5+ minutes)
     */
    public function autoCloseInactiveSessions()
    {
        $inactiveThreshold = now()->subMinutes(5);

        // Find sessions that have been active for more than 5 minutes
        // and either have no participants or haven't been updated
        $inactiveSessions = \App\Models\ClassSession::where('status', 'active')
            ->where('started_at', '<', $inactiveThreshold)
            ->where(function ($q) use ($inactiveThreshold) {
                $q->whereNull('last_participant_at')
                  ->orWhere('last_participant_at', '<', $inactiveThreshold);
            })
            ->get();

        foreach ($inactiveSessions as $session) {
            $session->update([
                'status'   => 'ended',
                'ended_at' => now(),
                'note'     => 'Auto-closed due to inactivity (no participants for 5+ minutes)',
            ]);
        }

        return response()->json([
            'closed_count' => $inactiveSessions->count(),
            'message'      => $inactiveSessions->count() > 0 ? 'Inactive sessions closed.' : 'No inactive sessions found.',
        ]);
    }

    /**
     * Update last participant activity timestamp
     */
    public function updateParticipantActivity(Request $request, $sessionId)
    {
        $session = \App\Models\ClassSession::findOrFail($sessionId);

        $session->update([
            'last_participant_at' => now(),
            'participants_count'  => $request->input('count', 1),
        ]);

        return response()->json(['success' => true]);
    }

    public function saveRecording(Request $request, $id, $lessonId)
    {
        $user   = Auth::user();
        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        $request->validate([
            'video_url' => ['required', 'url', 'max:500'],
        ]);

        $lesson = \App\Models\Lesson::where('course_id', $course->id)->findOrFail($lessonId);
        $lesson->update(['video_url' => $request->video_url]);

        return back()->with('recording_saved', 'Recording link saved for "' . $lesson->title . '".');
    }

    public function uploadDocument(Request $request, $id)
    {
        $user = Auth::user();

        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'lesson_id'   => ['nullable', 'exists:lessons,id'],
            'file'        => ['required', 'file', 'mimes:pdf', 'max:3072'],
        ]);

        $file     = $request->file('file');
        $path     = $file->store('course-documents/' . $id, 'public');

        \App\Models\CourseDocument::create([
            'course_id'   => $course->id,
            'lesson_id'   => $request->lesson_id ?: null,
            'uploaded_by' => $user->id,
            'title'       => $request->title,
            'description' => $request->description,
            'file_path'   => $path,
            'file_name'   => $file->getClientOriginalName(),
            'file_size'   => $file->getSize(),
        ]);

        // Notify enrolled students
        $enrolledUserIds = $course->enrollments()->where('status', 'active')->pluck('user_id');
        foreach ($enrolledUserIds as $studentId) {
            NotificationController::notifyDocumentUploaded($studentId, $course->title, $request->title, $course->slug);
        }

        return back()->with('doc_success', 'Document uploaded successfully.');
    }

    public function deleteDocument(Request $request, $id, $docId)
    {
        $user = Auth::user();

        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        $doc = \App\Models\CourseDocument::where('course_id', $course->id)->findOrFail($docId);

        \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->file_path);
        $doc->delete();

        return back()->with('doc_success', 'Document deleted.');
    }

    public function submitOnboarding(Request $request)
    {
        $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'bio'                => ['required', 'string', 'min:50', 'max:1000'],
            'department'         => ['required', 'string', 'max:255'],
            'experience_years'   => ['required', 'string'],
            'specialization'     => ['required', 'string', 'max:255'],
            'expertise'          => ['required', 'string', 'max:500'],
            'phone'              => ['nullable', 'string', 'max:20'],
            'linkedin'           => ['nullable', 'url', 'max:255'],
            'github'             => ['nullable', 'url', 'max:255'],
            'website'            => ['nullable', 'url', 'max:255'],
            'avatar'             => ['nullable', 'image', 'max:2048'],
            'cv'                 => ['nullable', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        // Validate max 10 skills
        $skills = array_filter(explode(',', $request->expertise));
        if (count($skills) > 10) {
            return back()->withErrors(['expertise' => 'You can select up to 10 skills only.'])->withInput();
        }

        $user = Auth::user();

        $avatarPath = $user->avatar;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $cvPath = $user->cv_path;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cvs', 'public');
        }

        $user->update([
            'name'             => $request->name,
            'bio'              => $request->bio,
            'department'       => $request->department,
            'experience_years' => $request->experience_years,
            'phone'            => $request->phone,
            'avatar'           => $avatarPath,
            'cv_path'          => $cvPath,
            'status'           => 'pending',
        ]);

        Teacher::updateOrCreate(
            ['user_id' => $user->id],
            [
                'specialization'     => $request->specialization,
                'expertise'          => $request->expertise,
                'years_of_experience'=> (int) $request->experience_years,
                'linkedin'           => $request->linkedin,
                'github'             => $request->github,
                'website'            => $request->website,
                'is_verified'        => false,
            ]
        );

        Mail::to($user->email)->send(new TeacherApplicationSubmitted($user));

        return redirect()->route('teacher.dashboard')->with('onboarding_submitted', true);
    }

    public function storeClassNote(Request $request, $id)
    {
        $user   = Auth::user();
        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        $request->validate([
            'class_date' => ['required', 'date'],
            'title'      => ['nullable', 'string', 'max:255'],
            'content'    => ['required', 'string', 'max:3000'],
        ]);

        $note = ClassNote::create([
            'course_id'  => $course->id,
            'teacher_id' => $user->id,
            'class_date' => $request->class_date,
            'title'      => $request->title,
            'content'    => $request->content,
        ]);

        // Notify enrolled students
        $enrolledUserIds = $course->enrollments()->where('status', 'active')->pluck('user_id');
        foreach ($enrolledUserIds as $studentId) {
            NotificationController::notifyClassNoteAdded($studentId, $course->title, $request->title, $course->slug);
        }

        return back()->with('note_success', 'Class note saved successfully.');
    }

    public function updateClassNote(Request $request, $id, $noteId)
    {
        $user   = Auth::user();
        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        $request->validate([
            'class_date' => ['required', 'date'],
            'title'      => ['nullable', 'string', 'max:255'],
            'content'    => ['required', 'string', 'max:3000'],
        ]);

        $note = ClassNote::where('course_id', $course->id)->findOrFail($noteId);
        $note->update([
            'class_date' => $request->class_date,
            'title'      => $request->title,
            'content'    => $request->content,
        ]);

        return back()->with('note_success', 'Class note updated.');
    }

    public function deleteClassNote(Request $request, $id, $noteId)
    {
        $user   = Auth::user();
        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        ClassNote::where('course_id', $course->id)->findOrFail($noteId)->delete();

        return back()->with('note_success', 'Class note deleted.');
    }

    public function storeLesson(Request $request, $id)
    {
        $user   = Auth::user();
        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string|max:1000',
            'duration_minutes' => 'nullable|integer|min:1|max:600',
            'is_free'          => 'nullable|boolean',
        ]);

        $maxOrder = $course->lessons()->max('order') ?? 0;

        $course->lessons()->create([
            'title'            => $request->title,
            'description'      => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'order'            => $maxOrder + 1,
            'is_free'          => $request->boolean('is_free'),
        ]);

        return back()->with('lesson_success', 'Lesson added successfully.');
    }

    public function updateLesson(Request $request, $id, $lessonId)
    {
        $user   = Auth::user();
        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string|max:1000',
            'duration_minutes' => 'nullable|integer|min:1|max:600',
            'order'            => 'nullable|integer|min:1',
            'is_free'          => 'nullable|boolean',
        ]);

        $lesson = Lesson::where('course_id', $course->id)->findOrFail($lessonId);

        $lesson->update([
            'title'            => $request->title,
            'description'      => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'order'            => $request->order ?? $lesson->order,
            'is_free'          => $request->boolean('is_free'),
        ]);

        return back()->with('lesson_success', 'Lesson updated successfully.');
    }

    public function deleteLesson(Request $request, $id, $lessonId)
    {
        $user   = Auth::user();
        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        Lesson::where('course_id', $course->id)->findOrFail($lessonId)->delete();

        return back()->with('lesson_success', 'Lesson deleted successfully.');
    }

    public function banStudent(Request $request, $id, $userId)
    {
        $user   = Auth::user();
        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        \App\Models\Enrollment::where('course_id', $course->id)
            ->where('user_id', $userId)
            ->firstOrFail()
            ->update(['status' => 'banned']);

        return back()->with('student_action', 'Student has been banned from this course.');
    }

    public function unbanStudent(Request $request, $id, $userId)
    {
        $user   = Auth::user();
        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        \App\Models\Enrollment::where('course_id', $course->id)
            ->where('user_id', $userId)
            ->firstOrFail()
            ->update(['status' => 'active']);

        return back()->with('student_action', 'Student has been reinstated to this course.');
    }

    /**
     * Show the scoring management page for a course.
     */
    public function points($id)
    {
        $user   = Auth::user();
        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        $enrollments = $course->enrollments()
            ->where('status', '!=', 'banned')
            ->with('user')
            ->get();

        $studentIds = $enrollments->pluck('user_id');

        $points = Point::with('creator')
            ->whereIn('user_id', $studentIds)
            ->where(function ($q) use ($course) {
                $q->where('course_id', $course->id)->orWhereNull('course_id');
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        $students = User::whereIn('id', $studentIds)
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn($s) => [$s->id => $s]);

        $rules = ScoringRule::active()->orderBy('label')->get();

        return view('teacher.points', compact('course', 'enrollments', 'points', 'students', 'rules'));
    }

    /**
     * Store a manual point adjustment for a student.
     */
    public function storePoints(Request $request, $id)
    {
        $user   = Auth::user();
        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'amount' => ['required', 'integer', 'not_in:0'],
            'reason' => ['required', 'string', 'max:500'],
            'type' => ['nullable', 'string', 'max:50'],
        ]);

        $student = User::where('role', 'student')->findOrFail($request->input('user_id'));

        // Ensure student is enrolled in this course
        $course->enrollments()
            ->where('user_id', $student->id)
            ->where('status', '!=', 'banned')
            ->firstOrFail();

        $type = $request->input('type', 'manual');

        ScoreService::adjust(
            user: $student,
            amount: (int) $request->input('amount'),
            type: $type,
            reason: $request->input('reason'),
            courseId: $course->id,
            createdBy: $user,
            notify: true
        );

        return back()->with('points_success', "Points updated for {$student->name}.");
    }

    /**
     * Show a single student's full scoring history.
     */
    public function studentPointsHistory($id, $userId)
    {
        $user   = Auth::user();
        $course = Course::where('teacher_id', $user->id)->findOrFail($id);

        $course->enrollments()
            ->where('user_id', $userId)
            ->where('status', '!=', 'banned')
            ->firstOrFail();

        $student = User::findOrFail($userId);
        $history = Point::with('creator', 'course')
            ->where('user_id', $student->id)
            ->where(function ($q) use ($course) {
                $q->where('course_id', $course->id)->orWhereNull('course_id');
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        $totalScore = $student->totalScore();
        $earned = $student->earnedPoints();
        $deducted = $student->deductedPoints();
        $rank = $student->leaderboardRank();

        return view('teacher.student-points-history', compact(
            'course', 'student', 'history', 'totalScore', 'earned', 'deducted', 'rank'
        ));
    }
}
