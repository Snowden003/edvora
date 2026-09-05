<?php

use App\Models\Course;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('course-chat.{courseId}', function ($user, $courseId) {
    $course = Course::find($courseId);

    if (!$course) {
        return false;
    }

    $isTeacher = (int) $course->teacher_id === (int) $user->id;
    $isEnrolled = $user->enrollments()
        ->where('course_id', $course->id)
        ->where('status', '!=', 'banned')
        ->exists();
    $isAdmin = ($user->role ?? null) === 'admin';

    if (!$isTeacher && !$isEnrolled && !$isAdmin) {
        return false;
    }

    return [
        'id' => $user->id,
        'name' => $user->name,
        'avatar' => $user->avatar ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar)) : null,
        'role' => $user->role,
    ];
});
