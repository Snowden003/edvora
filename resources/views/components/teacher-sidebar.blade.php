<!-- Mobile Sidebar Toggle Button -->
<button class="sidebar-mobile-toggle d-flex d-lg-none" id="sidebarMobileToggle" aria-label="Open sidebar" type="button">
    <i class="bi bi-list"></i>
</button>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="edvora-sidebar" id="sidebar">
    <div class="sidebar-collapse-toggle d-none d-lg-flex" id="sidebarCollapse" title="Toggle Sidebar">
        <i class="bi bi-chevron-left"></i>
    </div>

    <!-- Brand Header -->
    <a href="{{ route('home') }}" class="edvora-brand">
        <div class="edvora-brand-logo-wrap">
            <img src="{{ asset('assets/images/logo1.jpg') }}" alt="Edvora Tech">
        </div>
        <div class="edvora-brand-info">
            <span class="edvora-brand-title">Edvora Tech</span>
            <span class="edvora-brand-badge"><i class="bi bi-shield-check me-1"></i>Instructor</span>
        </div>
    </a>

    <!-- Nav Items -->
    <ul class="edvora-nav">
        <li class="edvora-nav-section-title">MAIN MENU</li>

        <li class="edvora-nav-item">
            <a href="{{ route('home') }}"
               class="edvora-nav-link {{ request()->routeIs('home') ? 'active' : '' }}" title="Home">
                <span class="edvora-nav-icon"><i class="bi bi-house-door-fill"></i></span>
                <span class="edvora-nav-text">Home</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('teacher.dashboard') }}"
               class="edvora-nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" title="Dashboard">
                <span class="edvora-nav-icon"><i class="bi bi-grid-1x2-fill"></i></span>
                <span class="edvora-nav-text">Dashboard</span>
            </a>
        </li>

        <li class="edvora-nav-section-title">TEACHING</li>

        @php
            $teacherCourses = auth()->user()->courses()->latest()->take(6)->get();
            $totalTeacherCourses = auth()->user()->courses()->count();
            $isCoursesRoute = request()->is('teacher/courses*') || request()->routeIs('teacher.your-courses', 'courses.chat.show');
        @endphp

        <!-- My Courses Dropdown -->
        <li class="edvora-nav-item {{ $isCoursesRoute ? 'open' : '' }}">
            <button type="button" class="edvora-submenu-toggle {{ request()->routeIs('teacher.your-courses', 'teacher.courses.*') ? 'active' : '' }}"
                    id="myCoursesToggle"
                    onclick="this.closest('.edvora-nav-item').classList.toggle('open')"
                    title="My Courses">
                <span class="edvora-nav-icon"><i class="bi bi-journal-bookmark-fill"></i></span>
                <span class="edvora-nav-text">My Courses</span>
                @if($totalTeacherCourses > 0)
                    <span class="edvora-badge edvora-badge-info me-1">{{ $totalTeacherCourses }}</span>
                @endif
                <i class="bi bi-chevron-down edvora-chevron"></i>
            </button>

            <div class="edvora-submenu">
                <div class="edvora-submenu-inner">
                    <!-- Link to All Courses Hub -->
                    <a href="{{ route('teacher.your-courses') }}" class="edvora-sublink-all {{ request()->routeIs('teacher.your-courses') ? 'active' : '' }}">
                        <span><i class="bi bi-grid"></i> All Courses</span>
                        <span class="badge bg-white text-primary border">{{ $totalTeacherCourses }}</span>
                    </a>

                    <!-- Courses List -->
                    @forelse($teacherCourses as $course)
                        @php
                            $isDetailActive = request()->is('teacher/courses/'.$course->id) && !request()->has('tab');
                            $isChatActive = request()->routeIs('courses.chat.show') && optional(request()->route('course'))->id === $course->id;
                            $isPointsActive = request()->routeIs('teacher.courses.points*') && request()->route('id') == $course->id;
                            $isCurrentCourse = request()->is('teacher/courses/'.$course->id.'*') || $isChatActive;
                        @endphp
                        <div class="edvora-course-item {{ $isCurrentCourse ? 'active' : '' }}">
                            <div class="edvora-course-header">
                                <a href="{{ route('teacher.courses.detail', $course->id) }}" class="edvora-course-title" title="{{ $course->title }}">
                                    <span class="edvora-course-icon"><i class="bi bi-book-half"></i></span>
                                    <span class="edvora-course-name">{{ $course->title }}</span>
                                </a>
                            </div>
                            <div class="edvora-course-actions">
                                <a href="{{ route('teacher.courses.detail', $course->id) }}" 
                                   class="edvora-action-btn action-view {{ $isDetailActive ? 'active' : '' }}" 
                                   title="Course Details & Lessons">
                                    <i class="bi bi-eye"></i>
                                    <span>Details</span>
                                </a>
                                <a href="{{ route('courses.chat.show', $course) }}" 
                                   class="edvora-action-btn action-chat {{ $isChatActive ? 'active' : '' }}" 
                                   title="Course Live Chat">
                                    <i class="bi bi-chat-dots"></i>
                                    <span>Chat</span>
                                </a>
                                <a href="{{ route('teacher.courses.points', $course->id) }}" 
                                   class="edvora-action-btn action-points {{ $isPointsActive ? 'active' : '' }}" 
                                   title="Student Points & Scoring">
                                    <i class="bi bi-star"></i>
                                    <span>Points</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="edvora-submenu-empty">
                            <span>No courses created yet</span>
                        </div>
                    @endforelse

                    @if($totalTeacherCourses > 6)
                        <a href="{{ route('teacher.your-courses') }}" class="edvora-view-all-link">
                            <span>View All ({{ $totalTeacherCourses }})</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        </li>

        @php
            $teacherCourseIds = auth()->user()->courses()->pluck('id');
            $pendingEnrollmentsCount = \App\Models\EnrollmentRequest::whereIn('course_id', $teacherCourseIds)
                ->where('status', 'pending')
                ->count();
        @endphp

        <!-- Enrollment Requests -->
        <li class="edvora-nav-item">
            <a href="{{ route('teacher.enrollment-requests') }}"
               class="edvora-nav-link {{ request()->routeIs('teacher.enrollment-requests*') ? 'active' : '' }}" title="Enrollment Requests">
                <span class="edvora-nav-icon"><i class="bi bi-person-check-fill"></i></span>
                <span class="edvora-nav-text">Enrollments</span>
                @if($pendingEnrollmentsCount > 0)
                    <span class="edvora-badge edvora-badge-warning">{{ $pendingEnrollmentsCount }}</span>
                @endif
            </a>
        </li>

        <!-- Request Course -->
        <li class="edvora-nav-item">
            <a href="{{ route('teacher.request-courses') }}"
               class="edvora-nav-link {{ request()->routeIs('teacher.request-courses') ? 'active' : '' }}" title="Request New Course">
                <span class="edvora-nav-icon"><i class="bi bi-plus-circle-fill"></i></span>
                <span class="edvora-nav-text">Request Course</span>
            </a>
        </li>

        <!-- Quizzes -->
        <li class="edvora-nav-item">
            <a href="{{ route('teacher.exams.index') }}"
               class="edvora-nav-link {{ request()->routeIs('teacher.exams.*') ? 'active' : '' }}" title="Quizzes & Exams">
                <span class="edvora-nav-icon"><i class="bi bi-patch-question-fill"></i></span>
                <span class="edvora-nav-text">Quizzes</span>
            </a>
        </li>

        <li class="edvora-nav-section-title">ACCOUNT</li>

        <!-- Notifications -->
        <li class="edvora-nav-item">
            <a href="{{ route('teacher.notifications.index') }}"
               class="edvora-nav-link {{ request()->routeIs('teacher.notifications.*') ? 'active' : '' }}" title="Notifications">
                <span class="edvora-nav-icon"><i class="bi bi-bell-fill"></i></span>
                <span class="edvora-nav-text">Notifications</span>
                @php $sidebarUnread = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
                @if($sidebarUnread > 0)
                    <span class="edvora-badge edvora-badge-danger">{{ $sidebarUnread }}</span>
                @endif
            </a>
        </li>

        <!-- Profile -->
        <li class="edvora-nav-item">
            <a href="{{ route('profile') }}"
               class="edvora-nav-link {{ request()->routeIs('profile') ? 'active' : '' }}" title="My Profile">
                <span class="edvora-nav-icon"><i class="bi bi-person-fill-gear"></i></span>
                <span class="edvora-nav-text">Profile</span>
            </a>
        </li>

        <!-- Logout -->
        <li class="edvora-nav-item" style="margin-top: auto; padding-top: 0.5rem;">
            <a href="{{ route('logout') }}" class="edvora-nav-link danger" title="Logout"
               onclick="event.preventDefault(); document.getElementById('teacher-logout-form').submit();">
                <span class="edvora-nav-icon"><i class="bi bi-box-arrow-right"></i></span>
                <span class="edvora-nav-text">Logout</span>
            </a>
            <form id="teacher-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>
    </ul>
</aside>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/sidebar-v2.css') }}">
@endpush
