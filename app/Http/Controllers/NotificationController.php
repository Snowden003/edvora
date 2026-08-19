<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = Notification::where('user_id', $user->id)
            ->unread()
            ->count();

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
        $since = $request->query('since');
        
        $query = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');
            
        if ($since) {
            $query->where('created_at', '>', $since);
        }
        
        $notifications = $query->limit(5)->get();
        
        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count()
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
