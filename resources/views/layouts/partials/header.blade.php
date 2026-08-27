{{-- ===== MOBILE DRAWER (outside navbar, fully custom) ===== --}}
<div class="mobile-nav-overlay" id="mobileNavOverlay"></div>
<div class="mobile-drawer" id="mobileDrawer">
    <div class="mobile-drawer-header">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            <i class="bi bi-mortarboard-fill me-2"></i>{{ $siteSettings->get('company_name', 'Edvora Tech') }}
        </a>
        <button class="mobile-drawer-close" id="mobileDrawerClose" aria-label="Close">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="mobile-drawer-body">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                    <i class="bi bi-house"></i>Home
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}" href="{{ route('courses.index') }}">
                    <i class="bi bi-journal-bookmark"></i>Courses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}" href="{{ route('books.index') }}">
                    <i class="bi bi-book"></i>Books
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}" href="{{ route('teachers.index') }}">
                    <i class="bi bi-person-workspace"></i>Teachers
                </a>
            </li>

            {{-- Community dropdown --}}
            <li class="nav-item dropdown {{ request()->routeIs(['events.*','leaderboard']) ? 'open' : '' }}">
                <a class="nav-link dropdown-toggle" href="#" role="button">
                    <i class="bi bi-people"></i>Community
                </a>
                <ul class="mobile-sub-menu {{ request()->routeIs(['events.*','leaderboard']) ? 'open' : '' }}">
                    <span class="dropdown-header-label">Engage</span>
                    <li><a class="dropdown-item {{ request()->routeIs('events.*') ? 'active' : '' }}" href="{{ route('events.index') }}"><i class="bi bi-calendar-event"></i>Events</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('leaderboard') ? 'active' : '' }}" href="{{ route('leaderboard') }}"><i class="bi bi-bar-chart-steps"></i>Leaderboard</a></li>
                </ul>
            </li>

            {{-- Resources dropdown --}}
            <li class="nav-item dropdown {{ request()->routeIs(['roadmap','foundation']) ? 'open' : '' }}">
                <a class="nav-link dropdown-toggle" href="#" role="button">
                    <i class="bi bi-layers"></i>Resources
                </a>
                <ul class="mobile-sub-menu {{ request()->routeIs(['roadmap','foundation']) ? 'open' : '' }}">
                    <span class="dropdown-header-label">Learn</span>
                    <li><a class="dropdown-item {{ request()->routeIs('roadmap') ? 'active' : '' }}" href="{{ route('roadmap') }}"><i class="bi bi-signpost-split"></i>Roadmap</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('foundation') ? 'active' : '' }}" href="{{ route('foundation') }}"><i class="bi bi-building"></i>Foundation</a></li>
                </ul>
            </li>

            {{-- About dropdown --}}
            <li class="nav-item dropdown {{ request()->routeIs(['about','story','how-we-work','terms','privacy']) ? 'open' : '' }}">
                <a class="nav-link dropdown-toggle" href="#" role="button">
                    <i class="bi bi-info-circle"></i>About
                </a>
                <ul class="mobile-sub-menu {{ request()->routeIs(['about','story','how-we-work','terms','privacy']) ? 'open' : '' }}">
                    <span class="dropdown-header-label">Company</span>
                    <li><a class="dropdown-item {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}"><i class="bi bi-info-circle"></i>About Us</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('story') ? 'active' : '' }}" href="{{ route('story') }}"><i class="bi bi-book"></i>Our Story</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('how-we-work') ? 'active' : '' }}" href="{{ route('how-we-work') }}"><i class="bi bi-gear"></i>How We Work</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item {{ request()->routeIs('terms') ? 'active' : '' }}" href="{{ route('terms') }}"><i class="bi bi-file-text"></i>Terms</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('privacy') ? 'active' : '' }}" href="{{ route('privacy') }}"><i class="bi bi-shield-check"></i>Privacy</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                    <i class="bi bi-envelope"></i>Contact
                </a>
            </li>
        </ul>

        <div class="mobile-drawer-auth">
            @guest
                <a href="{{ route('login') }}" class="btn btn-outline-light rounded-pill">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary rounded-pill btn-glow">Sign Up</a>
            @else
                <div class="dropdown">
                    <button class="btn btn-outline-light rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-2"></i>{{ Auth::user()->name }}
                    </button>
                    <ul class="dropdown-menu glass-dropdown">
                        <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person-circle"></i>Profile</a></li>
                        @if(Auth::user()->role === 'student')
                            <li><a class="dropdown-item" href="{{ route('student.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a></li>
                        @elseif(Auth::user()->role === 'teacher')
                            <li><a class="dropdown-item" href="{{ route('teacher.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a></li>
                        @elseif(Auth::user()->role === 'admin')
                            <li><a class="dropdown-item" href="{{ url('/admin-panel') }}"><i class="bi bi-speedometer2"></i>Admin Panel</a></li>
                            <li><a class="dropdown-item" href="{{ url('/admin-panel/company-pages') }}"><i class="bi bi-file-earmark-text"></i>Company Pages</a></li>
                            <li><a class="dropdown-item" href="{{ url('/admin-panel/site-settings') }}"><i class="bi bi-gear"></i>Site Settings</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item" style="color:#ff6b6b"><i class="bi bi-box-arrow-right" style="color:#ff6b6b"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endguest
        </div>
    </div>
