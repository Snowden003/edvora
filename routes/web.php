<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\EnrollmentRequestController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\CourseChatController;
use App\Models\Course;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [HomePageController::class, 'index'])->name('home');



Route::match(['get', 'post'], '/broadcasting/auth', function (\Illuminate\Http\Request $request) {
    if (! $request->user()) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    try {
        require_once base_path('routes/channels.php');
        $result = \Illuminate\Support\Facades\Broadcast::driver('pusher')->auth($request);

        return response()->json($result)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('X-LiteSpeed-Cache-Control', 'no-cache');
    } catch (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e) {
        return response()->json(['message' => 'Unauthorized channel access.'], 403);
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('Broadcast auth error: ' . $e->getMessage(), [
            'exception' => $e->getMessage(),
            'channel' => $request->input('channel_name'),
            'user' => $request->user()?->id,
        ]);
        return response()->json(['message' => 'Broadcasting authorization failed: ' . $e->getMessage()], 500);
    }
})->middleware(['web', 'auth']);

Route::get('/debug-broadcast', function (\Illuminate\Http\Request $request) {
    $user = $request->user();
    $course = \App\Models\Course::first();

    $driver = get_class(\Illuminate\Support\Facades\Broadcast::driver());
    $configDefault = config('broadcasting.default');
    $pusherKey = config('broadcasting.connections.pusher.key');
    $cluster = config('broadcasting.connections.pusher.options.cluster');

    $authTest = null;
    $authError = null;
    if ($user && $course) {
        try {
            require_once base_path('routes/channels.php');
            $subReq = \Illuminate\Http\Request::create('/broadcasting/auth', 'POST', [
                'channel_name' => 'presence-course-chat.' . $course->id,
                'socket_id' => '1234.5678',
            ]);
            $subReq->setUserResolver(fn() => $user);
            $authTest = \Illuminate\Support\Facades\Broadcast::driver('pusher')->auth($subReq);
        } catch (\Throwable $e) {
            $authError = $e->getMessage();
        }
    }

    return response()->json([
        'user' => $user ? ['id' => $user->id, 'name' => $user->name, 'role' => $user->role] : 'Not logged in',
        'config_default' => $configDefault,
        'broadcaster_class' => $driver,
        'pusher_key' => $pusherKey,
        'pusher_cluster' => $cluster,
        'test_course_id' => $course?->id,
        'auth_test_result' => $authTest,
        'auth_test_error' => $authError,
    ]);
});

// Google OAuth
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Logout GET (redirect to home for convenience)
Route::get('/logout', function () {
    return redirect()->route('home');
})->name('logout.get');

// Contact Message
Route::post('/contact', [ContactMessageController::class, 'store'])->name('contact.store');

// AI Chatbot
Route::post('/ai-chat', \App\Http\Controllers\AiChatController::class)->name('ai.chat');

// Books (public, no login required)
Route::prefix('books')->name('books.')->group(function () {
    Route::get('/', [BookController::class, 'index'])->name('index');
    Route::get('/{slug}/download', [BookController::class, 'download'])->name('download');
    Route::get('/{slug}/view', [BookController::class, 'view'])->name('view');
    Route::get('/{slug}', [BookController::class, 'show'])->name('show');
});

// Courses
Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('/{slug}', [CourseController::class, 'show'])->name('detail');
});

// Teachers
Route::prefix('teachers')->name('teachers.')->group(function () {
    Route::get('/', [TeacherController::class, 'index'])->name('index');
    Route::get('/{id}', [TeacherController::class, 'show'])->name('show');
});

// Events
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/{slug}', [EventController::class, 'show'])->name('detail');
});
Route::post('/events/{id}/register', [EventController::class, 'register'])->name('events.register');

// Community & Resources
Route::get('/leaderboard',     [\App\Http\Controllers\LeaderboardController::class, 'index'])->name('leaderboard');
Route::get('/scoring-help',    [\App\Http\Controllers\ScoringHelpController::class, 'index'])->name('scoring.help');
Route::get('/roadmap',         [\App\Http\Controllers\RoadmapController::class, 'index'])->name('roadmap');
Route::get('/foundation',      [\App\Http\Controllers\FoundationController::class, 'index'])->name('foundation');

