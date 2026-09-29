<!-- Mobile Sidebar Toggle Button -->
<button class="sidebar-mobile-toggle d-flex d-lg-none" id="sidebarMobileToggle" aria-label="Open sidebar" type="button">
    <i class="bi bi-list"></i>
</button>

@section('hide_header', true)
@section('hide_footer', true)

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="edvora-sidebar" id="sidebar">
    <div class="sidebar-collapse-toggle d-none d-lg-flex" id="sidebarCollapse" title="Toggle Sidebar">
        <i class="bi bi-chevron-left"></i>
    </div>

    <button type="button" class="sidebar-mobile-close d-flex d-lg-none" id="sidebarMobileClose" aria-label="Close sidebar">
        <i class="bi bi-x-lg"></i>
    </button>

    <!-- Brand Header -->
    <a href="{{ route('home') }}" class="edvora-brand">
        <div class="edvora-brand-logo-wrap">
            <img src="{{ asset('assets/images/logo1.jpg') }}" alt="Edvora Tech">
        </div>
        <div class="edvora-brand-info">
            <span class="edvora-brand-title">ادورا تک</span>
            <span class="edvora-brand-badge"><i class="bi bi-shield-check me-1"></i>مدرس رسمی</span>
        </div>
    </a>

    <!-- Nav Items -->
    <ul class="edvora-nav">
        <li class="edvora-nav-section-title">منوی اصلی</li>

        <li class="edvora-nav-item">
            <a href="{{ route('home') }}"
               class="edvora-nav-link {{ request()->routeIs('home') ? 'active' : '' }}" title="صفحه اصلی">
                <span class="edvora-nav-icon"><i class="bi bi-house-door-fill"></i></span>
                <span class="edvora-nav-text">صفحه اصلی</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('teacher.dashboard') }}"
               class="edvora-nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" title="داشبورد اساتید">
                <span class="edvora-nav-icon"><i class="bi bi-grid-1x2-fill"></i></span>
                <span class="edvora-nav-text">داشبورد</span>
            </a>
        </li>

        <li class="edvora-nav-section-title">آموزش و تدریس</li>

        @php
            $currentCourseId = request()->route('id') ?? (is_object(request()->route('course')) ? request()->route('course')->id : request()->route('course')) ?? (request()->segment(2) === 'courses' ? request()->segment(3) : null);
            $teacherCourses = auth()->user()->courses()->latest()->take(10)->get();
            if ($currentCourseId && !$teacherCourses->contains('id', (int)$currentCourseId)) {
                $extraCourse = auth()->user()->courses()->find($currentCourseId);
                if ($extraCourse) {
                    $teacherCourses->prepend($extraCourse);
                }
            }
            $totalTeacherCourses = auth()->user()->courses()->count();
            $isCoursesRoute = request()->is('teacher/courses*') || request()->routeIs('teacher.your-courses', 'courses.chat.show');
        @endphp

        <!-- My Courses Dropdown -->
        <li class="edvora-nav-item {{ $isCoursesRoute ? 'open' : '' }}">
            <button type="button" class="edvora-submenu-toggle {{ request()->routeIs('teacher.your-courses', 'teacher.courses.*') ? 'active' : '' }}"
                    id="myCoursesToggle"
                    onclick="this.closest('.edvora-nav-item').classList.toggle('open')"
                    title="دوره‌های من">
                <span class="edvora-nav-icon"><i class="bi bi-journal-bookmark-fill"></i></span>
                <span class="edvora-nav-text">دوره‌های من</span>
                @if($totalTeacherCourses > 0)
                    <span class="edvora-badge edvora-badge-info me-1">{{ $totalTeacherCourses }}</span>
                @endif
                <i class="bi bi-chevron-down edvora-chevron"></i>
            </button>

            <div class="edvora-submenu">
                <div class="edvora-submenu-inner">
                    <!-- Link to All Courses Hub -->
                    <a href="{{ route('teacher.your-courses') }}" class="edvora-sublink-all {{ request()->routeIs('teacher.your-courses') ? 'active' : '' }}">
                        <span><i class="bi bi-grid"></i> تمام دوره‌ها</span>
                        <span class="badge bg-white text-primary border">{{ $totalTeacherCourses }}</span>
                    </a>

                    <!-- Courses List -->
                    @forelse($teacherCourses as $course)
                        @php
                            $isThisCourse = ($currentCourseId && (int)$currentCourseId === (int)$course->id) || request()->is('teacher/courses/'.$course->id.'*');
                            $isDetailActive = (request()->is('teacher/courses/'.$course->id) || (request()->routeIs('teacher.courses.detail') && $isThisCourse)) && !request()->has('tab');
                            $isChatActive = request()->routeIs('courses.chat.show') && (optional(request()->route('course'))->id === $course->id || (int)$currentCourseId === (int)$course->id);
                            $isPointsActive = (request()->routeIs('teacher.courses.points*') || request()->is('teacher/courses/'.$course->id.'/points*')) && $isThisCourse;
                        @endphp
                        <div class="edvora-course-item {{ $isThisCourse ? 'active' : '' }}">
                            <div class="edvora-course-header">
                                <a href="{{ route('teacher.courses.detail', $course->id) }}" class="edvora-course-title" title="{{ $course->title }}">
                                    <span class="edvora-course-icon"><i class="bi bi-book-half"></i></span>
                                    <span class="edvora-course-name">{{ $course->title }}</span>
                                </a>
                            </div>
                            <div class="edvora-course-actions">
                                <a href="{{ route('teacher.courses.detail', $course->id) }}" 
                                   class="edvora-action-btn action-view {{ $isDetailActive ? 'active' : '' }}" 
                                   title="جزئیات دوره و سرفصل‌ها">
                                    <i class="bi bi-eye"></i>
                                    <span>جزئیات</span>
                                </a>
                                <a href="{{ route('courses.chat.show', $course) }}" 
                                   class="edvora-action-btn action-chat {{ $isChatActive ? 'active' : '' }}" 
                                   title="چت زنده صنف"
                                   data-bg-chat-course="{{ $course->id }}"
                                   data-bg-chat-user="{{ auth()->id() }}">
                                    <i class="bi bi-chat-dots"></i>
                                    <span>چت</span>
                                </a>
                            </div>

                            @if($isThisCourse && request()->routeIs('teacher.courses.detail'))
                            <!-- Course Detail Tabs in Sidebar -->
                            <div class="edvora-course-tabs-menu">
                                <button type="button" class="edvora-sidebar-tab-btn active" data-tab="syllabus" onclick="if(window.switchCourseTab){switchCourseTab('syllabus', this);}else{window.location.href='{{ route('teacher.courses.detail', $course->id) }}#tab-syllabus';}">
                                    <i class="bi bi-list-ol"></i>
                                    <span>سرفصل‌ها</span>
                                </button>
                                <button type="button" class="edvora-sidebar-tab-btn" data-tab="students" onclick="if(window.switchCourseTab){switchCourseTab('students', this);}else{window.location.href='{{ route('teacher.courses.detail', $course->id) }}#tab-students';}">
                                    <i class="bi bi-shield-check"></i>
                                    <span>دانشجویان و دسترسی</span>
                                </button>
                                <button type="button" class="edvora-sidebar-tab-btn" data-tab="referrals" onclick="if(window.switchCourseTab){switchCourseTab('referrals', this);}else{window.location.href='{{ route('teacher.courses.detail', $course->id) }}#tab-referrals';}">
                                    <i class="bi bi-link-45deg text-success"></i>
                                    <span>لینک معرفی و دعوت</span>
                                </button>
                                <button type="button" class="edvora-sidebar-tab-btn" data-tab="points" onclick="if(window.switchCourseTab){switchCourseTab('points', this);}else{window.location.href='{{ route('teacher.courses.detail', $course->id) }}#tab-points';}">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <span>سیستم نمرات</span>
                                </button>
                                <button type="button" class="edvora-sidebar-tab-btn" data-tab="reviews" onclick="if(window.switchCourseTab){switchCourseTab('reviews', this);}else{window.location.href='{{ route('teacher.courses.detail', $course->id) }}#tab-reviews';}">
                                    <i class="bi bi-star"></i>
                                    <span>دیدگاه‌ها</span>
                                </button>
                                <button type="button" class="edvora-sidebar-tab-btn" data-tab="documents" onclick="if(window.switchCourseTab){switchCourseTab('documents', this);}else{window.location.href='{{ route('teacher.courses.detail', $course->id) }}#tab-documents';}">
                                    <i class="bi bi-folder2-open"></i>
                                    <span>اسناد و منابع</span>
                                </button>
                                <button type="button" class="edvora-sidebar-tab-btn" data-tab="history" onclick="if(window.switchCourseTab){switchCourseTab('history', this);}else{window.location.href='{{ route('teacher.courses.detail', $course->id) }}#tab-history';}">
                                    <i class="bi bi-clock-history"></i>
                                    <span>تاریخچه جلسات</span>
                                </button>
                                <button type="button" class="edvora-sidebar-tab-btn" data-tab="attendance" onclick="if(window.switchCourseTab){switchCourseTab('attendance', this);}else{window.location.href='{{ route('teacher.courses.detail', $course->id) }}#tab-attendance';}">
                                    <i class="bi bi-person-check"></i>
                                    <span>حاضری و غیاب</span>
                                </button>
                                <button type="button" class="edvora-sidebar-tab-btn" data-tab="classnotes" onclick="if(window.switchCourseTab){switchCourseTab('classnotes', this);}else{window.location.href='{{ route('teacher.courses.detail', $course->id) }}#tab-classnotes';}">
                                    <i class="bi bi-journal-text"></i>
                                    <span>یادداشت‌های صنف</span>
                                </button>
                                <button type="button" class="edvora-sidebar-tab-btn" data-tab="chat" onclick="if(window.switchCourseTab){switchCourseTab('chat', this);}else{window.location.href='{{ route('teacher.courses.detail', $course->id) }}#tab-chat';}">
                                    <i class="bi bi-chat-heart"></i>
                                    <span>چت صنف</span>
                                    <span class="chat-unread-badge ms-auto" style="display:none;" data-chat-badge-course="{{ $course->id }}"></span>
                                </button>
                                <button type="button" class="edvora-sidebar-tab-btn" data-tab="curriculum" onclick="if(window.switchCourseTab){switchCourseTab('curriculum', this);}else{window.location.href='{{ route('teacher.courses.detail', $course->id) }}#tab-curriculum';}">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>ویرایش دروس</span>
                                </button>
                            </div>
                            @endif
                        </div>
                    @empty
                        <div class="edvora-submenu-empty">
                            <span>هنوز دوره‌ای ثبت نشده است</span>
                        </div>
                    @endforelse

                    @if($totalTeacherCourses > 6)
                        <a href="{{ route('teacher.your-courses') }}" class="edvora-view-all-link">
                            <span>مشاهده همه ({{ $totalTeacherCourses }})</span>
                            <i class="bi bi-arrow-left"></i>
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
               class="edvora-nav-link {{ request()->routeIs('teacher.enrollment-requests*') ? 'active' : '' }}" title="درخواست‌های ثبت‌نام">
                <span class="edvora-nav-icon"><i class="bi bi-person-check-fill"></i></span>
                <span class="edvora-nav-text">ثبت‌نام‌ها</span>
                @if($pendingEnrollmentsCount > 0)
                    <span class="edvora-badge edvora-badge-warning">{{ $pendingEnrollmentsCount }}</span>
                @endif
            </a>
        </li>

        <!-- Quizzes -->
        <li class="edvora-nav-item">
            <a href="{{ route('teacher.exams.index') }}"
               class="edvora-nav-link {{ request()->routeIs('teacher.exams.*') ? 'active' : '' }}" title="آزمون‌ها و کوییزها">
                <span class="edvora-nav-icon"><i class="bi bi-patch-question-fill"></i></span>
                <span class="edvora-nav-text">آزمون‌ها</span>
            </a>
        </li>

        <li class="edvora-nav-section-title">حساب کاربری</li>

        <!-- Notifications -->
        <li class="edvora-nav-item">
            <a href="{{ route('teacher.notifications.index') }}"
               class="edvora-nav-link {{ request()->routeIs('teacher.notifications.*') ? 'active' : '' }}" title="اعلان‌ها">
                <span class="edvora-nav-icon"><i class="bi bi-bell-fill"></i></span>
                <span class="edvora-nav-text">اعلان‌ها</span>
                @php $sidebarUnread = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
                @if($sidebarUnread > 0)
                    <span class="edvora-badge edvora-badge-danger">{{ $sidebarUnread }}</span>
                @endif
            </a>
        </li>

        <!-- Profile -->
        <li class="edvora-nav-item">
            <a href="{{ route('profile') }}"
               class="edvora-nav-link {{ request()->routeIs('profile') ? 'active' : '' }}" title="پروفایل من">
                <span class="edvora-nav-icon"><i class="bi bi-person-fill-gear"></i></span>
                <span class="edvora-nav-text">پروفایل</span>
            </a>
        </li>

        <!-- Logout -->
        <li class="edvora-nav-item">
            <a href="{{ route('logout') }}" class="edvora-nav-link danger" title="خروج از حساب"
               onclick="event.preventDefault(); document.getElementById('teacher-logout-form').submit();">
                <span class="edvora-nav-icon"><i class="bi bi-box-arrow-right"></i></span>
                <span class="edvora-nav-text">خروج</span>
            </a>
            <form id="teacher-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>
    </ul>
