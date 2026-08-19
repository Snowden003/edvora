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
                <li>
                    <a href="{{ route('student.courses') }}" class="edvora-sublink {{ request()->routeIs('student.courses') ? 'active' : '' }}">
                        <i class="bi bi-grid"></i> All Courses
                    </a>
                </li>

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
                        <li>
                            <a href="{{ route('student.courses.learn', $enrollment->course->slug) }}" class="edvora-sublink {{ request()->is('student/courses/'.$enrollment->course->slug.'*') || (request()->routeIs('courses.chat.show') && optional(request()->route('course'))->id === $enrollment->course->id) ? 'active' : '' }}">
                                <i class="bi bi-book"></i> {{ Str::limit($enrollment->course->title, 20) }}
                            </a>
                            <ul class="edvora-sub-actions">
                                <li>
                                    <a href="{{ route('student.courses.learn', $enrollment->course->slug) }}" class="edvora-sub-action {{ request()->is('student/courses/'.$enrollment->course->slug.'/learn') && !request()->has('tab') ? 'active' : '' }}">
                                        <i class="bi bi-play-circle"></i> Learn
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('courses.chat.show', $enrollment->course) }}" class="edvora-sub-action {{ request()->routeIs('courses.chat.show') && optional(request()->route('course'))->id === $enrollment->course->id ? 'active' : '' }}">
                                        <i class="bi bi-chat-dots"></i> Chat
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @empty
                    <li class="edvora-submenu-empty">No enrolled courses</li>
                @endforelse

                @if(auth()->user()->enrollments()->where('status', '!=', 'banned')->count() > 6)
                    <li>
                        <a href="{{ route('student.courses') }}" class="edvora-sublink edvora-view-all">
                            <i class="bi bi-arrow-right-circle"></i> View All Courses
                        </a>
                    </li>
                @endif
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

        <li class="edvora-nav-item" style="margin-top:auto;">
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
