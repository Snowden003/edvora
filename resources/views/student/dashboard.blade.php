@extends('layouts.app')

@section('title', 'Student Dashboard - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/student-dashboard.css') }}" rel="stylesheet" />
@endpush

@section('hide_header', true)
@section('hide_footer', true)

@section('content')
<div class="dashboard-wrapper">
    <!-- Sidebar Navigation -->
    <x-student-sidebar />

    <!-- Main Dashboard Content -->
    <main class="main-content" id="mainContent">
        <div class="container-fluid py-4 py-lg-5 px-3 px-md-4">
            
            <!-- 1. Hero / Welcome Banner -->
            @php $level = $user->level(); @endphp
            <div class="row mb-4 mb-lg-5">
                <div class="col-12 col-xl-11 mx-auto">
                    <div class="welcome-banner-premium">
                        <div class="banner-bg-animation">
                            <div class="floating-icon icon-1"><i class="bi bi-mortarboard"></i></div>
                            <div class="floating-icon icon-2"><i class="bi bi-stars"></i></div>
                            <div class="floating-icon icon-3"><i class="bi bi-lightning-charge"></i></div>
                            <div class="floating-icon icon-4"><i class="bi bi-award"></i></div>
                            <div class="banner-particle p-1"></div>
                            <div class="banner-particle p-2"></div>
                        </div>

                        <div class="glass-inner-panel">
                            <div class="row align-items-center g-4">
                                <div class="col-lg-8">
                                    <div class="user-level-badge">
                                        <i class="bi bi-patch-check-fill"></i>
                                        <span>Level {{ $level['level'] }} · {{ $level['title'] }}</span>
                                    </div>
                                    <h1 class="main-title mb-3">
                                        Welcome back, <span class="highlight-text">{{ $user->name }}!</span> 👋
                                    </h1>
                                    <p class="description-text mb-4">
                                        Ready to achieve your daily goals? You have <strong class="text-white">{{ $enrollments->count() }} active course{{ $enrollments->count() !== 1 ? 's' : '' }}</strong> and <strong class="text-white">{{ $upcomingEvents->count() }} upcoming event{{ $upcomingEvents->count() !== 1 ? 's' : '' }}</strong> waiting for you.
                                    </p>
                                    <div class="d-flex flex-wrap gap-3 align-items-center">
                                        @if($enrollments->isNotEmpty())
                                            @php $firstEnrollment = $enrollments->first(); @endphp
                                            <a href="{{ route('student.courses.learn', $firstEnrollment->course->slug) }}" class="btn-hero-action">
                                                <i class="bi bi-play-circle-fill fs-5"></i>
                                                <span>Continue: {{ Str::limit($firstEnrollment->course->title, 24) }}</span>
                                            </a>
                                        @else
                                            <a href="{{ route('courses.index') }}" class="btn-hero-action">
                                                <i class="bi bi-compass-fill fs-5"></i>
                                                <span>Explore Courses</span>
                                            </a>
                                        @endif
                                        <a href="{{ route('leaderboard') }}" class="btn btn-outline-light rounded-3 px-3 py-2 fw-semibold" style="border-color: rgba(255,255,255,0.25); backdrop-filter: blur(6px);">
                                            <i class="bi bi-trophy me-1 text-warning"></i> Rank #{{ $stats['rank'] ?? '—' }}
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-4 text-center">
                                    <div class="profile-container">
                                        <div class="rotating-ring ring-1"></div>
                                        <div class="rotating-ring ring-2"></div>
                                        @php
                                            $dashboardAvatar = $user->publicAvatarUrl();
                                        @endphp
                                        <img src="{{ $dashboardAvatar }}" alt="{{ $user->name }}" class="profile-img" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=150&background=1f8fff&color=fff'" />
                                        <div class="status-indicator online" title="Online Active"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Modern Stats Grid -->
            <div class="row mb-4 mb-lg-5">
                <div class="col-12 col-xl-11 mx-auto">
                    <div class="stats-grid-premium">
                        <!-- Active Courses -->
                        <div class="stat-tile theme-blue">
                            <div class="stat-tile-icon">
                                <i class="bi bi-journal-bookmark-fill"></i>
                            </div>
                            <div class="stat-tile-info">
                                <span class="stat-tile-label">Active Courses</span>
                                <div class="stat-tile-val">{{ $stats['active_courses'] }}</div>
                            </div>
                        </div>

                        <!-- Completed Courses -->
                        <div class="stat-tile theme-emerald">
                            <div class="stat-tile-icon">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="stat-tile-info">
                                <span class="stat-tile-label">Completed</span>
                                <div class="stat-tile-val">{{ $stats['completed_courses'] }}</div>
                            </div>
                        </div>

                        <!-- Exams Passed -->
                        <div class="stat-tile theme-purple">
                            <div class="stat-tile-icon">
                                <i class="bi bi-clipboard2-check-fill"></i>
                            </div>
                            <div class="stat-tile-info">
                                <span class="stat-tile-label">Exams Passed</span>
                                <div class="stat-tile-val">{{ $stats['passed_exams'] }}</div>
                            </div>
                        </div>

                        <!-- Certificates -->
                        <div class="stat-tile theme-amber">
                            <div class="stat-tile-icon">
                                <i class="bi bi-award-fill"></i>
                            </div>
                            <div class="stat-tile-info">
                                <span class="stat-tile-label">Certificates</span>
                                <div class="stat-tile-val">{{ $stats['certificates'] }}</div>
                            </div>
                        </div>

                        <!-- Total XP -->
                        <div class="stat-tile theme-cyan">
                            <div class="stat-tile-icon">
                                <i class="bi bi-lightning-charge-fill"></i>
                            </div>
                            <div class="stat-tile-info">
                                <span class="stat-tile-label">Total XP</span>
                                <div class="stat-tile-val">{{ number_format($stats['total_xp']) }}</div>
                            </div>
                        </div>

                        <!-- Daily Streak -->
                        <div class="streak-card-premium">
                            <div>
                                <div class="streak-val">{{ $stats['current_streak'] }}</div>
                                <div class="streak-label">Day Streak</div>
                            </div>
                            <div class="streak-fire-anim">🔥</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Score & Level Progression Hub -->
            <div class="row mb-4 mb-lg-5">
                <div class="col-12 col-xl-11 mx-auto">
                    <div class="score-hub-card">
                        <div class="row g-0">
                            <div class="col-6 col-md-3 score-metric-col border-end border-bottom border-md-bottom-0">
                                <div class="score-metric-label">
                                    <i class="bi bi-coin text-primary"></i> Total Score
                                </div>
                                <div class="score-metric-num text-primary">{{ number_format($stats['total_score']) }}</div>
                            </div>
                            <div class="col-6 col-md-3 score-metric-col border-end border-bottom border-md-bottom-0">
                                <div class="score-metric-label">
                                    <i class="bi bi-arrow-up-circle-fill text-success"></i> Earned
                                </div>
                                <div class="score-metric-num text-success">+{{ number_format($stats['earned_points']) }}</div>
                            </div>
                            <div class="col-6 col-md-3 score-metric-col border-end">
                                <div class="score-metric-label">
                                    <i class="bi bi-arrow-down-circle-fill text-danger"></i> Deducted
                                </div>
                                <div class="score-metric-num text-danger">-{{ number_format($stats['deducted_points']) }}</div>
                            </div>
                            <div class="col-6 col-md-3 score-metric-col">
                                <div class="score-metric-label">
                                    <i class="bi bi-trophy-fill text-warning"></i> Global Rank
                                </div>
                                <div class="score-metric-num text-warning">#{{ $stats['rank'] ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="level-bar-container">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold small text-dark">
                                    <i class="bi bi-stars text-primary me-1"></i> Level {{ $level['level'] }} · {{ $level['title'] }}
                                </span>
                                <span class="small text-muted fw-semibold">{{ $level['progress'] }} / 100 XP to next level</span>
                            </div>
                            <div class="level-progress-bar">
                                <div class="level-progress-fill" style="width: {{ $level['progress'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Analytics & Charts Section -->
            <div class="row mb-4 mb-lg-5">
                <div class="col-12 col-xl-11 mx-auto">
                    <div class="row g-4">
                        <!-- Weekly Activity Chart -->
                        <div class="col-lg-8">
                            <div class="glass-card-premium h-100">
                                <div class="card-header-premium">
                                    <h5 class="card-title-premium">
                                        <i class="bi bi-activity text-primary"></i> Weekly Activity
                                    </h5>
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold">Last 7 Days</span>
                                </div>
                                <div class="p-4" style="height: 280px;">
                                    <canvas id="activityChart"
                                        data-labels='@json($weeklyProgress['labels'])'
                                        data-values='@json($weeklyProgress['data'])'></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Course Completion Doughnut -->
                        <div class="col-lg-4">
                            <div class="glass-card-premium h-100">
                                <div class="card-header-premium">
                                    <h5 class="card-title-premium">
                                        <i class="bi bi-pie-chart-fill text-primary"></i> Course Status
                                    </h5>
                                </div>
                                <div class="p-4 d-flex align-items-center justify-content-center" style="height: 280px;">
                                    <canvas id="courseChart"
                                        data-completed="{{ $courseStats['completed'] }}"
                                        data-inprogress="{{ $courseStats['in_progress'] }}"
                                        data-notstarted="{{ $courseStats['not_started'] }}"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Exam Results Summary (If Any) -->
            @if($examStats['total'] > 0)
            <div class="row mb-4 mb-lg-5">
                <div class="col-12 col-xl-11 mx-auto">
                    <div class="glass-card-premium p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                            <h5 class="card-title-premium mb-0">
                                <i class="bi bi-clipboard-check-fill text-primary"></i> Exam Performance
                            </h5>
                            <a href="{{ route('student.exams.index') }}" class="btn-header-link">View All Exams →</a>
                        </div>
                        <div class="row g-3 text-center mb-3">
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3" style="background: rgba(31, 143, 255, 0.06);">
                                    <div class="fs-4 fw-bold text-dark">{{ $examStats['total'] }}</div>
                                    <div class="small text-muted fw-medium">Total Attempts</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3" style="background: rgba(16, 185, 129, 0.08);">
                                    <div class="fs-4 fw-bold text-success">{{ $examStats['passed'] }}</div>
                                    <div class="small text-muted fw-medium">Passed</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3" style="background: rgba(244, 63, 94, 0.08);">
                                    <div class="fs-4 fw-bold text-danger">{{ $examStats['failed'] }}</div>
                                    <div class="small text-muted fw-medium">Failed</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3" style="background: rgba(139, 92, 246, 0.08);">
                                    <div class="fs-4 fw-bold text-primary">{{ round($examStats['avg_score']) }}%</div>
                                    <div class="small text-muted fw-medium">Average Score</div>
                                </div>
                            </div>
                        </div>
                        @php
                            $passRate = $examStats['total'] > 0 ? ($examStats['passed'] / $examStats['total']) * 100 : 0;
                        @endphp
                        <div class="d-flex justify-content-between align-items-center small mb-1 fw-semibold">
                            <span class="text-muted">Overall Pass Rate</span>
                            <span class="text-success">{{ round($passRate) }}%</span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 99px; background: #e2e8f0;">
                            <div class="progress-bar bg-success" style="width: {{ $passRate }}%; border-radius: 99px 0 0 99px;"></div>
                            <div class="progress-bar bg-danger" style="width: {{ 100 - $passRate }}%; border-radius: 0 99px 99px 0;"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- 6. My Courses Section -->
            <div class="row mb-4 mb-lg-5">
                <div class="col-12 col-xl-11 mx-auto">
                    <div class="glass-card-premium p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                            <h5 class="card-title-premium mb-0">
                                <i class="bi bi-book-half text-primary"></i> My Active Courses
                            </h5>
                            <a href="{{ route('student.courses') }}" class="btn-header-link">View All Courses →</a>
                        </div>

                        @if($enrollments->isEmpty())
                            <div class="text-center py-5">
                                <div class="mb-3 text-muted" style="font-size: 3rem; opacity: 0.4;">
                                    <i class="bi bi-journal-x"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">No Courses Enrolled Yet</h6>
                                <p class="text-muted small mb-3">Explore our wide range of digital skills and courses to start learning.</p>
                                <a href="{{ route('courses.index') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                                    Browse Available Courses
                                </a>
                            </div>
                        @else
                            @foreach($enrollments as $enrollment)
                            <div class="course-card-premium">
                                <div class="course-meta-box">
                                    <div class="course-icon-badge">
                                        <i class="bi bi-mortarboard-fill"></i>
                                    </div>
                                    <div>
                                        <div class="course-title-text">{{ $enrollment->course->title }}</div>
                                        <div class="course-tags-row">
                                            @if($enrollment->course->category)
                                            <span class="badge bg-primary bg-opacity-10 text-primary fw-medium">{{ $enrollment->course->category->name }}</span>
                                            @endif
                                            <span><i class="bi bi-clock me-1"></i>{{ $enrollment->course->duration_weeks ?? 'Self-paced' }}</span>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="course-progress-mini">
                                                    <div class="course-progress-mini-fill" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                                </div>
                                                <span class="fw-bold text-dark">{{ $enrollment->progress_percentage }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('courses.chat.show', $enrollment->course) }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3 py-2 fw-semibold" title="Course Discussion">
                                        <i class="bi bi-chat-dots me-1"></i> Chat
                                    </a>
                                    <a href="{{ route('student.courses.learn', $enrollment->course->slug) }}" class="btn-course-continue">
                                        <i class="bi bi-play-fill fs-5"></i> Continue
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- 7. Leaderboard & Certificates Split -->
            <div class="row mb-4 mb-lg-5">
                <div class="col-12 col-xl-11 mx-auto">
                    <div class="row g-4">
                        
                        <!-- Leaderboard Widget with Podium -->
                        <div class="col-lg-7">
                            <div class="glass-card-premium h-100">
                                <div class="card-header-premium">
                                    <h5 class="card-title-premium">
                                        <i class="bi bi-trophy-fill text-warning"></i> Community Leaderboard
                                    </h5>
                                    <a href="{{ route('leaderboard') }}" class="btn-header-link">Full Board →</a>
                                </div>

                                <!-- Podium for Top 3 -->
                                @if($leaderboard->count() >= 3)
                                <div class="podium-container">
                                    <!-- Rank 2: Silver -->
                                    @php $top2 = $leaderboard->get(1); @endphp
                                    <div class="podium-card rank-2">
                                        <div class="podium-avatar-wrap">
                                            <img src="{{ $top2->user->publicAvatarUrl() }}" class="podium-avatar" />
                                        </div>
                                        <div class="fw-bold small text-truncate">{{ $top2->user->name }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ number_format($top2->xp) }} XP</div>
                                        <div class="podium-rank-pill mt-2">2nd</div>
                                    </div>

                                    <!-- Rank 1: Gold -->
                                    @php $top1 = $leaderboard->get(0); @endphp
                                    <div class="podium-card rank-1">
                                        <div class="podium-avatar-wrap">
                                            <div class="podium-crown">👑</div>
                                            <img src="{{ $top1->user->publicAvatarUrl() }}" class="podium-avatar" />
                                        </div>
                                        <div class="fw-bold text-dark small text-truncate">{{ $top1->user->name }}</div>
                                        <div class="text-warning fw-semibold" style="font-size: 0.75rem;">{{ number_format($top1->xp) }} XP</div>
                                        <div class="podium-rank-pill mt-2">1st</div>
                                    </div>

                                    <!-- Rank 3: Bronze -->
                                    @php $top3 = $leaderboard->get(2); @endphp
                                    <div class="podium-card rank-3">
                                        <div class="podium-avatar-wrap">
                                            <img src="{{ $top3->user->publicAvatarUrl() }}" class="podium-avatar" />
                                        </div>
                                        <div class="fw-bold small text-truncate">{{ $top3->user->name }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ number_format($top3->xp) }} XP</div>
                                        <div class="podium-rank-pill mt-2">3rd</div>
                                    </div>
                                </div>
                                @endif

                                <!-- Leaderboard List (Rank 4+) -->
                                <div class="border-top">
                                    @forelse($leaderboard->skip(3)->take(4) as $index => $entry)
                                    @php 
                                        $realRank = $index + 4;
                                        $isMe = $entry->user_id === auth()->id();
                                    @endphp
                                    <div class="leaderboard-row-item {{ $isMe ? 'highlight-me' : '' }}">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="fw-bold text-muted small" style="width: 24px;">#{{ $realRank }}</span>
                                            <img src="{{ $entry->user->publicAvatarUrl() }}" class="rounded-circle" style="width: 34px; height: 34px; object-fit: cover;" />
                                            <div>
                                                <div class="fw-semibold small text-dark {{ $isMe ? 'text-primary' : '' }}">
                                                    {{ $entry->user->name }}
                                                    @if($isMe) <span class="badge bg-primary ms-1" style="font-size: 0.65rem;">You</span> @endif
                                                </div>
                                            </div>
                                        </div>
                                        <span class="fw-bold text-dark small">{{ number_format($entry->xp) }} XP</span>
                                    </div>
                                    @empty
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Certificates Showcase -->
                        <div class="col-lg-5" id="certificates">
                            <div class="glass-card-premium h-100">
                                <div class="card-header-premium">
                                    <h5 class="card-title-premium">
                                        <i class="bi bi-award-fill text-amber" style="color: #f59e0b;"></i> Earned Certificates
                                    </h5>
                                    <a href="{{ route('student.certificates') }}" class="btn-header-link">All ({{ $certificates->count() + $completedEnrollments->count() }}) →</a>
                                </div>
                                <div class="p-4">
                                    @php $totalCertsCount = $certificates->count() + $completedEnrollments->count(); @endphp
                                    @if($totalCertsCount === 0)
                                        <div class="text-center py-4 text-muted">
                                            <i class="bi bi-patch-question fs-1 d-block mb-2 opacity-25"></i>
                                            <p class="small mb-2">Complete a course to unlock your first verified certificate!</p>
                                            <a href="{{ route('student.courses') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Start Learning</a>
                                        </div>
                                    @else
                                        @foreach($certificates->take(3) as $cert)
                                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-2" style="background: rgba(31, 143, 255, 0.06); border-left: 3px solid #1f8fff;">
                                            <i class="bi bi-patch-check-fill text-primary fs-4 flex-shrink-0"></i>
                                            <div class="flex-grow-1 min-width-0">
                                                <div class="fw-semibold small text-truncate text-dark">{{ $cert->title }}</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">
                                                    Issued {{ $cert->issued_at ? $cert->issued_at->format('M d, Y') : $cert->created_at->format('M d, Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach

                                        @foreach($completedEnrollments->take(3 - $certificates->count()) as $enrollment)
                                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-2" style="background: rgba(245, 158, 11, 0.08); border-left: 3px solid #f59e0b;">
                                            <i class="bi bi-trophy-fill text-warning fs-4 flex-shrink-0"></i>
                                            <div class="flex-grow-1 min-width-0">
                                                <div class="fw-semibold small text-truncate text-dark">{{ $enrollment->course->title }}</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">Completed Course</div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- 8. Achievements & Upcoming Events -->
            <div class="row mb-4 mb-lg-5">
                <div class="col-12 col-xl-11 mx-auto">
                    <div class="row g-4">
                        
                        <!-- Achievements -->
                        <div class="col-lg-7">
                            <div class="glass-card-premium h-100">
                                <div class="card-header-premium">
                                    <h5 class="card-title-premium">
                                        <i class="bi bi-stars text-primary"></i> Badges & Achievements
                                    </h5>
                                </div>
                                <div class="p-4">
                                    <div class="row g-3">
                                        @forelse($achievements as $achievement)
                                        <div class="col-md-6">
                                            <div class="achievement-badge-card">
                                                <div class="achievement-icon-wrapper">
                                                    <i class="bi {{ $achievement->icon ?? 'bi-award' }}"></i>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-1">{{ $achievement->title }}</h6>
                                                <p class="small text-muted mb-2" style="font-size: 0.78rem;">{{ $achievement->description }}</p>
                                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill" style="font-size: 0.72rem;">
                                                    Unlocked {{ optional($achievement->pivot->earned_at ?? null)->format('M d, Y') ?? now()->format('M d, Y') }}
                                                </span>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="col-12 text-center text-muted py-4">
                                            <i class="bi bi-gem fs-2 d-block mb-2 opacity-25"></i>
                                            <p class="small mb-0">No achievements yet. Keep completing lessons to earn badges!</p>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Events -->
                        <div class="col-lg-5">
                            <div class="glass-card-premium h-100">
                                <div class="card-header-premium">
                                    <h5 class="card-title-premium">
                                        <i class="bi bi-calendar-event-fill text-primary"></i> Upcoming Live Events
                                    </h5>
                                    <a href="{{ route('events.index') }}" class="btn-header-link">All Events →</a>
                                </div>
                                <div class="p-4">
                                    @forelse($upcomingEvents as $event)
                                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-2" style="background: rgba(255, 255, 255, 0.7); border: 1px solid rgba(226, 232, 240, 0.8);">
                                        <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.25rem;">
                                            <i class="bi bi-calendar2-check"></i>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <h6 class="fw-bold text-dark small mb-1 text-truncate">{{ $event->title }}</h6>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                <i class="bi bi-clock me-1"></i>{{ $event->start_date->format('M d, Y \a\t g:i A') }}
                                            </div>
                                        </div>
                                        <span class="badge {{ $event->type === 'webinar' || $event->type === 'conference' ? 'bg-info bg-opacity-10 text-info' : 'bg-success bg-opacity-10 text-success' }} px-2 py-1">
                                            {{ ucfirst($event->type) }}
                                        </span>
                                    </div>
                                    @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-25"></i>
                                        <p class="small mb-0">No upcoming events scheduled right now.</p>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- 9. Notifications & Recent Activity -->
            <div class="row mb-4 mb-lg-5">
                <div class="col-12 col-xl-11 mx-auto">
                    <div class="row g-4">
                        
                        <!-- Notifications -->
                        <div class="col-lg-6">
                            <div class="glass-card-premium h-100">
                                <div class="card-header-premium">
                                    <h5 class="card-title-premium">
                                        <i class="bi bi-bell-fill text-primary"></i> Notifications
                                        @if($unreadNotifCount > 0)
                                        <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.7rem;">{{ $unreadNotifCount }}</span>
                                        @endif
                                    </h5>
                                    <div class="d-flex align-items-center gap-2">
                                        <button id="enableSoundBtn" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="initAudioContext()" style="font-size: 0.78rem;">
                                            <i class="bi bi-volume-mute"></i> Sound
                                        </button>
                                        <a href="{{ route('student.notifications.index') }}" class="btn-header-link">View All</a>
                                    </div>
                                </div>
                                <div class="p-4">
                                    @if($notifications->isEmpty())
                                        <div class="text-center py-4 text-muted">
                                            <i class="bi bi-bell-slash fs-2 d-block mb-2 opacity-25"></i>
                                            <p class="small mb-0">You're all caught up! No new notifications.</p>
                                        </div>
                                    @else
                                        <div class="notif-list">
                                            @foreach($notifications->take(4) as $notif)
                                            <div class="notif-card-item {{ !$notif->is_read ? 'is-unread' : '' }}"
                                                 data-notif-id="{{ $notif->id }}"
                                                 data-mark-url="{{ parse_url(route('student.notifications.read', $notif->id), PHP_URL_PATH) }}">
                                                <div class="notif-icon-box">
                                                    <i class="{{ $notif->icon }}"></i>
                                                </div>
                                                <div class="flex-grow-1 min-width-0">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <span class="fw-bold text-dark small">{{ $notif->title }}</span>
                                                        @if(!$notif->is_read)
                                                        <span class="badge bg-primary" style="font-size: 0.62rem;">New</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-muted small mb-1" style="font-size: 0.8rem; line-height: 1.4;">{{ $notif->message }}</div>
                                                    <div class="text-muted" style="font-size: 0.72rem;">
                                                        <i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        @if($unreadNotifCount > 0)
                                        <div class="text-center mt-3">
                                            <button class="btn btn-outline-primary btn-sm rounded-pill px-4 btn-mark-all-read" data-url="{{ parse_url(route('student.notifications.read.all'), PHP_URL_PATH) }}">
                                                <i class="bi bi-check-all me-1"></i> Mark All as Read
                                            </button>
                                        </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity & Scoring History -->
                        <div class="col-lg-6">
                            <div class="glass-card-premium h-100">
                                <div class="card-header-premium">
                                    <h5 class="card-title-premium">
                                        <i class="bi bi-clock-history text-primary"></i> Recent Activity
                                    </h5>
                                </div>
                                <div class="p-4">
                                    @php
                                        $validActivities = $activities->filter(fn($a) => $a->type !== 'test_activity' && !str_starts_with(strtolower($a->message), 'test'));
                                    @endphp
                                    @forelse($validActivities->take(5) as $activity)
                                    @php
                                        $isHex = str_starts_with($activity->color, '#');
                                        $bgStyle  = $isHex ? 'background:' . $activity->color . ';' : '';
                                        $bgClass  = $isHex ? '' : 'bg-' . $activity->color;
                                    @endphp
                                    <div class="d-flex align-items-start gap-3 pb-3 mb-3 border-bottom">
                                        <div class="{{ $bgClass }} text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; {{ $bgStyle }}">
                                            <i class="bi {{ $activity->icon ?? 'bi-circle-fill' }}" style="font-size: 0.9rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <p class="mb-1 text-dark small fw-medium" style="line-height: 1.35;">{{ $activity->message }}</p>
                                            <small class="text-muted" style="font-size: 0.72rem;">{{ $activity->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-hourglass-split fs-2 d-block mb-2 opacity-25"></i>
                                        <p class="small mb-0">No recent activity to display yet.</p>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- 10. Scoring History Table -->
            <div class="row mb-4">
                <div class="col-12 col-xl-11 mx-auto">
                    <div class="glass-card-premium">
                        <div class="card-header-premium">
                            <h5 class="card-title-premium">
                                <i class="bi bi-star-half text-warning"></i> Scoring Log & History
                            </h5>
                            <a href="{{ route('scoring.help') }}" class="btn-header-link">How Scoring Works →</a>
                        </div>
                        <div class="p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                    <thead class="table-light text-muted" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                        <tr>
                                            <th class="ps-4 py-3">Date</th>
                                            <th class="py-3">Points</th>
                                            <th class="py-3">Reason</th>
                                            <th class="pe-4 py-3">Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($scoreHistory->take(6) as $entry)
                                        <tr>
                                            <td class="ps-4 text-muted">{{ $entry->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <span class="badge {{ $entry->amount >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} px-2 py-1 fw-bold">
                                                    {{ $entry->amount >= 0 ? '+' : '' }}{{ number_format($entry->amount) }}
                                                </span>
                                            </td>
                                            <td class="fw-medium text-dark">{{ $entry->reason ?? '—' }}</td>
                                            <td class="pe-4"><span class="text-muted text-capitalize">{{ str_replace('_', ' ', $entry->type) }}</span></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No score history records yet.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Modals -->
<div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="notificationsModalLabel">Notifications</h5>
                <div class="ms-3 text-muted small" id="notifCountText">0 unread</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted small">Recent notifications</div>
                    <div>
                        <button id="btnMarkAllRead" class="btn btn-sm btn-outline-primary rounded-pill px-3">Mark all read</button>
                    </div>
                </div>
                <div id="notificationsList" class="list-group list-group-flush">
                    <!-- Notifications Injected via JS -->
                </div>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="courseProgressModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Course Progress</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="courseProgressContent"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="profileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Profile Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="profileForm">
                    <div class="text-center mb-4">
                        <img src="{{ auth()->user()->publicAvatarUrl() }}" alt="Profile" class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover" />
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3">Change Photo</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="fullName" class="form-label small fw-semibold">Full Name</label>
                        <input type="text" class="form-control rounded-3" id="fullName" value="{{ auth()->user()->name }}" />
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label small fw-semibold">Email</label>
                        <input type="email" class="form-control rounded-3" id="email" value="{{ auth()->user()->email }}" />
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label small fw-semibold">Phone</label>
                        <input type="tel" class="form-control rounded-3" id="phone" value="{{ auth()->user()->phone ?? '' }}" />
                    </div>
                    <div class="mb-3">
                        <label for="bio" class="form-label small fw-semibold">Bio</label>
                        <textarea class="form-control rounded-3" id="bio" rows="3">{{ auth()->user()->bio ?? '' }}</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="saveProfile()">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('assets/js/student-dashboard.js') }}"></script>
<script src="{{ asset('assets/js/student-dashboard-charts.js') }}"></script>
<script src="{{ asset('assets/js/modern-footer.js') }}"></script>

<script>
    // Show class ended alert
    function showClassEndedAlert(data) {
        const overlay = document.createElement('div');
        overlay.id = 'class-ended-overlay';
        overlay.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 10000; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px);';

        overlay.innerHTML = `
            <div style="background: white; border-radius: 20px; padding: 40px; text-align: center; max-width: 420px; box-shadow: 0 25px 50px rgba(0,0,0,0.3);">
                <div style="background: #fee2e2; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="bi bi-stop-circle" style="font-size: 40px; color: #dc2626;"></i>
                </div>
                <h3 style="color: #1f2937; font-weight: 700; margin-bottom: 10px;">Class Ended</h3>
                <p style="color: #6b7280; font-size: 0.95rem; margin-bottom: 20px;">Your class "${data.course_title}" has been ended by the teacher.</p>
                <p style="color: #9ca3af; font-size: 14px; margin-bottom: 20px;">Redirecting to dashboard in <span id="countdown" class="fw-bold text-dark">5</span> seconds...</p>
                <button onclick="window.location.href = '{{ route('student.dashboard') }}'" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold">
                    <i class="bi bi-house-door me-1"></i> Go to Dashboard Now
                </button>
            </div>
        `;

        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';

        let countdown = 5;
        const countdownEl = document.getElementById('countdown');
        const interval = setInterval(() => {
            countdown--;
            if (countdownEl) countdownEl.textContent = countdown;
            if (countdown <= 0) {
                clearInterval(interval);
                window.location.href = '{{ route('student.dashboard') }}';
            }
        }, 1000);

        if (window.parent !== window) {
            window.parent.postMessage('class-ended', '*');
        }
    }

    // Audio & Notifications
    let audioContext = null;
    let notificationSoundEnabled = false;

    function initAudioContext() {
        if (!audioContext) {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioContext.state === 'suspended') {
            audioContext.resume();
        }
        notificationSoundEnabled = true;

        const btn = document.getElementById('enableSoundBtn');
        if (btn) {
            btn.innerHTML = '<i class="bi bi-volume-up"></i> Enabled';
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-success');
            setTimeout(() => btn.style.display = 'none', 2000);
        }

        playNotificationSound();
    }

    function playNotificationSound() {
        if (!notificationSoundEnabled) return;
        try {
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.setValueAtTime(880, audioContext.currentTime);
            oscillator.frequency.setValueAtTime(1100, audioContext.currentTime + 0.1);
            oscillator.frequency.setValueAtTime(880, audioContext.currentTime + 0.2);

            gainNode.gain.setValueAtTime(0.5, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.4);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.4);
        } catch (e) {
            console.log('Audio play failed:', e);
        }
    }

    document.addEventListener('click', initAudioContext, { once: true });
    document.addEventListener('touchstart', initAudioContext, { once: true });
    document.addEventListener('keydown', initAudioContext, { once: true });

    function showBrowserNotification(message, title) {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Edvora - Class Started!', {
                body: message,
                icon: '{{ asset('assets/images/logo1.jpg') }}',
                tag: 'class-started'
            });
        }
    }

    function addNotificationToList(data) {
        const notifList = document.querySelector('.notif-list');
        if (notifList) {
            const newNotif = document.createElement('div');
            newNotif.className = 'notif-card-item is-unread';
            newNotif.innerHTML = `
                <div class="notif-icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-camera-video-fill"></i>
                </div>
                <div class="flex-grow-1 min-width-0">
                    <div class="fw-bold text-dark small">${data.course_title}</div>
                    <div class="text-muted small mb-1">${data.message}</div>
                    <span class="text-muted" style="font-size:0.72rem;">Just now</span>
                </div>
                <a href="${data.room_url}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3">Join</a>
            `;
            notifList.insertBefore(newNotif, notifList.firstChild);
        }
    }

    function showToastNotification(data) {
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 360px;';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.style.cssText = 'background: #fff; border-left: 4px solid #22c55e; border-radius: 12px; padding: 15px; margin-bottom: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);';
        toast.innerHTML = `
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="background: #22c55e; color: white; border-radius: 50%; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="bi bi-camera-video-fill"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 700; color: #1f2937; margin-bottom: 2px;">${data.course_title}</div>
                    <p style="font-size: 13px; color: #6b7280; margin: 0 0 8px 0;">${data.message}</p>
                    <a href="${data.room_url}" target="_blank" style="display: inline-block; background: #1f8fff; color: white; padding: 5px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">
                        Join Class →
                    </a>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 0;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        `;
        toastContainer.appendChild(toast);
        setTimeout(() => toast.remove(), 10000);
    }

    // Polling setup
    let lastCheck = new Date().toISOString();
    const processedNotifIds = new Set();

    function checkNewNotifications() {
        fetch('/student/notifications/check-new?since=' + encodeURIComponent(lastCheck), {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.notifications && data.notifications.length > 0) {
                data.notifications.forEach(notif => {
                    if (processedNotifIds.has(notif.id)) return;
                    processedNotifIds.add(notif.id);

                    playNotificationSound();
                    const notifData = {
                        course_title: notif.course_title,
                        message: notif.message,
                        room_url: notif.data?.room_url || notif.data?.meet_link || '#',
                        started_at: notif.created_at
                    };
                    showToastNotification(notifData);
                    showBrowserNotification(notif.message, notif.course_title);
                    addNotificationToList(notifData);
                });
            }
            lastCheck = new Date().toISOString();
        })
        .catch(err => console.log('Polling error:', err));
    }

    let wasInClass = false;
    let activeCourseSlug = null;

    function checkActiveClass() {
        fetch('/student/active-class/check', {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.has_active_class) {
                wasInClass = true;
                activeCourseSlug = data.course_slug;
            } else if (wasInClass && !data.has_active_class) {
                showClassEndedAlert({
                    course_title: data.course_title || 'Your class',
                    message: 'The class has been ended by the teacher.'
                });
                wasInClass = false;
                activeCourseSlug = null;
            }
        })
        .catch(err => console.log('Active class check error:', err));
    }

    setInterval(checkNewNotifications, 10000);
    setInterval(checkActiveClass, 8000);
    checkNewNotifications();
    checkActiveClass();
</script>
@endpush