</aside>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/sidebar-v2.css') }}">
@endpush

<script>
(function() {
    function initTeacherSidebarComponent() {
        const sidebar = document.getElementById('sidebar');
        if (!sidebar || sidebar.dataset.sbInit === '1') return;
        sidebar.dataset.sbInit = '1';

        const mobileToggle = document.getElementById('sidebarMobileToggle');
        const mobileClose = document.getElementById('sidebarMobileClose');
        const overlay = document.getElementById('sidebarOverlay');
        const collapseBtn = document.getElementById('sidebarCollapse');
        const mainContent = document.querySelector('.main-content, #mainContent');

        function openSidebar() {
            sidebar.classList.add('active');
            if (overlay) overlay.classList.add('active');
            document.body.classList.add('sidebar-open');
        }

        function closeSidebar() {
            sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            document.body.classList.remove('sidebar-open');
        }

        if (mobileToggle) {
            mobileToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (sidebar.classList.contains('active')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }

        if (mobileClose) {
            mobileClose.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeSidebar();
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function(e) {
                e.preventDefault();
                closeSidebar();
            });
        }

        if (collapseBtn) {
            const isCollapsed = localStorage.getItem('teacherSidebarCollapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                if (mainContent) mainContent.classList.add('sidebar-collapsed');
            }

            collapseBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('collapsed');
                if (mainContent) mainContent.classList.toggle('sidebar-collapsed');
                localStorage.setItem('teacherSidebarCollapsed', sidebar.classList.contains('collapsed'));
            });
        }

        // Close when clicking nav items or tabs on mobile
        sidebar.querySelectorAll('.edvora-nav-link, .edvora-sublink, .edvora-sublink-all, .edvora-course-title, .edvora-action-btn, .edvora-view-all-link, .edvora-sidebar-tab-btn').forEach(function(el) {
            el.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    closeSidebar();
                }
            });
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                closeSidebar();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTeacherSidebarComponent);
    } else {
        initTeacherSidebarComponent();
    }
})();
</script>

