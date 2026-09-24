<?php

namespace App\Http\Controllers;

use App\Events\CourseMessageCreated;
use App\Events\CourseMessageDeleted;
use App\Events\CourseMessageUpdated;
use App\Models\Course;
use App\Models\CourseMessage;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseChatController extends Controller
{
    public function show(Course $course): View
    {
        $user = $this->authorizeParticipant($course);

        return view('courses.chat', compact('course', 'user'));
    }

    public function index(Course $course): JsonResponse
    {
        $this->authorizeParticipant($course);

        $messages = CourseMessage::query()
            ->with('user:id,name,avatar,role')
            ->where('course_id', $course->id)
            ->latest('id')
            ->limit(100)
            ->get()
            ->reverse()
            ->values()
            ->map(fn (CourseMessage $message) => $this->messagePayload($message));

        return response()->json(['messages' => $messages]);
    }

    public function store(Request $request, Course $course): JsonResponse
    {
        $user = $this->authorizeParticipant($course);
        $validated = $request->validate(['body' => ['required', 'string', 'max:2000']]);

        $message = CourseMessage::create([
            'course_id' => $course->id,
            'user_id'   => $user->id,
            'body'      => trim($validated['body']),
        ])->load('user:id,name,avatar,role');

        broadcast(new CourseMessageCreated($message))->toOthers();

        return response()->json(['message' => $this->messagePayload($message)], 201);
    }

    public function update(Request $request, Course $course, CourseMessage $message): JsonResponse
    {
        $user = $this->authorizeParticipant($course);
        abort_unless((int) $message->course_id === (int) $course->id, 404);

        $isAuthor = (int) $message->user_id === (int) $user->id;
        $isAdmin = ($user->role ?? null) === 'admin';
        abort_unless($isAuthor || $isAdmin, 403);

        $validated = $request->validate(['body' => ['required', 'string', 'max:2000']]);

        $message->update([
            'body'      => trim($validated['body']),
            'is_edited' => true,
        ]);
        $message->load('user:id,name,avatar,role');

        broadcast(new CourseMessageUpdated($message))->toOthers();

        return response()->json(['message' => $this->messagePayload($message)]);
    }

    public function destroy(Course $course, CourseMessage $message): JsonResponse
    {
        $user = $this->authorizeParticipant($course);
        abort_unless((int) $message->course_id === (int) $course->id, 404);

        $isAuthor = (int) $message->user_id === (int) $user->id;
        $isTeacher = (int) $course->teacher_id === (int) $user->id;
        $isAdmin = ($user->role ?? null) === 'admin';
        abort_unless($isAuthor || $isTeacher || $isAdmin, 403);

        $messageId = $message->id;
        $message->delete();

        broadcast(new CourseMessageDeleted($course->id, $messageId))->toOthers();

        return response()->json(['success' => true]);
    }

    public function togglePin(Course $course, CourseMessage $message): JsonResponse
    {
        $user = $this->authorizeParticipant($course);
        abort_unless((int) $message->course_id === (int) $course->id, 404);

        $isAuthor = (int) $message->user_id === (int) $user->id;
        $isTeacher = (int) $course->teacher_id === (int) $user->id;
        $isAdmin = ($user->role ?? null) === 'admin';
        abort_unless($isAuthor || $isTeacher || $isAdmin, 403);

        $message->is_pinned = !$message->is_pinned;
        $message->save();
        $message->load('user:id,name,avatar,role');

        broadcast(new CourseMessageUpdated($message))->toOthers();

        return response()->json(['message' => $this->messagePayload($message)]);
    }

    private function authorizeParticipant(Course $course): User
    {
        $user = Auth::user();
        $isTeacher = (int) $course->teacher_id === (int) $user->id;
        $isEnrolled = $user->enrollments()
            ->where('course_id', $course->id)
            ->where('status', '!=', 'banned')
            ->exists();
        $isAdmin = ($user->role ?? null) === 'admin';

        abort_unless($isTeacher || $isEnrolled || $isAdmin, 403);

        return $user;
    }

    private function messagePayload(CourseMessage $message): array
    {
        $avatar = $message->user->avatar;

        return [
            'id'         => $message->id,
            'body'       => $message->body,
            'is_pinned'  => (bool) $message->is_pinned,
            'is_edited'  => (bool) $message->is_edited,
            'created_at' => $message->created_at->toIso8601String(),
            'updated_at' => $message->updated_at->toIso8601String(),
            'user'       => [
                'id'     => $message->user->id,
                'name'   => $message->user->name,
                'avatar' => $avatar ? (str_starts_with($avatar, 'http') ? $avatar : asset('storage/' . $avatar)) : null,
                'role'   => $message->user->role,
            ],
        ];
    }
}