// About & Company
Route::get('/about',        fn() => view('about', ['content' => \App\Models\PageContent::get('page_about', [])]))->name('about');
Route::get('/story',        fn() => view('story', ['content' => \App\Models\PageContent::get('page_story', [])]))->name('story');
Route::get('/how-we-work',  [\App\Http\Controllers\HowWeWorkController::class, 'index'])->name('how-we-work');

// Referral link redirect
Route::get('/ref/{code}', [\App\Http\Controllers\ReferralController::class, 'join'])->name('referral.join');

// Contact
Route::get('/contact',      fn() => view('contact'))->name('contact');
Route::get('/faq',          fn() => view('faq'))->name('faq');
Route::get('/terms',        fn() => view('terms', ['content' => \App\Models\PageContent::get('page_terms', [])]))->name('terms');
Route::get('/privacy',      fn() => view('privacy', ['content' => \App\Models\PageContent::get('page_privacy', [])]))->name('privacy');

// Protected routes
Route::middleware(['auth'])->group(function () {

    // Course actions (any authenticated user)
    Route::get('/courses/{course}/chat', [CourseChatController::class, 'show'])->name('courses.chat.show');
    Route::get('/courses/{course}/chat/messages', [CourseChatController::class, 'index'])->name('courses.chat.index');
    Route::post('/courses/{course}/chat/messages', [CourseChatController::class, 'store'])->name('courses.chat.store');
    Route::put('/courses/{course}/chat/messages/{message}', [CourseChatController::class, 'update'])->name('courses.chat.update');
    Route::delete('/courses/{course}/chat/messages/{message}', [CourseChatController::class, 'destroy'])->name('courses.chat.destroy');
    Route::post('/courses/{course}/chat/messages/{message}/pin', [CourseChatController::class, 'togglePin'])->name('courses.chat.pin');
    Route::post('/courses/{course}/enroll',  [EnrollmentController::class, 'enroll'])->name('courses.enroll');
    Route::post('/courses/{course}/wishlist', [WishlistController::class, 'toggle'])->name('courses.wishlist.toggle');
    Route::post('/courses/{course}/enrollment-request', [EnrollmentRequestController::class, 'store'])->name('courses.enrollment-request.store');
    Route::delete('/enrollment-requests/{enrollmentRequest}/cancel', [EnrollmentRequestController::class, 'cancel'])->name('courses.enrollment-request.cancel');

    // Global notifications (accessible by student, teacher, admin on any page)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/live-check',        [NotificationController::class, 'liveCheck'])->name('live-check');
        Route::get('/unread-count',      [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all',         [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/delete-all',     [NotificationController::class, 'destroyAll'])->name('delete-all');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // Profile (both student & teacher)
    Route::middleware(['role:student,teacher'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::post('/profile', [ProfileController::class, 'saveProfile'])->name('profile.save');
    });

    // ─── STUDENT AREA ─── prefix: /student
    Route::prefix('student')->name('student.')->middleware(['role:student', 'verified'])->group(function () {
        Route::get('/dashboard',         [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/courses',           [StudentDashboardController::class, 'yourCourses'])->name('courses');
        Route::get('/courses/{slug}/learn', [StudentDashboardController::class, 'courseLearning'])->name('courses.learn');
        Route::get('/courses/{slug}/sessions/join',  [StudentDashboardController::class, 'joinClass'])->name('courses.sessions.join');
        Route::get('/courses/{slug}/sessions/leave', [StudentDashboardController::class, 'leaveClass'])->name('courses.sessions.leave');

        // API: Check if class session is still active
        Route::get('/courses/{slug}/check-session', [StudentDashboardController::class, 'checkSession'])->name('courses.check-session');
        // API: Get class notes (for AJAX polling)
        Route::get('/courses/{slug}/notes', [StudentDashboardController::class, 'getClassNotes'])->name('courses.notes.api');
        // API: Check for any active class across enrolled courses
        Route::get('/active-class/check', [StudentDashboardController::class, 'checkActiveClass'])->name('active-class.check');
        // Reviews
        Route::post('/courses/{slug}/reviews',              [StudentDashboardController::class, 'storeReview'])->name('courses.reviews.store');
        Route::post('/reviews/{review}/like',               [StudentDashboardController::class, 'toggleReviewLike'])->name('reviews.like');

        Route::get('/certificates',      [StudentDashboardController::class, 'certificates'])->name('certificates');
        Route::get('/reviews',           fn() => view('reviews'))->name('reviews');
        Route::get('/profile',           [ProfileController::class, 'show'])->name('profile');
        Route::post('/profile',          [ProfileController::class, 'saveProfile'])->name('profile.save');
        Route::get('/profile-details',   [\App\Http\Controllers\StudentProfileController::class, 'show'])->name('profile-details');
        Route::post('/profile-details',  [\App\Http\Controllers\StudentProfileController::class, 'store'])->name('profile-details.store');

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/',                           [NotificationController::class, 'index'])->name('index');
            Route::post('/{notification}/read',       [NotificationController::class, 'markAsRead'])->name('read');
            Route::post('/read-all',                  [NotificationController::class, 'markAllAsRead'])->name('read.all');
            Route::delete('/delete-all',              [NotificationController::class, 'destroyAll'])->name('delete.all');
            Route::delete('/{notification}',          [NotificationController::class, 'destroy'])->name('delete');
            Route::get('/unread-count',               [NotificationController::class, 'unreadCount'])->name('unread.count');
            Route::get('/check-new',               [NotificationController::class, 'checkNew'])->name('check.new');
        });

        Route::prefix('quizzes')->name('exams.')->group(function () {
            Route::get('/',              [QuizController::class, 'index'])->name('index');
            Route::get('/{quiz}',        [QuizController::class, 'show'])->name('show');
            Route::get('/{quiz}/take',   [QuizController::class, 'take'])->name('take');
            Route::post('/{quiz}/submit',[QuizController::class, 'submit'])->name('submit');
        });
    });

    // ─── TEACHER AREA ─── prefix: /teacher
    Route::prefix('teacher')->name('teacher.')->middleware(['role:teacher', 'teacher.onboarded'])->group(function () {
        Route::get('/dashboard',         [TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::get('/your-courses',      [TeacherDashboardController::class, 'yourCourses'])->name('your-courses');
        Route::get('/onboarding',        [TeacherDashboardController::class, 'onboarding'])->name('onboarding');
        Route::post('/onboarding',       [TeacherDashboardController::class, 'submitOnboarding'])->name('onboarding.submit');
        Route::get('/reviews',           fn() => view('reviews'))->name('reviews');

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/',                           [NotificationController::class, 'index'])->name('index');
            Route::post('/{notification}/read',       [NotificationController::class, 'markAsRead'])->name('read');
            Route::post('/read-all',                  [NotificationController::class, 'markAllAsRead'])->name('read.all');
            Route::delete('/delete-all',              [NotificationController::class, 'destroyAll'])->name('delete.all');
            Route::delete('/{notification}',          [NotificationController::class, 'destroy'])->name('delete');
            Route::get('/unread-count',               [NotificationController::class, 'unreadCount'])->name('unread.count');
            Route::get('/check-new',               [NotificationController::class, 'checkNew'])->name('check.new');
        });

        Route::prefix('quizzes')->name('exams.')->group(function () {
            Route::get('/',              [QuizController::class, 'index'])->name('index');
            Route::get('/create',        [QuizController::class, 'create'])->name('create');
            Route::post('/',             [QuizController::class, 'store'])->name('store');
            Route::get('/{quiz}',        [QuizController::class, 'show'])->name('show');
            Route::get('/{quiz}/edit',   [QuizController::class, 'edit'])->name('edit');
            Route::put('/{quiz}',        [QuizController::class, 'update'])->name('update');
            Route::delete('/{quiz}',     [QuizController::class, 'destroy'])->name('destroy');
            Route::get('/{quiz}/questions',  [QuizController::class, 'questions'])->name('questions');
            Route::post('/{quiz}/questions', [QuizController::class, 'storeQuestion'])->name('questions.store');
            Route::post('/{quiz}/publish',   [QuizController::class, 'publish'])->name('publish');
        });

        // Enrollment Requests & Students
        Route::get('/enrollment-requests',                    [EnrollmentRequestController::class, 'index'])->name('enrollment-requests');
        Route::get('/enrollment-requests/{enrollmentRequest}/student', [EnrollmentRequestController::class, 'studentProfile'])->name('enrollment-requests.student');
        Route::get('/students/{user}/profile',                 [EnrollmentRequestController::class, 'studentProfileUser'])->name('students.profile');
        Route::post('/students/{user}/points/adjust',          [EnrollmentRequestController::class, 'adjustPoints'])->name('students.points.adjust');
        Route::post('/enrollment-requests/{enrollmentRequest}/approve', [EnrollmentRequestController::class, 'approve'])->name('enrollment-requests.approve');
        Route::post('/enrollment-requests/{enrollmentRequest}/reject',  [EnrollmentRequestController::class, 'reject'])->name('enrollment-requests.reject');

        Route::get('/courses/{id}',                       [TeacherDashboardController::class, 'courseDetail'])->name('courses.detail');
        Route::post('/courses/{id}/toggle-enrollment',    [TeacherDashboardController::class, 'toggleEnrollment'])->name('courses.toggle-enrollment');
        Route::post('/courses/{id}/regenerate-referral',  [TeacherDashboardController::class, 'regenerateReferralCode'])->name('courses.referral.regenerate');
        Route::post('/courses/{id}/documents',            [TeacherDashboardController::class, 'uploadDocument'])->name('courses.documents.upload');
        Route::delete('/courses/{id}/documents/{docId}',  [TeacherDashboardController::class, 'deleteDocument'])->name('courses.documents.delete');
        Route::get('/courses/{id}/export-pdf',            [TeacherDashboardController::class, 'exportPdf'])->name('courses.export-pdf');
        Route::get('/courses/{id}/export-attendance-pdf', [TeacherDashboardController::class, 'exportAttendancePdf'])->name('courses.export-attendance-pdf');
        Route::post('/courses/{id}/sessions/start',       [TeacherDashboardController::class, 'startClass'])->name('courses.sessions.start');
        Route::post('/courses/{id}/sessions/end',         [TeacherDashboardController::class, 'endClass'])->name('courses.sessions.end');
        Route::post('/courses/{id}/sessions/activity',    [TeacherDashboardController::class, 'updateParticipantActivity'])->name('courses.sessions.activity');
        Route::get('/courses/{id}/sessions/{sessionId}/participants', [TeacherDashboardController::class, 'getSessionParticipants'])->name('courses.sessions.participants');
        Route::get('/courses/{id}/sessions/join',        [TeacherDashboardController::class, 'joinClass'])->name('courses.sessions.join');
        Route::post('/courses/auto-close-inactive',       [TeacherDashboardController::class, 'autoCloseInactiveSessions'])->name('courses.auto-close');
        Route::get('/courses/{id}/sessions/leave',        [TeacherDashboardController::class, 'leaveClass'])->name('courses.sessions.leave');
        Route::get('/courses/{id}/attendance',            [TeacherDashboardController::class, 'attendance'])->name('courses.attendance');
        Route::post('/courses/{id}/sessions/{sessionId}/attendance', [TeacherDashboardController::class, 'saveAttendance'])->name('courses.sessions.attendance');
        Route::post('/courses/{id}/notes',                [TeacherDashboardController::class, 'storeClassNote'])->name('courses.notes.store');
        Route::put('/courses/{id}/notes/{noteId}',        [TeacherDashboardController::class, 'updateClassNote'])->name('courses.notes.update');
        Route::delete('/courses/{id}/notes/{noteId}',     [TeacherDashboardController::class, 'deleteClassNote'])->name('courses.notes.delete');
        Route::post('/courses/{id}/lessons',               [TeacherDashboardController::class, 'storeLesson'])->name('courses.lessons.store');
        Route::put('/courses/{id}/lessons/{lessonId}',     [TeacherDashboardController::class, 'updateLesson'])->name('courses.lessons.update');
        Route::delete('/courses/{id}/lessons/{lessonId}',  [TeacherDashboardController::class, 'deleteLesson'])->name('courses.lessons.delete');

        Route::post('/courses/{id}/students/{userId}/ban',   [TeacherDashboardController::class, 'banStudent'])->name('courses.students.ban');
        Route::post('/courses/{id}/students/{userId}/unban', [TeacherDashboardController::class, 'unbanStudent'])->name('courses.students.unban');

        // Points / Gamification
        Route::get('/courses/{id}/points', [TeacherDashboardController::class, 'points'])->name('courses.points');
        Route::post('/courses/{id}/points', [TeacherDashboardController::class, 'storePoints'])->name('courses.points.store');
        Route::get('/courses/{id}/points/students/{userId}', [TeacherDashboardController::class, 'studentPointsHistory'])->name('courses.points.student');

    });

    // ─── ADMIN AREA ─── prefix: /admin
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index']);
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');

        // Courses Management (React SPA)
        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminCourseController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\AdminCourseController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\AdminCourseController::class, 'store'])->name('store');
            Route::post('/bulk-action', [\App\Http\Controllers\Admin\AdminCourseController::class, 'bulkAction'])->name('bulk-action');
            Route::post('/import-curriculum', [\App\Http\Controllers\Admin\AdminCourseController::class, 'importCurriculum'])->name('import-curriculum');
            Route::post('/categories', [\App\Http\Controllers\Admin\AdminCourseController::class, 'storeCategory'])->name('categories.store');
            Route::get('/{course}/edit', [\App\Http\Controllers\Admin\AdminCourseController::class, 'edit'])->name('edit');
            Route::get('/{course}/students', [\App\Http\Controllers\Admin\AdminCourseController::class, 'students'])->name('students');
            Route::patch('/{course}/students/{user}/status', [\App\Http\Controllers\Admin\AdminCourseController::class, 'updateStudentStatus'])->name('students.update-status');
            Route::delete('/{course}/students/{user}', [\App\Http\Controllers\Admin\AdminCourseController::class, 'removeStudent'])->name('students.remove');
            Route::post('/{course}/toggle-featured', [\App\Http\Controllers\Admin\AdminCourseController::class, 'toggleFeatured'])->name('toggle-featured');
            Route::post('/{course}', [\App\Http\Controllers\Admin\AdminCourseController::class, 'update'])->name('update');
            Route::delete('/{course}', [\App\Http\Controllers\Admin\AdminCourseController::class, 'destroy'])->name('destroy');
        });

        // Books & Articles Management (React SPA)
        Route::prefix('books')->name('books.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminBookController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\AdminBookController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\AdminBookController::class, 'store'])->name('store');
            Route::post('/bulk-action', [\App\Http\Controllers\Admin\AdminBookController::class, 'bulkAction'])->name('bulk-action');
            Route::post('/categories', [\App\Http\Controllers\Admin\AdminBookController::class, 'storeCategory'])->name('categories.store');
            Route::get('/{book}/edit', [\App\Http\Controllers\Admin\AdminBookController::class, 'edit'])->name('edit');
            Route::post('/{book}/toggle-publish', [\App\Http\Controllers\Admin\AdminBookController::class, 'togglePublish'])->name('toggle-publish');
            Route::post('/{book}', [\App\Http\Controllers\Admin\AdminBookController::class, 'update'])->name('update');
            Route::delete('/{book}', [\App\Http\Controllers\Admin\AdminBookController::class, 'destroy'])->name('destroy');
        });

        // Events Management (Custom Admin)
        Route::get('/events', [\App\Http\Controllers\Admin\EventAdminController::class, 'index'])->name('events.index');
        Route::get('/events/create', [\App\Http\Controllers\Admin\EventAdminController::class, 'create'])->name('events.create');
        Route::post('/events', [\App\Http\Controllers\Admin\EventAdminController::class, 'store'])->name('events.store');
        Route::get('/events/{event}/edit', [\App\Http\Controllers\Admin\EventAdminController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [\App\Http\Controllers\Admin\EventAdminController::class, 'update'])->name('events.update');
        Route::get('/events/{event}/registrations/pdf', [\App\Http\Controllers\Admin\EventAdminController::class, 'downloadRegistrationsPdf'])->name('events.registrations.pdf');

        // Broadcast Emails to All Students / Users (React SPA)
        Route::prefix('broadcast-emails')->name('broadcast-emails.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminBroadcastEmailController::class, 'index'])->name('index');
            Route::post('/send', [\App\Http\Controllers\Admin\AdminBroadcastEmailController::class, 'send'])->name('send');
            Route::post('/test', [\App\Http\Controllers\Admin\AdminBroadcastEmailController::class, 'sendTest'])->name('test');
        });

        // File Manager (React SPA)
        Route::prefix('file-manager')->name('file-manager.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminFileManagerController::class, 'index'])->name('index');
            Route::post('/upload', [\App\Http\Controllers\Admin\AdminFileManagerController::class, 'upload'])->name('upload');
            Route::post('/delete', [\App\Http\Controllers\Admin\AdminFileManagerController::class, 'destroy'])->name('destroy');
            Route::post('/create-folder', [\App\Http\Controllers\Admin\AdminFileManagerController::class, 'createFolder'])->name('create-folder');
            Route::post('/bulk-delete', [\App\Http\Controllers\Admin\AdminFileManagerController::class, 'bulkDestroy'])->name('bulk-delete');
        });

        // Students Management (React SPA)
        Route::prefix('students')->name('students.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminStudentController::class, 'index'])->name('index');
            Route::post('/{user}/status', [\App\Http\Controllers\Admin\AdminStudentController::class, 'updateStatus'])->name('update-status');
            Route::post('/{user}/enrollment/{enrollment}/status', [\App\Http\Controllers\Admin\AdminStudentController::class, 'updateEnrollmentStatus'])->name('update-enrollment-status');
            Route::delete('/{user}/enrollment/{enrollment}', [\App\Http\Controllers\Admin\AdminStudentController::class, 'removeEnrollment'])->name('remove-enrollment');
            Route::post('/{user}/message', [\App\Http\Controllers\Admin\AdminStudentController::class, 'sendMessage'])->name('send-message');
            Route::post('/bulk-action', [\App\Http\Controllers\Admin\AdminStudentController::class, 'bulkAction'])->name('bulk-action');
        });

        // Teachers Management (React SPA)
        Route::prefix('teachers')->name('teachers.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminTeacherController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\AdminTeacherController::class, 'store'])->name('store');
            Route::post('/{user}', [\App\Http\Controllers\Admin\AdminTeacherController::class, 'update'])->name('update');
            Route::post('/{user}/verify', [\App\Http\Controllers\Admin\AdminTeacherController::class, 'verify'])->name('verify');
            Route::post('/{user}/reject', [\App\Http\Controllers\Admin\AdminTeacherController::class, 'reject'])->name('reject');
            Route::post('/{user}/status', [\App\Http\Controllers\Admin\AdminTeacherController::class, 'updateStatus'])->name('update-status');
            Route::post('/{user}/message', [\App\Http\Controllers\Admin\AdminTeacherController::class, 'sendMessage'])->name('send-message');
            Route::delete('/{user}', [\App\Http\Controllers\Admin\AdminTeacherController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-action', [\App\Http\Controllers\Admin\AdminTeacherController::class, 'bulkAction'])->name('bulk-action');
        });

        // Public / Company Pages Management (React SPA + Gemini AI)
        Route::prefix('company-pages')->name('company-pages.')->group(function () {
            Route::get('/{pageKey?}', [\App\Http\Controllers\Admin\AdminCompanyPagesController::class, 'index'])->name('index');
            Route::post('/ai/generate', [\App\Http\Controllers\Admin\AdminCompanyPagesController::class, 'generateAi'])->name('ai.generate');
            Route::post('/{pageKey}', [\App\Http\Controllers\Admin\AdminCompanyPagesController::class, 'update'])->name('update');
        });
    });
});

// Legacy Admin-Panel Redirects
Route::redirect('/admin-panel/students', '/admin/students');
Route::redirect('/admin-panel/teachers', '/admin/teachers');
Route::redirect('/admin-panel', '/admin/dashboard');

// API Routes for events
Route::get('/api/events', [EventController::class, 'apiIndex'])->name('api.events.index');

// Breeze Auth routes
require __DIR__.'/auth.php';
