<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Services\CurriculumSpreadsheetImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class AdminCourseController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Course::with(['category', 'teacher'])->withCount('enrollments');

        // Filters
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhereHas('teacher', function ($tQ) use ($search) {
                      $tQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($level = $request->input('level')) {
            $query->where('level', $level);
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($request->has('is_featured') && $request->input('is_featured') !== '') {
            $query->where('is_featured', filter_var($request->input('is_featured'), FILTER_VALIDATE_BOOLEAN));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $allowedSorts = ['title', 'created_at', 'enrolled_count', 'rating', 'level', 'status'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $courses = $query->paginate(12)->withQueryString()->through(fn($c) => [
            'id' => $c->id,
            'title' => $c->title,
            'slug' => $c->slug,
            'category_id' => $c->category_id,
            'category_name' => $c->category?->name ?? 'بدون دسته‌بندی',
            'teacher_id' => $c->teacher_id,
            'teacher_name' => $c->teacher?->name ?? 'تعیین نشده',
            'thumbnail' => $c->thumbnail ? (Str::startsWith($c->thumbnail, ['http://', 'https://']) ? $c->thumbnail : Storage::url($c->thumbnail)) : null,
            'level' => $c->level,
            'duration_hours' => $c->duration_hours,
            'status' => $c->status,
            'is_featured' => (bool)$c->is_featured,
            'enrolled_count' => $c->enrolled_count ?? $c->enrollments_count ?? 0,
            'rating' => (float)$c->rating,
            'total_reviews' => (int)$c->total_reviews,
            'start_date' => $c->start_date?->format('Y-m-d'),
            'end_date' => $c->end_date?->format('Y-m-d'),
            'created_at' => $c->created_at?->format('Y-m-d H:i'),
        ]);

        // KPI Counts
        $summary = [
            'total' => Course::count(),
            'published' => Course::where('status', 'published')->count(),
            'started' => Course::where('status', 'started')->count(),
            'draft' => Course::where('status', 'draft')->count(),
            'archived' => Course::where('status', 'archived')->count(),
            'featured' => Course::where('is_featured', true)->count(),
            'total_enrolled' => Course::sum('enrolled_count'),
        ];

        $categories = Category::select('id', 'name', 'slug')->orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->where('status', 'active')->select('id', 'name', 'email')->orderBy('name')->get();

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses,
            'summary' => $summary,
            'categories' => $categories,
            'teachers' => $teachers,
            'filters' => $request->only(['search', 'status', 'level', 'category_id', 'is_featured', 'sort_by', 'sort_dir']),
        ]);
    }

    public function create(): Response
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->where('status', 'active')->select('id', 'name', 'email')->orderBy('name')->get();

        return Inertia::render('Admin/Courses/Form', [
            'course' => null,
            'categories' => $categories,
            'teachers' => $teachers,
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:courses,slug',
            'category_id' => 'required|exists:categories,id',
            'teacher_id' => 'nullable|exists:users,id',
            'description' => 'required|string|max:1500',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration_hours' => 'required|integer|min:1',
            'status' => 'required|in:draft,published,started,archived',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_featured' => 'boolean',
            'min_students' => 'nullable|integer|min:1',
            'max_students' => 'nullable|integer|min:1',
            'auto_start_enabled' => 'boolean',

            // Benefits
            'has_certificate' => 'boolean',
            'has_lifetime_access' => 'boolean',
            'has_money_back' => 'boolean',
            'has_downloadable_resources' => 'boolean',
            'has_community_access' => 'boolean',
            'has_mobile_access' => 'boolean',

            // Badges
            'show_category_badge' => 'boolean',
            'show_level_badge' => 'boolean',
            'show_duration_badge' => 'boolean',
            'show_certificate_badge' => 'boolean',
            'show_students_badge' => 'boolean',

            // Schedule
            'primary_class_start' => 'nullable|string',
            'primary_class_end' => 'nullable|string',
            'primary_class_days' => 'nullable|array',
            'primary_class_note' => 'nullable|string|max:500',
            'secondary_class_start' => 'nullable|string',
            'secondary_class_end' => 'nullable|string',
            'secondary_class_days' => 'nullable|array',
            'secondary_class_note' => 'nullable|string|max:500',

            // Media
            'thumbnail' => 'nullable|image|max:3072',

            // Lessons
            'lessons' => 'nullable|array',
            'lessons.*.title' => 'required|string|max:255',
            'lessons.*.duration_minutes' => 'nullable|integer|min:1',
            'lessons.*.description' => 'nullable|string|max:500',
            'lessons.*.order' => 'nullable|integer',
        ]);

        if (in_array($validated['status'], ['published', 'started']) && empty($validated['teacher_id'])) {
            return back()->withErrors(['teacher_id' => 'برای انتشار یا شروع دوره، انتخاب مدرس الزامی است.']);
        }

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('courses/thumbnails', 'public');
            $validated['thumbnail'] = $path;
        }

        $lessonsData = $validated['lessons'] ?? [];
        unset($validated['lessons']);

        $course = Course::create($validated);

        if (!empty($lessonsData)) {
            foreach ($lessonsData as $idx => $l) {
                $course->lessons()->create([
                    'title' => $l['title'],
                    'duration_minutes' => $l['duration_minutes'] ?? 30,
                    'description' => $l['description'] ?? null,
                    'order' => $l['order'] ?? ($idx + 1),
                ]);
            }
        }

        return redirect()->route('admin.courses.index')->with('success', 'دوره با موفقیت ایجاد شد.');
    }

    public function edit(Course $course): Response
    {
        $course->load(['lessons' => fn($q) => $q->orderBy('order')]);

        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->where('status', 'active')->select('id', 'name', 'email')->orderBy('name')->get();

        $courseData = [
            'id' => $course->id,
            'title' => $course->title,
            'slug' => $course->slug,
            'category_id' => $course->category_id,
            'teacher_id' => $course->teacher_id,
            'description' => $course->description,
            'thumbnail' => $course->thumbnail ? (Str::startsWith($course->thumbnail, ['http://', 'https://']) ? $course->thumbnail : Storage::url($course->thumbnail)) : null,
            'level' => $course->level,
            'duration_hours' => $course->duration_hours,
            'status' => $course->status,
            'is_featured' => (bool)$course->is_featured,
            'start_date' => $course->start_date?->format('Y-m-d'),
            'end_date' => $course->end_date?->format('Y-m-d'),
            'min_students' => $course->min_students,
            'max_students' => $course->max_students,
            'auto_start_enabled' => (bool)$course->auto_start_enabled,

            'has_certificate' => (bool)$course->has_certificate,
            'has_lifetime_access' => (bool)$course->has_lifetime_access,
            'has_money_back' => (bool)$course->has_money_back,
            'has_downloadable_resources' => (bool)$course->has_downloadable_resources,
            'has_community_access' => (bool)$course->has_community_access,
            'has_mobile_access' => (bool)$course->has_mobile_access,

            'show_category_badge' => (bool)$course->show_category_badge,
            'show_level_badge' => (bool)$course->show_level_badge,
            'show_duration_badge' => (bool)$course->show_duration_badge,
            'show_certificate_badge' => (bool)$course->show_certificate_badge,
            'show_students_badge' => (bool)$course->show_students_badge,

            'primary_class_start' => $course->primary_class_start ? substr($course->primary_class_start, 0, 5) : '',
            'primary_class_end' => $course->primary_class_end ? substr($course->primary_class_end, 0, 5) : '',
            'primary_class_days' => $course->primary_class_days ?? [],
            'primary_class_note' => $course->primary_class_note ?? '',

            'secondary_class_start' => $course->secondary_class_start ? substr($course->secondary_class_start, 0, 5) : '',
            'secondary_class_end' => $course->secondary_class_end ? substr($course->secondary_class_end, 0, 5) : '',
            'secondary_class_days' => $course->secondary_class_days ?? [],
            'secondary_class_note' => $course->secondary_class_note ?? '',

            'lessons' => $course->lessons->map(fn($l) => [
                'id' => $l->id,
                'title' => $l->title,
                'duration_minutes' => $l->duration_minutes,
                'description' => $l->description,
                'order' => $l->order,
            ]),
            'enrolled_count' => $course->enrolled_count,
            'rating' => $course->rating,
            'total_reviews' => $course->total_reviews,
        ];

        return Inertia::render('Admin/Courses/Form', [
            'course' => $courseData,
            'categories' => $categories,
            'teachers' => $teachers,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:courses,slug,' . $course->id,
            'category_id' => 'required|exists:categories,id',
            'teacher_id' => 'nullable|exists:users,id',
            'description' => 'required|string|max:1500',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration_hours' => 'required|integer|min:1',
            'status' => 'required|in:draft,published,started,archived',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_featured' => 'boolean',
            'min_students' => 'nullable|integer|min:1',
            'max_students' => 'nullable|integer|min:1',
            'auto_start_enabled' => 'boolean',

            // Benefits
            'has_certificate' => 'boolean',
            'has_lifetime_access' => 'boolean',
            'has_money_back' => 'boolean',
            'has_downloadable_resources' => 'boolean',
            'has_community_access' => 'boolean',
            'has_mobile_access' => 'boolean',

            // Badges
            'show_category_badge' => 'boolean',
            'show_level_badge' => 'boolean',
            'show_duration_badge' => 'boolean',
            'show_certificate_badge' => 'boolean',
            'show_students_badge' => 'boolean',

            // Schedule
            'primary_class_start' => 'nullable|string',
            'primary_class_end' => 'nullable|string',
            'primary_class_days' => 'nullable|array',
            'primary_class_note' => 'nullable|string|max:500',
            'secondary_class_start' => 'nullable|string',
            'secondary_class_end' => 'nullable|string',
            'secondary_class_days' => 'nullable|array',
            'secondary_class_note' => 'nullable|string|max:500',

            // Media
            'thumbnail' => 'nullable',

            // Lessons
            'lessons' => 'nullable|array',
            'lessons.*.id' => 'nullable',
            'lessons.*.title' => 'required|string|max:255',
            'lessons.*.duration_minutes' => 'nullable|integer|min:1',
            'lessons.*.description' => 'nullable|string|max:500',
            'lessons.*.order' => 'nullable|integer',
        ]);

        if (in_array($validated['status'], ['published', 'started']) && empty($validated['teacher_id'])) {
            return back()->withErrors(['teacher_id' => 'برای انتشار یا شروع دوره، انتخاب مدرس الزامی است.']);
        }

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $path = $request->file('thumbnail')->store('courses/thumbnails', 'public');
            $validated['thumbnail'] = $path;
        } else {
            unset($validated['thumbnail']);
        }

        $lessonsData = $validated['lessons'] ?? [];
        unset($validated['lessons']);

        $course->update($validated);

        // Sync Lessons
        $existingLessonIds = $course->lessons()->pluck('id')->toArray();
        $updatedLessonIds = [];

        foreach ($lessonsData as $idx => $lData) {
            $lessonId = $lData['id'] ?? null;
            $payload = [
                'title' => $lData['title'],
                'duration_minutes' => $lData['duration_minutes'] ?? 30,
                'description' => $lData['description'] ?? null,
                'order' => $lData['order'] ?? ($idx + 1),
            ];

            if ($lessonId && in_array($lessonId, $existingLessonIds)) {
                $course->lessons()->where('id', $lessonId)->update($payload);
                $updatedLessonIds[] = $lessonId;
            } else {
                $newLesson = $course->lessons()->create($payload);
                $updatedLessonIds[] = $newLesson->id;
            }
        }

        // Delete removed lessons
        $toDelete = array_diff($existingLessonIds, $updatedLessonIds);
        if (!empty($toDelete)) {
            $course->lessons()->whereIn('id', $toDelete)->delete();
        }

        return redirect()->route('admin.courses.index')->with('success', 'تغییرات دوره با موفقیت ذخیره شد.');
    }

    public function destroy(Course $course)
    {
        if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->lessons()->delete();
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'دوره مورد نظر حذف گردید.');
    }

    public function toggleFeatured(Course $course)
    {
        $course->update([
            'is_featured' => !$course->is_featured,
        ]);

        return back()->with('success', 'وضعیت ویژه بودن دوره به‌روزرسانی شد.');
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'هیچ دوره‌ای انتخاب نشده است.');
        }

        $courses = Course::whereIn('id', $ids);

        switch ($action) {
            case 'publish':
                // Check if any course doesn't have a teacher
                $withoutTeacher = (clone $courses)->whereNull('teacher_id')->count();
                if ($withoutTeacher > 0) {
                    return back()->with('error', "تعداد {$withoutTeacher} دوره انتخاب شده بدون مدرس هستند و امکان انتشار ندارند.");
                }
                $courses->update(['status' => 'published']);
                $msg = 'دوره‌های انتخاب شده با موفقیت منتشر شدند.';
                break;

            case 'start':
                $courses->each(fn($c) => $c->startCourse());
                $msg = 'وضعیت دوره‌های انتخاب شده به در حال برگزاری تغییر یافت.';
                break;

            case 'archive':
                $courses->update(['status' => 'archived']);
                $msg = 'دوره‌های انتخاب شده آرشیو شدند.';
                break;

            case 'delete':
                foreach ($courses->get() as $c) {
                    if ($c->thumbnail && Storage::disk('public')->exists($c->thumbnail)) {
                        Storage::disk('public')->delete($c->thumbnail);
                    }
                    $c->lessons()->delete();
                    $c->delete();
                }
                $msg = 'دوره‌های انتخاب شده با موفقیت حذف شدند.';
                break;

            default:
                return back()->with('error', 'عملیات نامعتبر است.');
        }

        return back()->with('success', $msg);
    }

    public function importCurriculum(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        try {
            $file = $request->file('file');
            $lessons = app(CurriculumSpreadsheetImporter::class)->import($file->getRealPath());

            return response()->json([
                'success' => true,
                'lessons' => $lessons,
                'message' => count($lessons) . ' درس با موفقیت از فایل اکسل استخراج شد.',
            ]);
        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در پردازش فایل اکسل.',
            ], 500);
        }
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string|max:500',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'category' => $category,
            'message' => 'دسته‌بندی جدید با موفقیت اضافه شد.',
        ]);
    }
}
