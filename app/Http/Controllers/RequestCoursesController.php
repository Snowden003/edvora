<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RequestCoursesController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $availableCourses = \DB::table('courses')
            ->join('categories', 'categories.id', '=', 'courses.category_id')
            ->where(function ($q) {
                $q->whereIn('courses.status', ['draft', 'pending'])
                  ->orWhere(function ($q2) {
                      $q2->where('courses.status', 'published')
                         ->whereNull('courses.teacher_id');
                  });
            })
            ->select(
                'courses.id', 'courses.title', 'courses.description',
                'courses.level', 'courses.duration_hours', 'courses.thumbnail',
                'categories.name as category_name', 'categories.id as category_id',
                'courses.created_at'
            )
            ->orderByDesc('courses.created_at')
            ->get();

        $myRequests = \DB::table('course_requests')
            ->join('categories', 'categories.id', '=', 'course_requests.category_id')
            ->where('course_requests.teacher_id', $user->id)
            ->select(
                'course_requests.*',
                'categories.name as category_name'
            )
            ->orderByDesc('course_requests.created_at')
            ->get();

        $categories = \DB::table('categories')->orderBy('name')->get();

        return view('teacher.request-courses', compact('availableCourses', 'myRequests', 'categories'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $request->validate([
            'course_id'   => ['required', 'exists:courses,id'],
            'note'        => ['required', 'string', 'min:20', 'max:1000'],
        ], [
            'note.required' => 'Please describe why you are a good fit for this course.',
            'note.min' => 'Your description should be at least 20 characters. Please tell us more about your experience and teaching approach.',
        ]);

        $course = \DB::table('courses')->where('id', $request->course_id)->first();

        $exists = \DB::table('course_requests')
            ->where('teacher_id', $user->id)
            ->where('title', $course->title)
            ->exists();

        if ($exists) {
            return back()->with('req_error', 'You have already requested this course.');
        }

        \DB::table('course_requests')->insert([
            'teacher_id'  => $user->id,
            'category_id' => $course->category_id,
            'course_id'   => $course->id,
            'title'       => $course->title,
            'description' => $request->note,
            'status'      => 'pending',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('req_success', 'Your request has been submitted successfully!');
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $request->validate([
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $courseRequest = \DB::table('course_requests')
            ->where('id', $id)
            ->where('teacher_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$courseRequest) {
            return back()->with('req_error', 'Request not found or cannot be edited.');
        }

        \DB::table('course_requests')
            ->where('id', $id)
            ->update([
                'description' => $request->input('description'),
                'updated_at'  => now(),
            ]);

        return back()->with('req_success', 'Your request has been updated successfully!');
    }

    public function destroy($id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $courseRequest = \DB::table('course_requests')
            ->where('id', $id)
            ->where('teacher_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$courseRequest) {
            return back()->with('req_error', 'Request not found or cannot be cancelled.');
        }

        \DB::table('course_requests')->where('id', $id)->delete();

        return back()->with('req_success', 'Your request has been cancelled successfully.');
    }
}
