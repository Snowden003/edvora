<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AdminStudentController extends Controller
{
    /**
     * Display a listing of students with categorization, search, and statistics.
     */
    public function index(Request $request): Response
    {
        $search = trim((string) $request->input('search', ''));
        $categoryId = $request->input('category_id');
        $courseId = $request->input('course_id');
        $status = $request->input('status'); // active, banned, suspended, all
        $enrollmentStatus = $request->input('enrollment_status'); // active, banned, completed
        $sortBy = (string) $request->input('sort_by', 'created_at');
        $sortDir = (string) $request->input('sort_dir', 'desc');

        $query = User::where('role', 'student')
            ->with([
                'studentProfile',
                'enrollments.course.category',
                'enrollments.course.teacher',
            ]);

        // Search Filter (Search in User and StudentProfile)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('studentProfile', function ($sp) use ($search) {
                      $sp->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('father_name', 'like', "%{$search}%")
                         ->orWhere('national_id', 'like', "%{$search}%")
                         ->orWhere('phone_number', 'like', "%{$search}%")
                         ->orWhere('whatsapp_number', 'like', "%{$search}%")
                         ->orWhere('field_of_study', 'like', "%{$search}%")
                         ->orWhere('province', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Course Category (ما چیزی که می‌خوانند را فیلتر می‌کنیم)
        if (!empty($categoryId) && $categoryId !== 'all') {
            if ($categoryId === 'no_course') {
                $query->doesntHave('enrollments');
            } else {
                $query->whereHas('enrollments.course', function ($c) use ($categoryId) {
                    $c->where('category_id', $categoryId);
                });
            }
        }

        // Filter by Specific Course
        if (!empty($courseId) && $courseId !== 'all') {
            $query->whereHas('enrollments', function ($e) use ($courseId) {
                $e->where('course_id', $courseId);
            });
        }

        // Filter by Student Account Status
        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by Enrollment Status in Class (مثلا شاگردانی که از کلاس بن/تعلیق شده‌اند)
        if (!empty($enrollmentStatus) && $enrollmentStatus !== 'all') {
            $query->whereHas('enrollments', function ($e) use ($enrollmentStatus) {
                $e->where('status', $enrollmentStatus);
            });
        }

        // Sorting
        $allowedSorts = ['name', 'email', 'created_at', 'xp', 'status'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        // Statistics across all students
        $totalStudents = User::where('role', 'student')->count();
        $activeStudents = User::where('role', 'student')->where('status', 'active')->count();
        $bannedStudents = User::where('role', 'student')->whereIn('status', ['banned', 'suspended'])->count();
        $totalEnrollmentsCount = Enrollment::count();

        // Categorization breakdown (count students per category)
        $allCategories = Category::withCount(['courses as enrolled_students_count' => function ($q) {
            $q->join('enrollments', 'courses.id', '=', 'enrollments.course_id');
        }])->get();

        $allCourses = Course::select('id', 'title', 'category_id')
            ->withCount('enrollments')
            ->orderBy('title')
            ->get();

        // Pagination and Transformation
        $students = $query->paginate(12)->withQueryString()->through(function ($student) {
            $profile = $student->studentProfile;

            // Map enrollments with class & teacher details
            $enrollments = $student->enrollments->map(function ($enr) {
                $course = $enr->course;
                $teacher = $course?->teacher;
                return [
                    'id' => $enr->id,
                    'course_id' => $enr->course_id,
                    'course_title' => $course?->title ?? 'دوره حذف‌شده',
                    'course_slug' => $course?->slug,
                    'course_thumbnail' => $course?->thumbnail ? (str_starts_with($course->thumbnail, 'http') ? $course->thumbnail : Storage::disk('public')->url($course->thumbnail)) : null,
                    'category_id' => $course?->category_id,
                    'category_name' => $course?->category?->name ?? 'بدون دسته‌بندی',
                    'teacher_name' => $teacher?->name ?? 'نامشخص',
                    'status' => $enr->status ?? 'active', // active, suspended, banned, completed
                    'progress_percentage' => (int) ($enr->progress_percentage ?? 0),
                    'enrolled_at' => $enr->created_at ? $enr->created_at->diffForHumans() : '-',
                    'enrolled_date' => $enr->created_at ? $enr->created_at->format('Y-m-d') : null,
                    'completed_at' => $enr->completed_at ? $enr->completed_at->format('Y-m-d') : null,
                    'schedule' => [
                        'start' => $course?->primary_class_start,
                        'end' => $course?->primary_class_end,
                        'days' => $course?->primary_class_days ?? [],
                    ],
                ];
            });

            // Avatar resolution
            $avatarUrl = null;
            if ($student->avatar) {
                $avatarUrl = str_starts_with($student->avatar, 'http') ? $student->avatar : Storage::disk('public')->url($student->avatar);
            } elseif ($profile?->profile_photo) {
                $avatarUrl = str_starts_with($profile->profile_photo, 'http') ? $profile->profile_photo : Storage::disk('public')->url($profile->profile_photo);
            }

            return [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'avatar' => $avatarUrl,
                'status' => $student->status ?? 'active',
                'xp' => (int) ($student->xp ?? 0),
                'created_at' => $student->created_at ? $student->created_at->diffForHumans() : '-',
                'registered_date' => $student->created_at ? $student->created_at->format('Y-m-d') : '-',
                'enrollments_count' => $enrollments->count(),
                'active_classes_count' => $enrollments->where('status', 'active')->count(),
                'banned_classes_count' => $enrollments->whereIn('status', ['banned', 'suspended'])->count(),
                'enrollments' => $enrollments,

                // Comprehensive profile details
                'profile' => [
                    'first_name' => $profile?->first_name,
                    'last_name' => $profile?->last_name,
                    'father_name' => $profile?->father_name ?? '-',
                    'mother_name' => $profile?->mother_name ?? '-',
                    'gender' => $profile?->gender ?? 'نامشخص',
                    'date_of_birth' => $profile?->date_of_birth ? $profile->date_of_birth->format('Y-m-d') : '-',
                    'national_id' => $profile?->national_id ?? '-',
                    'phone_number' => $profile?->phone_number ?? $student->phone ?? '-',
                    'whatsapp_number' => $profile?->whatsapp_number ?? '-',
                    'passport_number' => $profile?->passport_number ?? '-',
                    'marital_status' => $profile?->marital_status ?? '-',
                    'blood_type' => $profile?->blood_type ?? '-',
                    // Address
                    'province' => $profile?->province ?? '-',
                    'district' => $profile?->district ?? '-',
                    'current_address' => $profile?->current_address ?? '-',
                    'permanent_address' => $profile?->permanent_address ?? '-',
                    'postal_code' => $profile?->postal_code ?? '-',
                    // Education
                    'last_education_level' => $profile?->last_education_level ?? '-',
                    'last_school_name' => $profile?->last_school_name ?? '-',
                    'graduation_year' => $profile?->graduation_year ?? '-',
                    'field_of_study' => $profile?->field_of_study ?? 'نامشخص',
                    'gpa' => $profile?->gpa ?? '-',
                    'university_name' => $profile?->university_name ?? '-',
                    'other_certifications' => $profile?->other_certifications ?? '-',
                    // Emergency
                    'emergency_contact_name' => $profile?->emergency_contact_name ?? '-',
                    'emergency_contact_phone' => $profile?->emergency_contact_phone ?? '-',
                    'emergency_contact_relation' => $profile?->emergency_contact_relation ?? '-',
                    // Extra
                    'skills' => $profile?->skills ?? '-',
                    'languages' => $profile?->languages ?? '-',
                    'about_me' => $profile?->about_me ?? '-',
                    'is_complete' => (bool) ($profile?->is_complete ?? false),
                ],
            ];
        });

        return Inertia::render('Admin/Students/Index', [
            'students' => $students,
            'categories' => $allCategories,
            'courses' => $allCourses,
            'stats' => [
                'total_students' => $totalStudents,
                'active_students' => $activeStudents,
                'banned_students' => $bannedStudents,
                'total_enrollments' => $totalEnrollmentsCount,
            ],
            'filters' => [
                'search' => $search,
                'category_id' => $categoryId,
                'course_id' => $courseId,
                'status' => $status,
                'enrollment_status' => $enrollmentStatus,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
            ],
        ]);
    }

    /**
     * Update a student's overall account status (e.g. active, banned, suspended).
     */
    public function updateStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,banned,suspended',
            'reason' => 'nullable|string|max:500',
        ]);

        $newStatus = $validated['status'];
        $user->update(['status' => $newStatus]);

        // Send an informative notification to the student
        $title = match ($newStatus) {
            'banned' => 'مسدودسازی حساب کاربری',
            'suspended' => 'تعلیق موقت حساب کاربری',
            default => 'فعال‌سازی مجدد حساب کاربری',
        };

        $message = match ($newStatus) {
            'banned' => 'حساب کاربری شما توسط مدیر سیستم مسدود گردید.' . (!empty($validated['reason']) ? " علت: {$validated['reason']}" : ''),
            'suspended' => 'حساب کاربری شما به صورت موقت تعلیق شد.' . (!empty($validated['reason']) ? " علت: {$validated['reason']}" : ''),
            default => 'حساب کاربری شما مجدداً فعال شد و هم‌اکنون به کلیه امکانات دسترسی دارید.',
        };

        Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_ADMIN_MESSAGE,
            'title' => $title,
            'message' => $message,
            'data' => [
                'action' => 'status_change',
                'new_status' => $newStatus,
                'reason' => $validated['reason'] ?? null,
                'admin_name' => auth()->user()?->name ?? 'مدیر سیستم',
            ],
            'is_read' => false,
        ]);

        $statusFa = match ($newStatus) {
            'banned' => 'مسدود (بن)',
            'suspended' => 'تعلیق موقت',
            default => 'فعال',
        };

        return back()->with('success', "وضعیت حساب دانشجو «{$user->name}» با موفقیت به «{$statusFa}» تغییر یافت.");
    }

    /**
     * Ban / Suspend / Activate a student in a specific class or course.
     */
    public function updateEnrollmentStatus(Request $request, User $user, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,suspended,banned,completed,dropped',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($enrollment->user_id !== $user->id) {
            abort(404);
        }

        $newStatus = $validated['status'];
        $enrollment->update(['status' => $newStatus]);

        $courseTitle = $enrollment->course?->title ?? 'دوره آموزشی';

        $title = match ($newStatus) {
            'banned' => "اخراج و مسدودیت از کلاس {$courseTitle}",
            'suspended' => "تعلیق موقت از کلاس {$courseTitle}",
            'completed' => "تکمیل دوره {$courseTitle}",
            default => "فعال‌سازی حضور در کلاس {$courseTitle}",
        };

        $message = match ($newStatus) {
            'banned' => "شما از شرکت در کلاس «{$courseTitle}» محروم شدید." . (!empty($validated['reason']) ? " دلیل: {$validated['reason']}" : ''),
            'suspended' => "دسترسی شما به جلسات کلاس «{$courseTitle}» موقتاً به حالت تعلیق درآمد." . (!empty($validated['reason']) ? " دلیل: {$validated['reason']}" : ''),
            'completed' => "دوره «{$courseTitle}» برای شما با موفقیت به پایان رسید.",
            default => "دسترسی شما به جلسات و محتوای کلاس «{$courseTitle}» مجدداً برقرار گردید.",
        };

        Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_ADMIN_MESSAGE,
            'title' => $title,
            'message' => $message,
            'data' => [
                'course_id' => $enrollment->course_id,
                'course_title' => $courseTitle,
                'status' => $newStatus,
                'reason' => $validated['reason'] ?? null,
                'admin_name' => auth()->user()?->name ?? 'مدیریت آموزش',
            ],
            'is_read' => false,
        ]);

        $statusFa = match ($newStatus) {
            'banned' => 'اخراج / مسدود از کلاس',
            'suspended' => 'تعلیق موقت از کلاس',
            'completed' => 'تکمیل‌شده',
            'dropped' => 'انصراف',
            default => 'فعال در کلاس',
        };

        return back()->with('success', "وضعیت دانشجو در کلاس «{$courseTitle}» به «{$statusFa}» تغییر یافت.");
    }

    /**
     * Remove student completely from a course.
     */
    public function removeEnrollment(Request $request, User $user, Enrollment $enrollment)
    {
        if ($enrollment->user_id !== $user->id) {
            abort(404);
        }

        $course = $enrollment->course;
        $courseTitle = $course?->title ?? 'دوره';

        $enrollment->delete();

        if ($course && $course->enrolled_count > 0) {
            $course->decrement('enrolled_count');
        }

        Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_ADMIN_MESSAGE,
            'title' => "حذف از دوره {$courseTitle}",
            'message' => "شما توسط مدیر سیستم از لیست ثبت‌نام‌شدگان دوره «{$courseTitle}» خارج شدید.",
            'is_read' => false,
        ]);

        return back()->with('success', "دانشجو با موفقیت از دوره «{$courseTitle}» حذف شد.");
    }

    /**
     * Send direct message or notification to a student.
     */
    public function sendMessage(Request $request, User $user)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:3000',
            'priority' => 'nullable|in:normal,important,warning',
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_ADMIN_MESSAGE,
            'title' => $validated['title'],
            'message' => $validated['message'],
            'data' => [
                'priority' => $validated['priority'] ?? 'normal',
                'admin_name' => auth()->user()?->name ?? 'مدیر ارشد ادورا',
                'sent_at' => now()->toDateTimeString(),
            ],
            'is_read' => false,
        ]);

        return back()->with('success', "پیام با موفقیت برای «{$user->name}» ارسال شد.");
    }

    /**
     * Bulk actions for students (e.g. bulk status update or bulk messaging).
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:users,id',
            'action' => 'required|in:activate,ban,suspend,message',
            'reason' => 'nullable|string|max:500',
            'title' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:3000',
        ]);

        $ids = $validated['ids'];
        $action = $validated['action'];
        $count = count($ids);

        if ($action === 'activate') {
            User::whereIn('id', $ids)->where('role', 'student')->update(['status' => 'active']);
            return back()->with('success', "تعداد {$count} دانشجو با موفقیت فعال شدند.");
        }

        if ($action === 'ban') {
            User::whereIn('id', $ids)->where('role', 'student')->update(['status' => 'banned']);
            return back()->with('success', "تعداد {$count} دانشجو با موفقیت مسدود شدند.");
        }

        if ($action === 'suspend') {
            User::whereIn('id', $ids)->where('role', 'student')->update(['status' => 'suspended']);
            return back()->with('success', "تعداد {$count} دانشجو با موفقیت تعلیق شدند.");
        }

        if ($action === 'message') {
            if (empty($validated['title']) || empty($validated['message'])) {
                return back()->with('error', 'عنوان و متن پیام الزامی است.');
            }

            foreach ($ids as $userId) {
                Notification::create([
                    'user_id' => $userId,
                    'type' => Notification::TYPE_ADMIN_MESSAGE,
                    'title' => $validated['title'],
                    'message' => $validated['message'],
                    'data' => [
                        'admin_name' => auth()->user()?->name ?? 'مدیریت ادورا',
                        'sent_at' => now()->toDateTimeString(),
                    ],
                    'is_read' => false,
                ]);
            }

            return back()->with('success', "پیام همگانی با موفقیت برای {$count} دانشجو ارسال شد.");
        }

        return back();
    }
}
