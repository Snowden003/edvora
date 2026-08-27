<!-- Mobile Sidebar Toggle Button -->
<button class="sidebar-mobile-toggle d-flex d-lg-none" id="sidebarMobileToggle" aria-label="Open sidebar">
    <i class="bi bi-list"></i>
</button>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="edvora-sidebar" id="sidebar">
    <div class="sidebar-collapse-toggle d-none d-lg-flex" id="sidebarCollapse">
        <i class="bi bi-chevron-left"></i>
    </div>

    <a href="{{ route('home') }}" class="edvora-brand">
        <img src="{{ asset('assets/images/logo1.jpg') }}" alt="Edvora Tech">
        <span>Edvora Tech</span>
    </a>

    <ul class="edvora-nav">
        <li class="edvora-nav-item">
            <a href="{{ route('home') }}"
               class="edvora-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="bi bi-house-fill"></i>
                <span>Home</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ auth()->user()->dashboardRoute() }}"
               class="edvora-nav-link {{ request()->routeIs('student.dashboard', 'teacher.dashboard', 'admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="edvora-nav-item {{ request()->is('student/courses*') || request()->routeIs('student.courses', 'courses.chat.show') ? 'open' : '' }}">
            <button type="button" class="edvora-submenu-toggle {{ request()->routeIs('student.courses', 'student.courses.*', 'courses.chat.show') ? 'active' : '' }}"
                    onclick="this.closest('.edvora-nav-item').classList.toggle('open')">
                <i class="bi bi-journal-bookmark"></i>
                <span>My Courses</span>
                <i class="bi bi-chevron-down edvora-chevron"></i>
            </button>
            <ul class="edvora-submenu">
                <div class="edvora-submenu-inner">
                    <a href="{{ route('student.courses') }}" class="edvora-sublink-all {{ request()->routeIs('student.courses') ? 'active' : '' }}">
                        <span><i class="bi bi-grid"></i> All Courses</span>
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
                                        <i class="bi bi-play-circle"></i> Learn
                                    </a>
                                    <a href="{{ route('courses.chat.show', $enrollment->course) }}" 
                                       class="edvora-action-btn action-chat {{ request()->routeIs('courses.chat.show') && optional(request()->route('course'))->id === $enrollment->course->id ? 'active' : '' }}"
                                       data-bg-chat-course="{{ $enrollment->course->id }}"
                                       data-bg-chat-user="{{ auth()->id() }}">
                                        <i class="bi bi-chat-dots"></i> Chat
                                    </a>
                                </div>
                                
                                @if(request()->is('student/courses/'.$enrollment->course->slug.'/learn'))
                                <div class="edvora-course-tabs-menu">
                                    <button type="button" class="edvora-sidebar-tab-btn learning-tab active" data-tab="curriculum">
                                        <i class="bi bi-list-check"></i>
                                        <span>Curriculum</span>
                                    </button>
                                    <button type="button" class="edvora-sidebar-tab-btn learning-tab" data-tab="documents">
                                        <i class="bi bi-folder"></i>
                                        <span>Files & Documents</span>
                                    </button>
                                    <button type="button" class="edvora-sidebar-tab-btn learning-tab" data-tab="notes">
                                        <i class="bi bi-sticky"></i>
                                        <span>Class Notes</span>
                                    </button>
                                    <button type="button" class="edvora-sidebar-tab-btn learning-tab" data-tab="sessions">
                                        <i class="bi bi-camera-video"></i>
                                        <span>Sessions</span>
                                    </button>
                                    <button type="button" class="edvora-sidebar-tab-btn learning-tab" data-tab="quizzes">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>Quizzes</span>
                                    </button>
                                    <button type="button" class="edvora-sidebar-tab-btn learning-tab" data-tab="reviews">
                                        <i class="bi bi-star"></i>
                                        <span>Reviews</span>
                                    </button>
                                    <button type="button" class="edvora-sidebar-tab-btn learning-tab" data-tab="chat">
                                        <i class="bi bi-chat-heart"></i>
                                        <span>Course Chat</span>
                                    </button>
                                </div>
                                @endif
                            </div>
                        @endif
                    @empty
                        <div class="edvora-submenu-empty">No enrolled courses</div>
                    @endforelse

                    @if(auth()->user()->enrollments()->where('status', '!=', 'banned')->count() > 6)
                        <a href="{{ route('student.courses') }}" class="edvora-view-all-link">
                            View All Courses <i class="bi bi-arrow-right-circle"></i>
                        </a>
                    @endif
                </div>
            </ul>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('student.exams.index') }}"
               class="edvora-nav-link {{ request()->routeIs('student.exams.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Quizzes</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('student.certificates') }}"
               class="edvora-nav-link {{ request()->routeIs('student.certificates') ? 'active' : '' }}">
                <i class="bi bi-award"></i>
                <span>Certificates</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('courses.index') }}"
               class="edvora-nav-link {{ request()->routeIs('courses.index') ? 'active' : '' }}">
                <i class="bi bi-mortarboard"></i>
                <span>Explore Courses</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('leaderboard') }}"
               class="edvora-nav-link {{ request()->routeIs('leaderboard') ? 'active' : '' }}">
                <i class="bi bi-bar-chart"></i>
                <span>Leaderboard</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('scoring.help') }}"
               class="edvora-nav-link {{ request()->routeIs('scoring.help') ? 'active' : '' }}">
                <i class="bi bi-question-circle"></i>
                <span>How Scoring Works</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('student.notifications.index') }}"
               class="edvora-nav-link {{ request()->routeIs('student.notifications.*') ? 'active' : '' }}">
                <i class="bi bi-bell"></i>
                <span>Notifications</span>
                @php $sidebarUnread = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
                @if($sidebarUnread > 0)
                    <span class="edvora-badge">{{ $sidebarUnread }}</span>
                @endif
            </a>
        </li>

        <li><hr class="edvora-divider"></li>

        <li class="edvora-nav-item">
            <a href="{{ route('student.profile') }}"
               class="edvora-nav-link {{ request()->routeIs('student.profile', 'profile') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i>
                <span>Profile</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('student.profile-details') }}"
               class="edvora-nav-link {{ request()->routeIs('student.profile-details') ? 'active' : '' }}">
                <i class="bi bi-person-vcard"></i>
                <span>My Details</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('logout') }}" class="edvora-nav-link danger"
               onclick="event.preventDefault(); document.getElementById('student-logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
            <form id="student-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>
    </ul>
</aside>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/sidebar-v2.css') }}">
@endpush
