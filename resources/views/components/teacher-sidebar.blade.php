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
            <a href="{{ route('teacher.dashboard') }}"
               class="edvora-nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="edvora-nav-item {{ request()->is('teacher/courses*') || request()->routeIs('teacher.your-courses', 'courses.chat.show') ? 'open' : '' }}">
            <button type="button" class="edvora-submenu-toggle {{ request()->routeIs('teacher.your-courses', 'teacher.courses.*', 'courses.chat.show') ? 'active' : '' }}"
                    onclick="this.closest('.edvora-nav-item').classList.toggle('open')">
                <i class="bi bi-journal-bookmark"></i>
                <span>My Courses</span>
                <i class="bi bi-chevron-down edvora-chevron"></i>
            </button>
            <ul class="edvora-submenu">
                <li>
                    <a href="{{ route('teacher.your-courses') }}" class="edvora-sublink {{ request()->routeIs('teacher.your-courses') ? 'active' : '' }}">
                        <i class="bi bi-grid"></i> All Courses
                    </a>
                </li>

                @php
                    $teacherCourses = auth()->user()->courses()->latest()->take(6)->get();
                @endphp

                @forelse($teacherCourses as $course)
                    <li>
                        <a href="{{ route('teacher.courses.detail', $course->id) }}" class="edvora-sublink {{ request()->is('teacher/courses/'.$course->id.'*') || (request()->routeIs('courses.chat.show') && optional(request()->route('course'))->id === $course->id) ? 'active' : '' }}">
                            <i class="bi bi-book"></i> {{ Str::limit($course->title, 20) }}
                        </a>
                        <ul class="edvora-sub-actions">
                            <li>
                                <a href="{{ route('teacher.courses.detail', $course->id) }}" class="edvora-sub-action {{ request()->is('teacher/courses/'.$course->id) && !request()->has('tab') ? 'active' : '' }}">
                                    <i class="bi bi-eye"></i> Details
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('courses.chat.show', $course) }}" class="edvora-sub-action {{ request()->routeIs('courses.chat.show') && optional(request()->route('course'))->id === $course->id ? 'active' : '' }}">
                                    <i class="bi bi-chat-dots"></i> Chat
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('teacher.courses.points', $course->id) }}" class="edvora-sub-action {{ request()->routeIs('teacher.courses.points*') && request()->route('id') == $course->id ? 'active' : '' }}">
                                    <i class="bi bi-star"></i> Scoring
                                </a>
                            </li>
                        </ul>
                    </li>
                @empty
                    <li class="edvora-submenu-empty">No courses yet</li>
                @endforelse

                @if(auth()->user()->courses()->count() > 6)
                    <li>
                        <a href="{{ route('teacher.your-courses') }}" class="edvora-sublink edvora-view-all">
                            <i class="bi bi-arrow-right-circle"></i> View All Courses
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('teacher.request-courses') }}"
               class="edvora-nav-link {{ request()->routeIs('teacher.request-courses') ? 'active' : '' }}">
                <i class="bi bi-plus-circle-dotted"></i>
                <span>Request Courses</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('profile') }}"
               class="edvora-nav-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i>
                <span>Profile</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('teacher.exams.index') }}"
               class="edvora-nav-link {{ request()->routeIs('teacher.exams.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Quizzes</span>
            </a>
        </li>

        <li class="edvora-nav-item">
            <a href="{{ route('teacher.notifications.index') }}"
               class="edvora-nav-link {{ request()->routeIs('teacher.notifications.*') ? 'active' : '' }}">
                <i class="bi bi-bell"></i>
                <span>Notifications</span>
                @php $sidebarUnread = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
                @if($sidebarUnread > 0)
                    <span class="edvora-badge">{{ $sidebarUnread }}</span>
                @endif
            </a>
        </li>

        <li class="edvora-nav-item" style="margin-top:auto;">
            <a href="{{ route('logout') }}" class="edvora-nav-link danger"
               onclick="event.preventDefault(); document.getElementById('teacher-logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
            <form id="teacher-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>
    </ul>
</aside>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/sidebar-v2.css') }}">
@endpush
