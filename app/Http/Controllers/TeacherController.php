<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = $this->teacherDirectoryQuery()
            ->get()
            ->map(fn (User $teacher) => $this->decorateTeacher($teacher));

        $teachingSections = $teachers
            ->flatMap(fn (User $teacher) => $teacher->teaching_sections)
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('teachers.index', compact('teachers', 'teachingSections'));
    }

    public function show($id)
    {
        $teacher = $this->teacherDirectoryQuery()
            ->whereKey($id)
            ->firstOrFail();

        $teacher = $this->decorateTeacher($teacher);

        return view('teachers.show', compact('teacher'));
    }

    private function teacherDirectoryQuery(): Builder
    {
        return User::query()
            ->where('role', 'teacher')
            ->where('status', 'active')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->whereNotNull('bio')
            ->where('bio', '!=', '')
            ->whereHas('teacher', function (Builder $query) {
                $query->where('is_verified', true)
                    ->whereNotNull('specialization')
                    ->where('specialization', '!=', '')
                    ->whereNotNull('expertise')
                    ->where('expertise', '!=', '');
            })
            ->with([
                'teacher',
                'courses' => function ($query) {
                    $query->whereIn('status', ['published', 'started', 'archived'])
                        ->with('category')
                        ->latest();
                },
            ])
            ->withCount([
                'courses as active_courses_count' => function ($query) {
                    $query->whereIn('status', ['published', 'started']);
                },
                'courses as total_courses_count' => function ($query) {
                    $query->whereIn('status', ['published', 'started', 'archived']);
                },
            ])
            ->withSum([
                'courses as total_students_count' => function ($query) {
                    $query->whereIn('status', ['published', 'started', 'archived']);
                },
            ], 'enrolled_count')
            ->latest();
    }

    private function decorateTeacher(User $teacher): User
    {
        $publishedCourses = $teacher->courses
            ->whereIn('status', ['published', 'started'])
            ->values();

        $teachingSections = $publishedCourses
            ->pluck('category.name')
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique()
            ->values();

        if ($teachingSections->isEmpty() && filled($teacher->department)) {
            $teachingSections = collect([trim((string) $teacher->department)]);
        }

        $teacher->setAttribute('avatar_url', $teacher->publicAvatarUrl());
        $teacher->setAttribute('teaching_sections', $teachingSections);
        $teacher->setAttribute('joined_display', optional($teacher->created_at)?->format('M Y'));
        $teacher->setAttribute('public_courses', $publishedCourses);
        $teacher->setAttribute('display_rating', (float) ($teacher->teacher->rating ?? 0));
        $teacher->setAttribute('display_experience_years', $teacher->experience_years ?? $teacher->teacher->years_of_experience);
        $teacher->setAttribute(
            'expertise_list',
            collect(explode(',', (string) ($teacher->teacher->expertise ?? '')))
                ->map(fn ($item) => trim($item))
                ->filter()
                ->unique()
                ->values()
        );
        $teacher->setAttribute(
            'has_contact_links',
            filled($teacher->teacher->linkedin)
                || filled($teacher->teacher->github)
                || filled($teacher->teacher->website)
        );

        return $teacher;
    }
}
