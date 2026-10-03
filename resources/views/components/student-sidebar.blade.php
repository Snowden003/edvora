<!-- Mobile Sidebar Toggle Button -->
<button class="sidebar-mobile-toggle d-flex d-lg-none" id="sidebarMobileToggle" aria-label="باز کردن منو" type="button">
    <i class="bi bi-list"></i>
</button>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="edvora-sidebar" id="sidebar">
    <!-- Brand Header with Integrated Collapse Toggle Button -->
    <div class="edvora-brand-header d-flex align-items-center justify-content-between p-2 mb-2 border-bottom">
        <a href="{{ route('home') }}" class="edvora-brand d-flex align-items-center gap-2 mb-0 border-0 p-0 flex-grow-1 text-decoration-none">
            <div class="edvora-brand-logo-wrap">
                <img src="{{ asset('logo.png') }}" alt="Edvora Tech">
            </div>
            <div class="edvora-brand-info">
                <span class="edvora-brand-title">ادوُرا تِک</span>
                <span class="edvora-brand-badge"><i class="bi bi-mortarboard-fill me-1"></i>پورتال شاگردان</span>
            </div>
        </a>

        <!-- Sleek and clearly visible toggle button -->
        <button type="button" class="sidebar-collapse-toggle-btn d-none d-lg-flex" id="sidebarCollapse" title="تغییر اندازه سایدبار" aria-label="تغییر اندازه سایدبار">
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>

    <ul class="edvora-nav">
        <!-- 1. MAIN MENU -->
        <li class="edvora-nav-section-title">منوی اصلی</li>

        <li class="edvora-nav-item">
            <a href="{{ route('home') }}"
               class="edvora-nav-link {{ request()->routeIs('home') ? 'active' : '' }}" title="صفحه اصلی">
                <span class="edvora-nav-icon"><i class="bi bi-house-door-fill"></i></span>
                <span class="edvora-nav-text">صفحه اصلی</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ auth()->user()->dashboardRoute() }}"
               class="edvora-nav-link {{ request()->routeIs('student.dashboard', 'teacher.dashboard', 'admin.dashboard') ? 'active' : '' }}" title="داشبورد">
                <span class="edvora-nav-icon"><i class="bi bi-grid-1x2-fill"></i></span>
                <span class="edvora-nav-text">داشبورد</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('ai.chat.page') }}"
               class="edvora-nav-link {{ request()->routeIs('ai.chat.page') ? 'active' : '' }}" title="دستیار هوش مصنوعی ادوُرا">
                <span class="edvora-nav-icon"><i class="bi bi-robot text-primary"></i></span>
                <span class="edvora-nav-text">دستیار هوش مصنوعی</span>
                <span class="edvora-badge edvora-badge-info">AI</span>
            </a>
        </li>

        <!-- 2. ACADEMICS -->
        <li class="edvora-nav-section-title">بخش درسی و تعلیمی</li>

        <li class="edvora-nav-item {{ request()->is('student/courses*') || request()->routeIs('student.courses', 'courses.chat.show') ? 'open' : '' }}">
            <button type="button" class="edvora-submenu-toggle {{ request()->routeIs('student.courses', 'student.courses.*', 'courses.chat.show') ? 'active' : '' }}"
                    onclick="this.closest('.edvora-nav-item').classList.toggle('open')" title="کورس‌های من">
                <span class="edvora-nav-icon"><i class="bi bi-journal-bookmark-fill"></i></span>
                <span class="edvora-nav-text">کورس‌های من</span>
                <i class="bi bi-chevron-down edvora-chevron"></i>
            </button>
            <ul class="edvora-submenu">
                <div class="edvora-submenu-inner">
                    <a href="{{ route('student.courses') }}" class="edvora-sublink-all {{ request()->routeIs('student.courses') ? 'active' : '' }}">
                        <span><i class="bi bi-grid"></i> تمام کورس‌ها</span>
                    </a>

                    @php
                        $studentCourses = auth()->user()->enrollments()
                            ->where('status', '!=', 'banned')
                            ->with('course')
                            ->latest()
                            ->take(6)
                            ->get();
                    @endphp

                    @forelse($studentCourses as $enrollment)
                        @if($enrollment->course)
                            <div class="edvora-course-item {{ request()->is('student/courses/'.$enrollment->course->slug.'*') || (request()->routeIs('courses.chat.show') && optional(request()->route('course'))->id === $enrollment->course->id) ? 'active' : '' }}">
                                <div class="edvora-course-header">
                                    <a href="{{ route('student.courses.learn', $enrollment->course->slug) }}" class="edvora-course-title">
                                        <i class="bi bi-book edvora-course-icon"></i>
                                        <span class="edvora-course-name">{{ Str::limit($enrollment->course->title, 20) }}</span>
                                    </a>
                                </div>
                                <div class="edvora-course-actions">
                                    <a href="{{ route('student.courses.learn', $enrollment->course->slug) }}" class="edvora-action-btn action-view {{ request()->is('student/courses/'.$enrollment->course->slug.'/learn') && !request()->has('tab') ? 'active' : '' }}">
                                        <i class="bi bi-play-circle"></i> درس
                                    </a>
                                    <a href="{{ route('courses.chat.show', $enrollment->course) }}" 
                                       class="edvora-action-btn action-chat {{ request()->routeIs('courses.chat.show') && optional(request()->route('course'))->id === $enrollment->course->id ? 'active' : '' }}"
                                       data-bg-chat-course="{{ $enrollment->course->id }}"
                                       data-bg-chat-user="{{ auth()->id() }}">
                                        <i class="bi bi-chat-dots"></i> گفتگو
                                    </a>
                                </div>
                                
                                @if(request()->is('student/courses/'.$enrollment->course->slug.'/learn'))
                                <div class="edvora-course-tabs-menu">
                                    <button type="button" class="edvora-sidebar-tab-btn learning-tab active" data-tab="curriculum">
                                        <i class="bi bi-list-check"></i>
                                        <span>مفردات و مباحث</span>
                                    </button>
                                    <button type="button" class="edvora-sidebar-tab-btn learning-tab" data-tab="documents">
                                        <i class="bi bi-folder"></i>
                                        <span>اسناد و مواد درسی</span>
                                    </button>
                                    <button type="button" class="edvora-sidebar-tab-btn learning-tab" data-tab="notes">
                                        <i class="bi bi-sticky"></i>
                                        <span>نوت‌های درسی</span>
                                    </button>
                                    <button type="button" class="edvora-sidebar-tab-btn learning-tab" data-tab="sessions">
                                        <i class="bi bi-camera-video"></i>
                                        <span>جلسات آنلاین</span>
                                    </button>
                                    <button type="button" class="edvora-sidebar-tab-btn learning-tab" data-tab="quizzes">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>امتحانات و آزمون‌ها</span>
                                    </button>
                                    <button type="button" class="edvora-sidebar-tab-btn learning-tab" data-tab="reviews">
                                        <i class="bi bi-star"></i>
                                        <span>نظرات و ارزیابی</span>
                                    </button>
                                    <button type="button" class="edvora-sidebar-tab-btn learning-tab" data-tab="chat">
                                        <i class="bi bi-chat-heart"></i>
                                        <span>چت کورس</span>
                                    </button>
                                </div>
                                @endif
                            </div>
                        @endif
                    @empty
                        <div class="edvora-submenu-empty">هیچ کورسی ثبت‌نام نشده</div>
                    @endforelse

                    @if(auth()->user()->enrollments()->where('status', '!=', 'banned')->count() > 6)
                        <a href="{{ route('student.courses') }}" class="edvora-view-all-link">
                            مشاهده تمام کورس‌ها <i class="bi bi-arrow-left-circle"></i>
                        </a>
                    @endif
                </div>
            </ul>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('student.exams.index') }}"
               class="edvora-nav-link {{ request()->routeIs('student.exams.*') ? 'active' : '' }}" title="امتحانات و آزمون‌ها">
                <span class="edvora-nav-icon"><i class="bi bi-clipboard-check-fill"></i></span>
                <span class="edvora-nav-text">امتحانات و آزمون‌ها</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('student.certificates') }}"
               class="edvora-nav-link {{ request()->routeIs('student.certificates') ? 'active' : '' }}" title="تصدیق‌نامه‌ها">
                <span class="edvora-nav-icon"><i class="bi bi-award-fill"></i></span>
                <span class="edvora-nav-text">تصدیق‌نامه‌ها</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('courses.index') }}"
               class="edvora-nav-link {{ request()->routeIs('courses.index') ? 'active' : '' }}" title="جستجوی کورس‌ها">
                <span class="edvora-nav-icon"><i class="bi bi-compass-fill"></i></span>
                <span class="edvora-nav-text">جستجوی کورس‌ها</span>
            </a>
        </li>

        <!-- 3. COMMUNITY -->
        <li class="edvora-nav-section-title">بخش جامعه و محصلین</li>

        <li class="edvora-nav-item">
            <a href="{{ route('leaderboard') }}"
               class="edvora-nav-link {{ request()->routeIs('leaderboard') ? 'active' : '' }}" title="جدول پیشتازان">
                <span class="edvora-nav-icon"><i class="bi bi-trophy-fill"></i></span>
                <span class="edvora-nav-text">جدول پیشتازان</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('scoring.help') }}"
               class="edvora-nav-link {{ request()->routeIs('scoring.help') ? 'active' : '' }}" title="نحوه محاسبه نمرات">
                <span class="edvora-nav-icon"><i class="bi bi-question-circle-fill"></i></span>
                <span class="edvora-nav-text">نحوه محاسبه نمرات</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('student.notifications.index') }}"
               class="edvora-nav-link {{ request()->routeIs('student.notifications.*') ? 'active' : '' }}" title="آگاهی‌ها">
                <span class="edvora-nav-icon"><i class="bi bi-bell-fill"></i></span>
                <span class="edvora-nav-text">آگاهی‌ها</span>
                @php $sidebarUnread = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
                @if($sidebarUnread > 0)
                    <span class="edvora-badge edvora-badge-danger me-auto">{{ $sidebarUnread }}</span>
                @endif
            </a>
        </li>

        <!-- 4. ACCOUNT -->
        <li class="edvora-nav-section-title">حساب کاربری</li>

        <li class="edvora-nav-item">
            <a href="{{ route('student.profile') }}"
               class="edvora-nav-link {{ request()->routeIs('student.profile', 'profile') ? 'active' : '' }}" title="پروفایل من">
                <span class="edvora-nav-icon"><i class="bi bi-person-circle"></i></span>
                <span class="edvora-nav-text">پروفایل من</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('student.profile-details') }}"
               class="edvora-nav-link {{ request()->routeIs('student.profile-details') ? 'active' : '' }}" title="مشخصات فردی">
                <span class="edvora-nav-icon"><i class="bi bi-person-vcard-fill"></i></span>
                <span class="edvora-nav-text">مشخصات فردی</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('logout') }}" class="edvora-nav-link danger" title="خروج از حساب"
               onclick="event.preventDefault(); document.getElementById('student-logout-form').submit();">
                <span class="edvora-nav-icon"><i class="bi bi-box-arrow-right"></i></span>
                <span class="edvora-nav-text">خروج از حساب</span>
            </a>
            <form id="student-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>
    </ul>

    <!-- Bottom Student Mini Profile Card -->
    @auth
    @php
        $authUser = auth()->user();
        $authLevel = method_exists($authUser, 'level') ? $authUser->level() : ['level' => 1, 'title' => 'شاگرد'];
        $authAvatar = method_exists($authUser, 'publicAvatarUrl') ? $authUser->publicAvatarUrl() : 'https://ui-avatars.com/api/?name='.urlencode($authUser->name).'&background=1f8fff&color=fff';
    @endphp
    <a href="{{ route('student.profile') }}" class="edvora-sidebar-user-card" title="مشاهده پروفایل">
        <div class="edvora-user-avatar-wrap">
            <img src="{{ $authAvatar }}" alt="{{ $authUser->name }}" class="edvora-user-avatar" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($authUser->name) }}&size=80&background=1f8fff&color=fff'">
            <span class="edvora-user-status-dot" title="فعال"></span>
        </div>
        <div class="edvora-user-details">
            <span class="edvora-user-name">{{ $authUser->name }}</span>
            <span class="edvora-user-role">سطح {{ $authLevel['level'] ?? 1 }} · شاگرد</span>
        </div>
        <div class="edvora-user-action-btn">
            <i class="bi bi-gear-fill"></i>
        </div>
    </a>
    @endauth
</aside>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/sidebar-v2.css') }}?v={{ file_exists(public_path('assets/css/sidebar-v2.css')) ? filemtime(public_path('assets/css/sidebar-v2.css')) : time() }}">
@endpush

<script>
(function() {
    function initStudentSidebarComponent() {
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
            const isCollapsed = localStorage.getItem('studentSidebarCollapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                if (mainContent) mainContent.classList.add('sidebar-collapsed');
            }

            collapseBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('collapsed');
                if (mainContent) mainContent.classList.toggle('sidebar-collapsed');
                localStorage.setItem('studentSidebarCollapsed', sidebar.classList.contains('collapsed'));
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
        document.addEventListener('DOMContentLoaded', initStudentSidebarComponent);
    } else {
        initStudentSidebarComponent();
    }
})();
</script>
