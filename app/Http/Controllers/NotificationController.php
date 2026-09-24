<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = Notification::where('user_id', $user->id)
            ->unread()
            ->count();

        // If request is from Inertia or user is student, render Inertia view
        if ($request->header('X-Inertia') || $user->role === 'student') {
            $formattedNotifications = [
                'data' => collect($notifications->items())->map(function ($n) use ($user) {
                    $data = is_array($n->data) ? $n->data : json_decode($n->data ?? '[]', true);
                    $actionUrl = null;
                    $actionText = 'View';

                    if ($n->type === Notification::TYPE_CLASS_STARTED) {
                        $actionUrl = !empty($data['course_slug'])
                            ? route('student.courses.sessions.join', $data['course_slug'])
                            : ($data['meet_link'] ?? $data['room_url'] ?? null);
                        $actionText = 'Join Google Meet';
                    } elseif ($n->type === Notification::TYPE_ENROLLMENT_REQUEST) {
                        $actionUrl = route('teacher.enrollment-requests') . '?tab=requests';
                        $actionText = 'Review Request';
                    } elseif ($n->type === Notification::TYPE_STUDENT_ENROLLED) {
                        $actionUrl = route('teacher.enrollment-requests') . '?tab=enrolled';
                        $actionText = 'View Students';
                    } elseif ($n->type === Notification::TYPE_ENROLLMENT_APPROVED && !empty($data['course_slug'])) {
                        $actionUrl = route('student.courses.learn', $data['course_slug']);
                        $actionText = 'Go to Classroom';
                    } elseif ($n->type === Notification::TYPE_EXAM_PUBLISHED) {
                        $actionUrl = route('student.exams.index');
                        $actionText = 'Take Quiz';
                    } elseif (!empty($data['course_slug'])) {
                        $actionUrl = route('student.courses.learn', $data['course_slug']);
                        $actionText = 'Open Course';
                    }

                    return [
                        'id'          => $n->id,
                        'type'        => $n->type,
                        'title'       => $n->title,
                        'message'     => $n->message,
                        'icon'        => $n->icon,
                        'type_label'  => $n->type_label,
                        'data'        => $data,
                        'action_url'  => $actionUrl,
                        'action_text' => $actionText,
                        'is_read'     => (bool) $n->is_read,
                        'created_at'  => $n->created_at ? $n->created_at->toISOString() : now()->toISOString(),
                        'time_ago'    => $n->created_at ? $n->created_at->diffForHumans() : 'Just now',
                    ];
                }),
                'links' => $notifications->linkCollection()->toArray(),
            ];

            return \Inertia\Inertia::render('Student/Notifications', [
                'notifications' => $formattedNotifications,
                'unreadCount'   => $unreadCount,
            ]);
        }

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    // Check for new notifications since timestamp (for real-time polling)
    public function checkNew(Request $request)
    {
        return $this->liveCheck($request);
    }

    /**
     * Unified real-time polling endpoint for all authenticated users (students & teachers).
     * Returns:
     * - unread_count
     * - notifications (recent unread with rich actionable URLs & icons)
     * - active_class (for students: currently live Google Meet session if any)
     */
    public function liveCheck(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['unread_count' => 0, 'notifications' => [], 'active_class' => null]);
        }

        $since = $request->query('since');

        $unreadCount = Notification::where('user_id', $user->id)
            ->unread()
            ->count();

        $query = Notification::where('user_id', $user->id)
            ->unread()
            ->orderBy('created_at', 'desc');

        if ($since) {
            $query->where('created_at', '>', $since);
        }

        $notifications = $query->limit(10)->get()->map(function ($n) use ($user) {
            $data = is_array($n->data) ? $n->data : json_decode($n->data ?? '[]', true);
            $actionUrl = null;
            $actionText = 'View';

            if ($n->type === Notification::TYPE_CLASS_STARTED) {
                $actionUrl = !empty($data['course_slug'])
                    ? route('student.courses.sessions.join', $data['course_slug'])
                    : ($data['meet_link'] ?? $data['room_url'] ?? null);
                $actionText = 'Join Google Meet';
            } elseif ($n->type === Notification::TYPE_ENROLLMENT_REQUEST) {
                $actionUrl = route('teacher.enrollment-requests') . '?tab=requests';
                $actionText = 'Review Request';
            } elseif ($n->type === Notification::TYPE_STUDENT_ENROLLED) {
                $actionUrl = route('teacher.enrollment-requests') . '?tab=enrolled';
                $actionText = 'View Students';
            } elseif ($n->type === Notification::TYPE_ENROLLMENT_APPROVED && !empty($data['course_slug'])) {
                $actionUrl = route('student.courses.learn', $data['course_slug']);
                $actionText = 'Go to Classroom';
            } elseif ($n->type === Notification::TYPE_EXAM_PUBLISHED) {
                $actionUrl = route('student.exams.index');
                $actionText = 'Take Quiz';
            } elseif (!empty($data['course_slug'])) {
                $actionUrl = route('student.courses.learn', $data['course_slug']);
                $actionText = 'Open Course';
            }

            return [
                'id'          => $n->id,
                'type'        => $n->type,
                'title'       => $n->title,
                'message'     => $n->message,
                'icon'        => $n->icon,
                'type_label'  => $n->type_label,
                'data'        => $data,
                'action_url'  => $actionUrl,
                'action_text' => $actionText,
                'is_read'     => (bool) $n->is_read,
                'created_at'  => $n->created_at ? $n->created_at->toISOString() : now()->toISOString(),
                'time_ago'    => $n->created_at ? $n->created_at->diffForHumans() : 'Just now',
            ];
        });

        // Check if user is a student with an active class session currently running
        $activeClass = null;
        if ($user->role === 'student') {
            // Clean up any abandoned sessions older than 3 minutes without attendees
            \App\Models\ClassSession::checkAndCloseExpiredSessions();

            $enrolledCourseIds = $user->enrollments()
                ->where('status', 'active')
                ->pluck('course_id');

            $activeSession = \App\Models\ClassSession::whereIn('course_id', $enrolledCourseIds)
                ->where('status', 'active')
                ->with('course')
                ->latest('started_at')
                ->first();

            if ($activeSession && $activeSession->course) {
                $activeClass = [
                    'session_id'   => $activeSession->id,
                    'course_id'    => $activeSession->course_id,
                    'course_title' => $activeSession->course->title,
                    'course_slug'  => $activeSession->course->slug,
                    'meet_link'    => $activeSession->meet_link,
                    'join_url'     => route('student.courses.sessions.join', $activeSession->course->slug),
                    'started_at'   => $activeSession->started_at ? $activeSession->started_at->toISOString() : null,
                ];
            }
        }

        return response()->json([
            'unread_count'  => $unreadCount,
            'notifications' => $notifications,
            'active_class'  => $activeClass,
            'timestamp'     => now()->toISOString(),
        ]);
    }

    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    public function destroyAll()
    {
        Notification::where('user_id', Auth::id())->delete();

        return response()->json(['success' => true]);
    }

    // Helper methods to create different types of notifications
    public static function createCourseReminder($userId, $courseId, $courseTitle, $startTime)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => Notification::TYPE_COURSE_REMINDER,
            'title' => 'Class Starting Soon',
            'message' => "Your class '{$courseTitle}' starts in 10 minutes at {$startTime->format('H:i')}.",
            'data' => [
                'course_id' => $courseId,
                'start_time' => $startTime->toISOString(),
            ],
        ]);
    }

    public static function createAdminMessage($userId, $title, $message)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => Notification::TYPE_ADMIN_MESSAGE,
            'title' => $title,
            'message' => $message,
            'data' => [],
        ]);
    }

    public static function createStudentMessage($userId, $studentName, $message, $courseId = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => Notification::TYPE_STUDENT_MESSAGE,
            'title' => "New message from {$studentName}",
            'message' => $message,
            'data' => [
                'student_name' => $studentName,
                'course_id' => $courseId,
            ],
        ]);
    }

    public static function createMeetingStatus($userId, $meetingTitle, $status, $meetingId = null)
    {
        $statusText = match($status) {
            'started' => 'has started',
            'ended' => 'has ended',
            'cancelled' => 'has been cancelled',
            default => 'status updated',
        };

        return Notification::create([
            'user_id' => $userId,
            'type' => Notification::TYPE_MEETING_STATUS,
            'title' => 'Meeting Update',
            'message' => "Your meeting '{$meetingTitle}' {$statusText}.",
            'data' => [
                'meeting_id' => $meetingId,
                'status' => $status,
            ],
        ]);
    }

    public static function notifyExamPublished($userId, $courseTitle, $quizTitle, $quizId, $courseSlug)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => Notification::TYPE_EXAM_PUBLISHED,
            'title' => 'New Exam Available',
            'message' => "A new exam '{$quizTitle}' has been published in '{$courseTitle}'.",
            'data' => [
                'quiz_id' => $quizId,
                'course_slug' => $courseSlug,
            ],
        ]);
    }

    public static function notifyClassStarted($userId, $courseTitle, $meetLink, $courseSlug)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => Notification::TYPE_CLASS_STARTED,
            'title' => 'Class Started',
            'message' => "Your class for '{$courseTitle}' has started. Join now!",
            'data' => [
                'course_slug' => $courseSlug,
                'meet_link' => $meetLink,
                'room_url' => $meetLink,
            ],
        ]);
    }

    public static function notifyDocumentUploaded($userId, $courseTitle, $docTitle, $courseSlug)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => Notification::TYPE_DOCUMENT_UPLOADED,
            'title' => 'New Document Uploaded',
            'message' => "A new file '{$docTitle}' has been uploaded in '{$courseTitle}'.",
            'data' => [
                'course_slug' => $courseSlug,
            ],
        ]);
    }

    public static function notifyClassNoteAdded($userId, $courseTitle, $noteTitle, $courseSlug)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => Notification::TYPE_CLASS_NOTE_ADDED,
            'title' => 'New Class Note',
            'message' => "A new note" . ($noteTitle ? " '{$noteTitle}'" : "") . " has been added in '{$courseTitle}'.",
            'data' => [
                'course_slug' => $courseSlug,
            ],
        ]);
    }

    public static function notifyClassEnded($userId, $courseTitle)
    {
        return Notification::create([
            'user_id' => $userId,
            'type'    => Notification::TYPE_CLASS_ENDED,
            'title'   => 'Class Ended',
            'message' => "The live class for '{$courseTitle}' has ended.",
            'data'    => [
                'course_title' => $courseTitle,
            ],
        ]);
    }

    public static function notifyEnrollmentApproved($studentId, $courseTitle, $courseSlug)
    {
        return Notification::create([
            'user_id' => $studentId,
            'type'    => Notification::TYPE_ENROLLMENT_APPROVED,
            'title'   => 'Enrollment Approved! 🎉',
            'message' => "Your enrollment in '{$courseTitle}' has been approved! You can now access all course lessons and live classes.",
            'data'    => [
                'course_title' => $courseTitle,
                'course_slug'  => $courseSlug,
            ],
        ]);
    }

    public static function notifyEnrollmentRejected($studentId, $courseTitle, $reason = null)
    {
        $message = "Your enrollment request for '{$courseTitle}' was not approved.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }

        return Notification::create([
            'user_id' => $studentId,
            'type'    => Notification::TYPE_ENROLLMENT_REJECTED,
            'title'   => 'Enrollment Update',
            'message' => $message,
            'data'    => [
                'course_title' => $courseTitle,
                'reason'       => $reason,
            ],
        ]);
    }

    public static function notifyStudentBanned($studentId, $courseTitle, $teacherName, $reason = null)
    {
        $message = "You have been suspended from '{$courseTitle}' by Instructor {$teacherName}.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }

        return Notification::create([
            'user_id' => $studentId,
            'type'    => Notification::TYPE_STUDENT_BANNED,
            'title'   => 'Course Access Suspended 🚫',
            'message' => $message,
            'data'    => [
                'course_title' => $courseTitle,
                'teacher_name' => $teacherName,
                'reason'       => $reason,
            ],
        ]);
    }

    public static function notifyStudentUnbanned($studentId, $courseTitle, $teacherName)
    {
        return Notification::create([
            'user_id' => $studentId,
            'type'    => Notification::TYPE_STUDENT_UNBANNED,
            'title'   => 'Course Access Reinstated ✅',
            'message' => "Your access to '{$courseTitle}' has been reinstated by Instructor {$teacherName}. You can resume learning!",
            'data'    => [
                'course_title' => $courseTitle,
                'teacher_name' => $teacherName,
            ],
        ]);
    }

    public static function notifyStudentEnrolled($teacherId, $studentName, $courseTitle, $courseId)
    {
        return Notification::create([
            'user_id' => $teacherId,
            'type'    => Notification::TYPE_STUDENT_ENROLLED,
            'title'   => 'New Student Enrolled 🎓',
            'message' => "{$studentName} has enrolled in your course '{$courseTitle}'.",
            'data'    => [
                'course_id'    => $courseId,
                'course_title' => $courseTitle,
                'student_name' => $studentName,
            ],
        ]);
    }

    public static function notifyEnrollmentRequest($teacherId, $studentName, $courseTitle, $courseId)
    {
        return Notification::create([
            'user_id' => $teacherId,
            'type'    => Notification::TYPE_ENROLLMENT_REQUEST,
            'title'   => 'New Enrollment Request',
            'message' => "{$studentName} has requested to enroll in '{$courseTitle}'.",
            'data'    => [
                'course_id' => $courseId,
            ],
        ]);
    }
}
