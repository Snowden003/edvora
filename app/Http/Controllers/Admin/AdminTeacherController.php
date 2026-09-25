<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Notification;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminTeacherController extends Controller
{
    /**
     * Display a listing of teachers with advanced filtering, verification status, and statistics.
     */
    public function index(Request $request): Response
    {
        $search = trim((string) $request->input('search', ''));
        $department = $request->input('department', 'all');
        $verification = $request->input('verification', 'all'); // all, verified, pending, rejected
        $status = $request->input('status', 'all'); // all, active, suspended, banned
        $sortBy = (string) $request->input('sort_by', 'created_at');
        $sortDir = (string) $request->input('sort_dir', 'desc');

        $query = User::where('role', 'teacher')
            ->with([
                'teacher',
                'courses' => function ($q) {
                    $q->select('id', 'teacher_id', 'title', 'slug', 'thumbnail', 'category_id', 'status', 'enrolled_count', 'rating')
                      ->with('category:id,name');
                },
            ]);

        // Search Filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhereHas('teacher', function ($t) use ($search) {
                      $t->where('specialization', 'like', "%{$search}%")
                        ->orWhere('expertise', 'like', "%{$search}%");
                  });
            });
        }

        // Department Filter
        if (!empty($department) && $department !== 'all') {
            $query->where('department', $department);
        }

        // Verification Status Filter
        if ($verification === 'verified') {
            $query->whereHas('teacher', fn($t) => $t->where('is_verified', true));
        } elseif ($verification === 'pending') {
            $query->where(function ($q) {
                $q->where('status', 'pending')
                  ->orWhereHas('teacher', fn($t) => $t->where('is_verified', false));
            });
        } elseif ($verification === 'rejected') {
            $query->where('status', 'rejected');
        }

        // Account Status Filter
        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        // Sorting
        $allowedSorts = ['name', 'email', 'created_at', 'status'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        // Compute Statistics
        $totalTeachers = User::where('role', 'teacher')->count();
        $verifiedTeachers = Teacher::where('is_verified', true)->count();
        $pendingTeachers = Teacher::where('is_verified', false)->count();
        $rejectedTeachers = User::where('role', 'teacher')->where('status', 'rejected')->count();
        $totalTaughtCourses = Course::whereNotNull('teacher_id')->count();
        $totalEnrolledStudents = Course::whereNotNull('teacher_id')->sum('enrolled_count');

        // Extract unique departments for filter dropdown
        $departments = User::where('role', 'teacher')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->pluck('department');

        // Map and Paginate Teachers
        $teachers = $query->paginate(12)->withQueryString()->through(function ($user) {
            $teacher = $user->teacher;

            // Resolve CV URL
            $cvUrl = null;
            if ($user->cv_path) {
                $cvUrl = str_starts_with($user->cv_path, 'http')
                    ? $user->cv_path
                    : Storage::disk('public')->url($user->cv_path);
            }

            // Resolve Avatar URL
            $avatarUrl = null;
            if ($user->avatar) {
                $avatarUrl = str_starts_with($user->avatar, 'http')
                    ? $user->avatar
                    : Storage::disk('public')->url($user->avatar);
            }

            // Map Courses
            $courses = $user->courses->map(function ($c) {
                return [
                    'id' => $c->id,
                    'title' => $c->title,
                    'slug' => $c->slug,
                    'thumbnail' => $c->thumbnail ? (str_starts_with($c->thumbnail, 'http') ? $c->thumbnail : Storage::disk('public')->url($c->thumbnail)) : null,
                    'category' => $c->category?->name ?? 'عمومی',
                    'status' => $c->status,
                    'enrolled_count' => (int) ($c->enrolled_count ?? 0),
                    'rating' => (float) ($c->rating ?? 0),
                ];
            });

            // Expertise Array
            $expertiseList = [];
            if ($teacher?->expertise) {
                $expertiseList = array_map('trim', explode(',', $teacher->expertise));
            }

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '-',
                'avatar' => $avatarUrl,
                'department' => $user->department ?? 'نامشخص',
                'bio' => $user->bio,
                'status' => $user->status ?? 'active',
                'created_at' => $user->created_at ? $user->created_at->diffForHumans() : '-',
                'registered_date' => $user->created_at ? $user->created_at->format('Y-m-d') : '-',
                'cv_url' => $cvUrl,
                'cv_file_name' => $user->cv_path ? basename($user->cv_path) : null,

                // Teacher model fields
                'teacher_id' => $teacher?->id,
                'specialization' => $teacher?->specialization ?? $user->department ?? 'مدرس آموزشی',
                'expertise' => $expertiseList,
                'expertise_raw' => $teacher?->expertise ?? '',
                'years_of_experience' => (int) ($teacher?->years_of_experience ?? ($user->experience_years ? (int) $user->experience_years : 0)),
                'is_verified' => (bool) ($teacher?->is_verified ?? false),
                'rating' => (float) ($teacher?->rating ?? 0),
                'total_students' => (int) ($teacher?->total_students ?? $courses->sum('enrolled_count')),
                'total_courses' => (int) ($teacher?->total_courses ?? $courses->count()),
                'linkedin' => $teacher?->linkedin,
                'github' => $teacher?->github,
                'website' => $teacher?->website,

                // Enrolled courses list
                'courses' => $courses,
            ];
        });

        return Inertia::render('Admin/Teachers/Index', [
            'teachers' => $teachers,
            'departments' => $departments,
            'stats' => [
                'total_teachers' => $totalTeachers,
                'verified_teachers' => $verifiedTeachers,
                'pending_teachers' => $pendingTeachers,
                'rejected_teachers' => $rejectedTeachers,
                'total_courses' => $totalTaughtCourses,
                'total_students' => $totalEnrolledStudents,
            ],
            'filters' => [
                'search' => $search,
                'department' => $department,
                'verification' => $verification,
                'status' => $status,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
            ],
        ]);
    }

    /**
     * Store a newly created teacher account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'specialization' => 'required|string|max:255',
            'expertise' => 'nullable|string|max:500',
            'years_of_experience' => 'nullable|integer|min:0|max:50',
            'bio' => 'nullable|string|max:2000',
            'linkedin' => 'nullable|url|max:255',
            'github' => 'nullable|url|max:255',
            'website' => 'nullable|url|max:255',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'avatar_file' => 'nullable|image|max:4096',
            'is_verified' => 'boolean',
        ]);

        $cvPath = null;
        if ($request->hasFile('cv_file')) {
            $cvPath = $request->file('cv_file')->store('cvs', 'public');
        }

        $avatarPath = null;
        if ($request->hasFile('avatar_file')) {
            $avatarPath = $request->file('avatar_file')->store('avatars', 'public');
        }

        $isVerified = (bool) ($validated['is_verified'] ?? true);
        $userStatus = $isVerified ? 'active' : 'pending';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'teacher',
            'status' => $userStatus,
            'phone' => $validated['phone'] ?? null,
            'department' => $validated['department'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'avatar' => $avatarPath,
            'cv_path' => $cvPath,
            'experience_years' => (string) ($validated['years_of_experience'] ?? 0),
        ]);

        Teacher::create([
            'user_id' => $user->id,
            'specialization' => $validated['specialization'],
            'expertise' => $validated['expertise'] ?? null,
            'years_of_experience' => $validated['years_of_experience'] ?? 0,
            'linkedin' => $validated['linkedin'] ?? null,
            'github' => $validated['github'] ?? null,
            'website' => $validated['website'] ?? null,
            'is_verified' => $isVerified,
            'rating' => 0.0,
            'total_students' => 0,
            'total_courses' => 0,
        ]);

        Cache::forget('home:page-data:v2');
        Cache::forget('home:page-data:v3');

        return back()->with('success', "استاد جدید «{$user->name}» با موفقیت در سامانه ثبت گردید.");
    }

    /**
     * Update an existing teacher's profile and qualifications.
     */
    public function update(Request $request, User $user)
    {
        if ($user->role !== 'teacher') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|max:255|unique:users,email,{$user->id}",
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'specialization' => 'required|string|max:255',
            'expertise' => 'nullable|string|max:500',
            'years_of_experience' => 'nullable|integer|min:0|max:50',
            'bio' => 'nullable|string|max:2000',
            'linkedin' => 'nullable|url|max:255',
            'github' => 'nullable|url|max:255',
            'website' => 'nullable|url|max:255',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'avatar_file' => 'nullable|image|max:4096',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'department' => $validated['department'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'experience_years' => (string) ($validated['years_of_experience'] ?? 0),
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('cv_file')) {
            $userData['cv_path'] = $request->file('cv_file')->store('cvs', 'public');
        }

        if ($request->hasFile('avatar_file')) {
            $userData['avatar'] = $request->file('avatar_file')->store('avatars', 'public');
        }

        $user->update($userData);

        $teacher = $user->teacher ?: new Teacher(['user_id' => $user->id]);
        $teacher->fill([
            'specialization' => $validated['specialization'],
            'expertise' => $validated['expertise'] ?? null,
            'years_of_experience' => $validated['years_of_experience'] ?? 0,
            'linkedin' => $validated['linkedin'] ?? null,
            'github' => $validated['github'] ?? null,
            'website' => $validated['website'] ?? null,
        ]);
        $teacher->save();

        Cache::forget('home:page-data:v2');
        Cache::forget('home:page-data:v3');

        return back()->with('success', "اطلاعات استاد «{$user->name}» با موفقیت به‌روزرسانی شد.");
    }

    /**
     * Verify and approve a teacher's credentials.
     */
    public function verify(Request $request, User $user)
    {
        $teacher = $user->teacher ?: Teacher::create(['user_id' => $user->id, 'specialization' => $user->department ?? 'General']);
        $teacher->update(['is_verified' => true]);
        $user->update(['status' => 'active']);

        Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_ADMIN_MESSAGE,
            'title' => 'تبریک! مدارک تدریس شما تایید شد',
            'message' => 'رزومه و مدارک شما توسط مدیریت ارشد ادورا بررسی و با موفقیت تایید شد. هم‌اکنون به پنل اساتید و امکان ایجاد دوره‌ها دسترسی کامل دارید.',
            'is_read' => false,
        ]);

        Cache::forget('home:page-data:v2');
        Cache::forget('home:page-data:v3');

        return back()->with('success', "استاد «{$user->name}» با موفقیت تایید صلاحیت و فعال گردید.");
    }

    /**
     * Reject a teacher's application with a reason.
     */
    public function reject(Request $request, User $user)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        if ($user->teacher) {
            $user->teacher->update(['is_verified' => false]);
        }
        $user->update(['status' => 'rejected']);

        $reasonText = !empty($validated['reason']) ? " علت: {$validated['reason']}" : '';

        Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_ADMIN_MESSAGE,
            'title' => 'نتیجه بررسی مدارک تدریس',
            'message' => "درخواست تدریس شما در ادورا مورد تایید قرار نگرفت.{$reasonText}",
            'is_read' => false,
        ]);

        Cache::forget('home:page-data:v2');
        Cache::forget('home:page-data:v3');

        return back()->with('success', "درخواست تدریس «{$user->name}» رد شد و به وی اطلاع داده شد.");
    }

    /**
     * Update teacher account status (active, suspended, banned).
     */
    public function updateStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,suspended,banned',
            'reason' => 'nullable|string|max:500',
        ]);

        $newStatus = $validated['status'];
        $user->update(['status' => $newStatus]);

        $title = match ($newStatus) {
            'banned' => 'مسدودسازی حساب کاربری مدرس',
            'suspended' => 'تعلیق موقت حساب کاربری مدرس',
            default => 'فعال‌سازی مجدد حساب مدرسی',
        };

        $message = match ($newStatus) {
            'banned' => 'حساب مدرسی شما مسدود گردید.' . (!empty($validated['reason']) ? " علت: {$validated['reason']}" : ''),
            'suspended' => 'حساب مدرسی شما موقتاً به حالت تعلیق درآمد.' . (!empty($validated['reason']) ? " علت: {$validated['reason']}" : ''),
            default => 'حساب مدرسی شما مجدداً فعال گردید.',
        };

        Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_ADMIN_MESSAGE,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);

        return back()->with('success', "وضعیت حساب استاد «{$user->name}» تغییر یافت.");
    }

    /**
     * Send direct message or notification to a teacher.
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
                'admin_name' => auth()->user()?->name ?? 'مدیریت ارشد ادورا',
                'sent_at' => now()->toDateTimeString(),
            ],
            'is_read' => false,
        ]);

        return back()->with('success', "پیام با موفقیت برای استاد «{$user->name}» ارسال شد.");
    }

    /**
     * Delete a teacher account and profile.
     */
    public function destroy(User $user)
    {
        if ($user->role !== 'teacher') {
            abort(404);
        }

        $name = $user->name;
        if ($user->teacher) {
            $user->teacher->delete();
        }
        $user->delete();

        Cache::forget('home:page-data:v2');
        Cache::forget('home:page-data:v3');

        return back()->with('success', "حساب کاربری استاد «{$name}» با موفقیت حذف گردید.");
    }

    /**
     * Bulk actions for multiple teachers.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:users,id',
            'action' => 'required|in:verify,activate,suspend,ban,message',
            'title' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:3000',
        ]);

        $ids = $validated['ids'];
        $action = $validated['action'];
        $count = count($ids);

        if ($action === 'verify') {
            Teacher::whereIn('user_id', $ids)->update(['is_verified' => true]);
            User::whereIn('id', $ids)->where('role', 'teacher')->update(['status' => 'active']);
            return back()->with('success', "تعداد {$count} استاد با موفقیت تایید و فعال شدند.");
        }

        if ($action === 'activate') {
            User::whereIn('id', $ids)->where('role', 'teacher')->update(['status' => 'active']);
            return back()->with('success', "تعداد {$count} استاد فعال شدند.");
        }

        if ($action === 'suspend') {
            User::whereIn('id', $ids)->where('role', 'teacher')->update(['status' => 'suspended']);
            return back()->with('success', "تعداد {$count} استاد تعلیق شدند.");
        }

        if ($action === 'ban') {
            User::whereIn('id', $ids)->where('role', 'teacher')->update(['status' => 'banned']);
            return back()->with('success', "تعداد {$count} استاد مسدود شدند.");
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
                    'is_read' => false,
                ]);
            }

            return back()->with('success', "پیام همگانی برای {$count} استاد با موفقیت ارسال شد.");
        }

        return back();
    }
}
