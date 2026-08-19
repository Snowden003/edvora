<?php

namespace App\Http\Controllers;

use App\Events\CourseMessageCreated;
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
            'user_id' => $user->id,
            'body' => trim($validated['body']),
        ])->load('user:id,name,avatar,role');

        broadcast(new CourseMessageCreated($message))->toOthers();

        return response()->json(['message' => $this->messagePayload($message)], 201);
    }

    private function authorizeParticipant(Course $course): User
    {
        $user = Auth::user();
        $isTeacher = (int) $course->teacher_id === (int) $user->id;
        $isEnrolled = $user->enrollments()
            ->where('course_id', $course->id)
            ->where('status', '!=', 'banned')
            ->exists();

        abort_unless($isTeacher || $isEnrolled, 403);

        return $user;
    }

    private function messagePayload(CourseMessage $message): array
    {
        $avatar = $message->user->avatar;

        return [
            'id' => $message->id,
            'body' => $message->body,
            'created_at' => $message->created_at->toIso8601String(),
            'user' => [
                'id' => $message->user->id,
                'name' => $message->user->name,
                'avatar' => $avatar ? (str_starts_with($avatar, 'http') ? $avatar : asset('storage/' . $avatar)) : null,
                'role' => $message->user->role,
            ],
        ];
    }
}