</div>

{{-- ===== DESKTOP NAVBAR ===== --}}
<nav class="navbar navbar-expand-lg navbar-dark sticky-top glass-header">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            <i class="bi bi-mortarboard-fill me-2"></i>{{ $siteSettings->get('company_name', 'Edvora Tech') }}
        </a>

        <button class="navbar-toggler" id="mobileNavToggler" type="button" aria-label="Open menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}" href="{{ route('courses.index') }}">Courses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}" href="{{ route('books.index') }}">Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}" href="{{ route('teachers.index') }}">Teachers</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs(['events.*', 'leaderboard']) ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                        Community
                    </a>
                    <ul class="dropdown-menu glass-dropdown">
                        <li><span class="dropdown-header-label">Engage</span></li>
                        <li><a class="dropdown-item {{ request()->routeIs('events.*') ? 'active' : '' }}" href="{{ route('events.index') }}"><i class="bi bi-calendar-event"></i>Events</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('leaderboard') ? 'active' : '' }}" href="{{ route('leaderboard') }}"><i class="bi bi-bar-chart-steps"></i>Leaderboard</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs(['roadmap', 'foundation']) ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                        Resources
                    </a>
                    <ul class="dropdown-menu glass-dropdown">
                        <li><span class="dropdown-header-label">Learn</span></li>
                        <li><a class="dropdown-item {{ request()->routeIs('roadmap') ? 'active' : '' }}" href="{{ route('roadmap') }}"><i class="bi bi-signpost-split"></i>Roadmap</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('foundation') ? 'active' : '' }}" href="{{ route('foundation') }}"><i class="bi bi-building"></i>Foundation</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs(['about', 'story', 'how-we-work', 'terms', 'privacy']) ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                        About
                    </a>
                    <ul class="dropdown-menu glass-dropdown">
                        <li><span class="dropdown-header-label">Company</span></li>
                        <li><a class="dropdown-item {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}"><i class="bi bi-info-circle"></i>About Us</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('story') ? 'active' : '' }}" href="{{ route('story') }}"><i class="bi bi-book"></i>Our Story</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('how-we-work') ? 'active' : '' }}" href="{{ route('how-we-work') }}"><i class="bi bi-gear"></i>How We Work</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item {{ request()->routeIs('terms') ? 'active' : '' }}" href="{{ route('terms') }}"><i class="bi bi-file-text"></i>Terms</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('privacy') ? 'active' : '' }}" href="{{ route('privacy') }}"><i class="bi bi-shield-check"></i>Privacy</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
                </li>
            </ul>

            <div class="d-flex gap-2">
                @guest
                    <button class="btn btn-outline-light btn-sm rounded-pill px-4" onclick="location.href='{{ route('login') }}'">Login</button>
                    <button class="btn btn-primary btn-sm rounded-pill px-4 btn-glow" onclick="location.href='{{ route('register') }}'">Sign Up</button>
                @else
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm rounded-pill px-4 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu glass-dropdown dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person-circle"></i>Profile</a></li>
                            @if(Auth::user()->role === 'student')
                                <li><a class="dropdown-item" href="{{ route('student.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a></li>
                            @elseif(Auth::user()->role === 'teacher')
                                <li><a class="dropdown-item" href="{{ route('teacher.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a></li>
                            @elseif(Auth::user()->role === 'admin')
                                <li><a class="dropdown-item" href="{{ url('/admin-panel') }}"><i class="bi bi-speedometer2"></i>Admin Panel</a></li>
                                <li><a class="dropdown-item" href="{{ url('/admin-panel/company-pages') }}"><i class="bi bi-file-earmark-text"></i>Company Pages</a></li>
                                <li><a class="dropdown-item" href="{{ url('/admin-panel/site-settings') }}"><i class="bi bi-gear"></i>Site Settings</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right" style="color:#dc3545!important"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</nav>
