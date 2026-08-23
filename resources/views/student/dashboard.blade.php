@extends('layouts.app')

@section('title', 'Student Dashboard - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/student-dashboard.css') }}" rel="stylesheet" />
@endpush

@section('content')
<!-- Header -->
  

  <div class="dashboard-wrapper">
    <!-- Sidebar -->
    <x-student-sidebar />

    <!-- Main Dashboard Content -->
    <main class="main-content" id="mainContent">
      <div class="container-fluid py-5">
        <!-- Welcome / Hero -->
        <div class="row mb-5">
          <div class="col-lg-10 col-12 mx-auto">
            <div class="card card_ hero-card border-0 shadow-sm">
              <div class="card-body p-4">
                <div class="row align-items-center">
                  <div class="col-md-8">
                    <h2 class="fw-bold mb-2">Welcome back, {{ $user->name }}! 👋</h2>
                    <p class="mb-3 muted-small">
                      Ready to continue your learning journey? You have {{ $enrollments->count() }}
                      active course(s) and {{ $upcomingEvents->count() }} upcoming event(s).
                    </p>
                    <button class="btn btn-outline-light btn-continue">
                      <i class="bi bi-play-circle me-2"></i>Continue Learning
                    </button>
                  </div>
                  <div class="col-md-4 text-center">
                    <div class="position-relative">
                      @php
                        $dashboardAvatar = null;
                        if ($user->avatar) {
                            if (str_starts_with($user->avatar, 'http')) {
                                $dashboardAvatar = $user->avatar;
                            } elseif (str_starts_with($user->avatar, 'storage/') || str_starts_with($user->avatar, '/storage/')) {
                                $dashboardAvatar = asset(ltrim($user->avatar, '/'));
                            } else {
                                $dashboardAvatar = asset('storage/' . $user->avatar);
                            }
                        } else {
                            $dashboardAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=150&background=1f8fff&color=fff';
                        }
                      @endphp
                      <img
                        src="{{ $dashboardAvatar }}"
                        alt="{{ $user->name }}" class="rounded-circle border border-3 border-white"
                        style="width: 120px; height: 120px; object-fit: cover"
                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=150&background=1f8fff&color=fff'" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Stats Cards Row -->
        <div class="row mb-4">
          <div class="col-lg-10 col-12 mx-auto">
            <div class="stats-row">
              <!-- Active Courses -->
              <div class="stat-card">
                <div class="stat-content">
                  <div class="stat-icon-wrapper">
                    <div class="stat-icon">
                      <i class="bi bi-book"></i>
                    </div>
                  </div>
                  <div class="stat-info">
                    <span class="stat-label">Active Courses</span>
                    <div class="stat-value">{{ $stats['active_courses'] }}</div>
                  </div>
                </div>
              </div>

              <!-- Completed Courses -->
              <div class="stat-card">
                <div class="stat-content">
                  <div class="stat-icon-wrapper">
                    <div class="stat-icon">
                      <i class="bi bi-check-circle"></i>
                    </div>
                  </div>
                  <div class="stat-info">
                    <span class="stat-label">Completed</span>
                    <div class="stat-value">{{ $stats['completed_courses'] }}</div>
                  </div>
                </div>
              </div>

              <!-- Exams Passed -->
              <div class="stat-card">
                <div class="stat-content">
                  <div class="stat-icon-wrapper">
                    <div class="stat-icon">
                      <i class="bi bi-clipboard-check"></i>
                    </div>
                  </div>
                  <div class="stat-info">
                    <span class="stat-label">Exams Passed</span>
                    <div class="stat-value">{{ $stats['passed_exams'] }}</div>
                  </div>
                </div>
              </div>

              <!-- Certificates -->
              <div class="stat-card">
                <div class="stat-content">
                  <div class="stat-icon-wrapper">
                    <div class="stat-icon">
                      <i class="bi bi-award"></i>
                    </div>
                  </div>
                  <div class="stat-info">
                    <span class="stat-label">Certificates</span>
                    <div class="stat-value">{{ $stats['certificates'] }}</div>
                  </div>
                </div>
              </div>

              <!-- XP Points -->
              <div class="stat-card">
                <div class="stat-content">
                  <div class="stat-icon-wrapper">
                    <div class="stat-icon">
                      <i class="bi bi-lightning"></i>
                    </div>
                  </div>
                  <div class="stat-info">
                    <span class="stat-label">Total XP</span>
                    <div class="stat-value">{{ number_format($stats['total_xp']) }}</div>
                  </div>
                </div>
              </div>

              <!-- Current Streak -->
              <div class="streak-card">
                <div class="d-flex align-items-center justify-content-between">
                  <div>
                    <div class="streak-number">{{ $stats['current_streak'] }}</div>
                    <div class="small opacity-75">Day Streak</div>
                  </div>
                  <div class="streak-flame">🔥</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Score Overview -->
        @php $level = $user->level(); @endphp
        <div class="row mb-4">
          <div class="col-lg-10 col-12 mx-auto">
            <div class="card border-0 shadow-sm overflow-hidden">
              <div class="card-body p-0">
                <div class="row g-0">
                  <div class="col-md-3 p-4 text-center border-end">
                    <div class="text-muted small mb-1">Total Score</div>
                    <div class="display-6 fw-bold text-primary">{{ number_format($stats['total_score']) }}</div>
                  </div>
                  <div class="col-md-3 p-4 text-center border-end">
                    <div class="text-muted small mb-1">Earned</div>
                    <div class="display-6 fw-bold text-success">+{{ number_format($stats['earned_points']) }}</div>
                  </div>
                  <div class="col-md-3 p-4 text-center border-end">
                    <div class="text-muted small mb-1">Deducted</div>
                    <div class="display-6 fw-bold text-danger">-{{ number_format($stats['deducted_points']) }}</div>
                  </div>
                  <div class="col-md-3 p-4 text-center">
                    <div class="text-muted small mb-1">Rank</div>
                    <div class="display-6 fw-bold text-warning">#{{ $stats['rank'] ?? '—' }}</div>
                    <small class="text-muted">out of {{ \App\Models\User::where('role', 'student')->count() }} students</small>
                  </div>
                </div>
                <div class="p-3 border-top bg-light">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="fw-semibold">Level {{ $level['level'] }} · {{ $level['title'] }}</small>
                    <small class="text-muted">{{ $level['progress'] }} / 100 to next level</small>
                  </div>
                  <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $level['progress'] }}%"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
          <div class="col-lg-10 col-12 mx-auto">
            <div class="row g-4">
              <!-- Weekly Activity Chart -->
              <div class="col-md-8">
                <div class="chart-card">
                  <div class="chart-header">
                    <h5 class="chart-title">
                      <i class="bi bi-graph-up me-2 text-primary"></i>Weekly Activity
                    </h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary">Last 7 Days</span>
                  </div>
                  <div class="chart-container">
                    <canvas id="activityChart"
                      data-labels='@json($weeklyProgress['labels'])'
                      data-values='@json($weeklyProgress['data'])'></canvas>
                  </div>
                </div>
              </div>

              <!-- Course Completion Doughnut -->
              <div class="col-md-4">
                <div class="chart-card">
                  <div class="chart-header">
                    <h5 class="chart-title">
                      <i class="bi bi-pie-chart me-2 text-primary"></i>Course Status
                    </h5>
                  </div>
                  <div class="chart-container">
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

        <!-- Exam Results Summary -->
        @if($examStats['total'] > 0)
        <div class="row mb-4">
          <div class="col-lg-10 col-12 mx-auto">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 primary-blue-text">
                  <i class="bi bi-clipboard-check me-2 text-primary"></i>Exam Performance
                </h5>
                <a href="{{ route('student.exams.index') }}" class="btn btn-sm btn-outline-primary">View All Exams</a>
              </div>
              <div class="card-body">
                <div class="exam-stat-row">
                  <div class="exam-stat-item">
                    <div class="exam-stat-value">{{ $examStats['total'] }}</div>
                    <div class="small text-muted">Total Attempts</div>
                  </div>
                  <div class="exam-stat-item">
                    <div class="exam-stat-value text-success">{{ $examStats['passed'] }}</div>
                    <div class="small text-muted">Passed</div>
                  </div>
                  <div class="exam-stat-item">
                    <div class="exam-stat-value text-danger">{{ $examStats['failed'] }}</div>
                    <div class="small text-muted">Failed</div>
                  </div>
                  <div class="exam-stat-item">
                    <div class="exam-stat-value text-primary">{{ round($examStats['avg_score']) }}%</div>
                    <div class="small text-muted">Avg Score</div>
                  </div>
                </div>
                <!-- Progress Bar -->
                <div class="mt-4">
                  <div class="d-flex justify-content-between mb-2">
                    <small class="text-muted">Pass Rate</small>
                    <small class="text-success fw-bold">
                      {{ $examStats['total'] > 0 ? round(($examStats['passed'] / $examStats['total']) * 100) : 0 }}%
                    </small>
                  </div>
                  <div class="progress" style="height: 8px;">
                    @php
                      $passRate = $examStats['total'] > 0 ? ($examStats['passed'] / $examStats['total']) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $passRate }}%"></div>
                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ 100 - $passRate }}%"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif

        <!-- Certificates Summary Card -->
        <div class="row mb-4" id="certificates">
          <div class="col-lg-10 col-12 mx-auto">
            <div class="card border-0 shadow-sm" style="border-radius:16px;overflow:hidden;">
              <div class="card-body p-0">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 p-4"
                     style="background:linear-gradient(135deg,#0d0d0d 5%,#1f8fff 100%);">
                  <div class="d-flex align-items-center gap-3">
                    <div style="width:54px;height:54px;background:rgba(255,255,255,.15);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.8rem;color:#fcd34d;">
                      <i class="bi bi-award-fill"></i>
                    </div>
                    <div>
                      <h5 class="fw-bold text-white mb-0">My Certificates</h5>
                      <div class="text-white-50 small mt-1">
                        @php $totalCertsCount = $certificates->count() + $completedEnrollments->count(); @endphp
                        {{ $totalCertsCount }} certificate{{ $totalCertsCount !== 1 ? 's' : '' }} earned
                      </div>
                    </div>
                  </div>
                  <a href="{{ route('student.certificates') }}"
                     style="background:rgba(255,255,255,.18);color:#fff;border:1.5px solid rgba(255,255,255,.3);border-radius:10px;padding:.5rem 1.2rem;font-size:.85rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;backdrop-filter:blur(6px);transition:all .2s;"
                     onmouseover="this.style.background='rgba(255,255,255,.28)'"
                     onmouseout="this.style.background='rgba(255,255,255,.18)'">
                    <i class="bi bi-arrow-right-circle"></i>View All Certificates
                  </a>
                </div>

                @if($totalCertsCount === 0)
                <div class="text-center py-4 text-muted">
                  <i class="bi bi-award fs-2 d-block mb-2 opacity-25"></i>
                  <small>Complete a course to earn your first certificate!</small>
                </div>
                @else
                <div class="p-3">
                  @foreach($certificates->take(3) as $cert)
                  <div class="d-flex align-items-center gap-3 p-2 rounded-3 mb-1"
                       style="background:#f8fbff;border-left:3px solid #1f8fff;">
                    <i class="bi bi-patch-check-fill text-primary fs-5 flex-shrink-0"></i>
                    <div class="flex-grow-1 min-width-0">
                      <div class="fw-semibold small text-truncate">{{ $cert->title }}</div>
                      <div class="text-muted" style="font-size:.72rem;">
                        {{ $cert->issued_at ? $cert->issued_at->format('M d, Y') : $cert->created_at->format('M d, Y') }}
                      </div>
                    </div>
                  </div>
                  @endforeach

                  @foreach($completedEnrollments->take(3 - $certificates->count()) as $enrollment)
                  <div class="d-flex align-items-center gap-3 p-2 rounded-3 mb-1"
                       style="background:#fffbeb;border-left:3px solid #f59e0b;">
                    <i class="bi bi-trophy-fill text-warning fs-5 flex-shrink-0"></i>
                    <div class="flex-grow-1 min-width-0">
                      <div class="fw-semibold small text-truncate">{{ $enrollment->course->title }}</div>
                      <div class="text-muted" style="font-size:.72rem;">Completed</div>
                    </div>
                  </div>
                  @endforeach

                  @if($totalCertsCount > 3)
                  <div class="text-center pt-2">
                    <a href="{{ route('student.certificates') }}" class="text-primary small fw-semibold">
                      +{{ $totalCertsCount - 3 }} more → View all
                    </a>
                  </div>
                  @endif
                </div>
                @endif
              </div>
            </div>
          </div>
        </div>

        <!-- Notifications -->
        <div class="row mb-4">
          <div class="col-lg-10 col-12 mx-auto">
            <div class="section-header">
              <h3 class="section-title">
                <i class="bi bi-bell"></i>
                Notifications
                @if($unreadNotifCount > 0)
                  <span class="notif-unread-badge">{{ $unreadNotifCount }}</span>
                @endif
              </h3>
              <div class="d-flex align-items-center gap-2">
                <button id="enableSoundBtn" class="btn btn-sm btn-outline-primary" onclick="initAudioContext()" style="font-size: 12px; padding: 4px 8px;">
                  <i class="bi bi-volume-mute"></i> Enable Sound
                </button>
                <a href="{{ route('student.notifications.index') }}" class="btn-view-all">
                  View All
                </a>
              </div>
            </div>

            @if($notifications->isEmpty())
              <div class="empty-state">
                <i class="bi bi-bell-slash"></i>
                <div class="empty-state-title">No Notifications</div>
                <p>You're all caught up! No new notifications.</p>
              </div>
            @else
              <div class="notif-list">
                @foreach($notifications as $notif)
                <div class="notif-item {{ !$notif->is_read ? 'unread' : '' }}"
                     data-notif-id="{{ $notif->id }}"
                     data-mark-url="{{ parse_url(route('student.notifications.read', $notif->id), PHP_URL_PATH) }}">
                  <div class="notif-icon-wrapper">
                    <i class="{{ $notif->icon }}"></i>
                  </div>
                  <div class="notif-content">
                    <div class="notif-title">
                      {{ $notif->title }}
                      @if(!$notif->is_read)
                        <span class="notif-new-badge">New</span>
                      @endif
                    </div>
                    <div class="notif-message">{{ $notif->message }}</div>
                    <div class="notif-meta">
                      <span><i class="bi bi-tag me-1"></i>{{ $notif->type_label }}</span>
                      <span><i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}</span>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
              @if($unreadNotifCount > 0)
              <div class="text-center mt-3">
                <button class="btn-mark-all-read"
                        data-url="{{ parse_url(route('student.notifications.read.all'), PHP_URL_PATH) }}">
                  <i class="bi bi-check-all me-1"></i>Mark All as Read
                </button>
              </div>
              @endif
            @endif
          </div>
        </div>

        <!-- My Courses -->
        <div class="row mb-4">
          <div class="col-lg-10 col-12 mx-auto">
            <div class="section-header">
              <h3 class="section-title">
                <i class="bi bi-book"></i>
                My Courses
              </h3>
              <a href="{{ route('student.courses') }}" class="btn-view-all">
                View All Courses
              </a>
            </div>

            @if($enrollments->isEmpty())
              <div class="empty-state">
                <i class="bi bi-journal-x"></i>
                <div class="empty-state-title">No Courses Yet</div>
                <p>You haven't enrolled in any courses yet.</p>
                <a href="{{ route('courses.index') }}" class="empty-state-link">
                  Browse Available Courses →
                </a>
              </div>
            @else
              @foreach($enrollments as $enrollment)
              <div class="course-item">
                <div class="course-header">
                  <div class="course-icon">
                    <i class="bi bi-mortarboard-fill"></i>
                  </div>
                  <div class="course-info">
                    <h6 class="course-title">{{ $enrollment->course->title }}</h6>
                    <div class="course-progress-wrapper">
                      <div class="course-progress-bar">
                        <div class="course-progress-fill {{ $enrollment->progress_percentage >= 80 ? 'high' : ($enrollment->progress_percentage >= 40 ? '' : 'medium') }}"
                             style="width: {{ $enrollment->progress_percentage }}%"></div>
                      </div>
                    </div>
                    <div class="course-meta">
                      <span><i class="bi bi-bar-chart-fill me-1"></i>{{ $enrollment->progress_percentage }}% Complete</span>
                      @if($enrollment->course->category)
                      <span><i class="bi bi-tag-fill me-1"></i>{{ $enrollment->course->category->name }}</span>
                      @endif
                    </div>
                  </div>
                  <div class="course-actions">
                    <a href="{{ route('student.courses.learn', $enrollment->course->slug) }}" class="btn-continue">
                      <i class="bi bi-play-circle me-1"></i>Continue
                    </a>
                  </div>
                </div>
              </div>
              @endforeach
            @endif
          </div>
        </div>

        <!-- Upcoming Events & Competitions -->
        <div class="row g-4 mb-4 justify-content-center">
          <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-header bg-white border-0 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                  <h4 class="fw-bold mb-0 primary-blue-text">
                    Upcoming Events
                  </h4>
                  <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-primary white-hover">
                    View All
                  </a>
                </div>
              </div>
              <div class="card-body">
                @forelse($upcomingEvents as $event)
                <div class="d-flex align-items-center p-2 border-bottom">
                  <div class="flex-shrink-0 me-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                      <i class="bi bi-calendar-event"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="mb-1">{{ $event->title }}</h6>
                    <p class="small text-muted mb-1">
                      <i class="bi bi-clock me-1"></i>{{ $event->start_date->format('M d, Y \a\t g:i A') }}
                    </p>
                    <span class="badge {{ $event->type === 'webinar' || $event->type === 'conference' ? 'bg-info' : 'bg-success' }}">
                      {{ ucfirst($event->type) }}
                    </span>
                  </div>
                </div>
                @empty
                <p class="text-muted text-center py-3">No upcoming events.</p>
                @endforelse
              </div>
            </div>
          </div>

          <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-header bg-white border-0 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                  <h4 class="fw-bold mb-0 primary-blue-text">
                    Active Competitions
                  </h4>
                  <a href="{{ route('competitions.index') }}" class="btn btn-sm btn-outline-primary white-hover">
                    View All
                  </a>
                </div>
              </div>
              <div class="card-body">
                @forelse($activeCompetitions as $comp)
                <div class="d-flex align-items-center p-2 border-bottom">
                  <div class="flex-shrink-0 me-3">
                    <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                      <i class="bi bi-trophy"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="mb-1">{{ $comp->title }}</h6>
                    <p class="small text-muted mb-1">
                      <i class="bi bi-people me-1"></i>{{ number_format($comp->participants_count) }} participants
                    </p>
                    <p class="small text-muted mb-2">Deadline: {{ $comp->end_date->format('M d, Y') }}</p>
                    <a href="{{ route('competitions.detail', $comp->slug) }}" class="btn btn-outline-warning btn-sm">View</a>
                  </div>
                </div>
                @empty
                <p class="text-muted text-center py-3">No active competitions.</p>
                @endforelse
              </div>
            </div>
          </div>
        </div>

        <!-- Leaderboard -->
        <div class="row mb-4">
          <div class="col-lg-10 col-12 mx-auto">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 primary-blue-text">
                  <i class="bi bi-trophy me-2 text-warning"></i>
                  Leaderboard
                </h5>

                <a href="{{ route('leaderboard') }}" class="btn btn-sm btn-outline-primary">
                  View Full
                </a>
              </div>

              <div class="list-group list-group-flush">
                @php
                  $userRank = null;
                  foreach ($leaderboard as $i => $entry) {
                    if ($entry->user_id === auth()->id()) {
                      $userRank = $i + 1;
                      break;
                    }
                  }
                @endphp

                @if($userRank)
                <div class="user-rank-banner">
                  <i class="bi bi-trophy-fill"></i>
                  <span>Your Rank: <strong>#{{ $userRank }}</strong></span>
                </div>
                @endif

                @forelse($leaderboard as $i => $entry)
                @php
                  $badgeClass = $i === 0 ? 'bg-warning text-dark' : ($i === 1 ? 'bg-secondary' : ($i === 2 ? 'bg-danger' : 'bg-light text-dark'));
                  $badgeLabel = $i === 0 ? 'Gold' : ($i === 1 ? 'Silver' : ($i === 2 ? 'Bronze' : '#' . ($i+1)));
                  $isMe = $entry->user_id === auth()->id();
                @endphp
                @php
                  $entryAvatar = null;
                  if ($entry->user->avatar) {
                      if (str_starts_with($entry->user->avatar, 'http')) {
                          $entryAvatar = $entry->user->avatar;
                      } elseif (str_starts_with($entry->user->avatar, 'storage/') || str_starts_with($entry->user->avatar, '/storage/')) {
                          $entryAvatar = asset(ltrim($entry->user->avatar, '/'));
                      } else {
                          $entryAvatar = asset('storage/' . $entry->user->avatar);
                      }
                  } else {
                      $entryAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($entry->user->name) . '&size=35&background=1f8fff&color=fff';
                  }
                @endphp
                <div class="list-group-item d-flex align-items-center justify-content-between py-3 leaderboard-item {{ $isMe ? 'leaderboard-me' : '' }}">
                  <div class="d-flex align-items-center">
                    <span class="fw-bold me-3 rank-number {{ $i === 0 ? 'text-warning' : ($i === 1 ? 'text-secondary' : ($i === 2 ? 'text-danger' : '')) }}">#{{ $i+1 }}</span>
                    <div class="position-relative">
                      <img src="{{ $entryAvatar }}"
                        class="rounded-circle me-3 {{ $isMe ? 'leaderboard-avatar' : '' }}" style="width:35px;height:35px;object-fit:cover;"
                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($entry->user->name) }}&size=35&background=1f8fff&color=fff'" />
                      @if($isMe)
                      <span class="you-badge">You</span>
                      @endif
                    </div>
                    <div>
                      <div class="fw-semibold {{ $isMe ? 'text-primary' : '' }}">{{ $entry->user->name }}</div>
                      <small class="text-muted">{{ number_format($entry->xp) }} XP</small>
                    </div>
                  </div>
                  <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                </div>
                @empty
                <div class="list-group-item text-center text-muted py-3">No leaderboard data.</div>
                @endforelse
              </div>
            </div>
          </div>
        </div>

        <!-- Achievements -->
        <div class="row g-4 mb-4">
          <div class="col-lg-10 col-12 mx-auto">
            <div class="card border-0 shadow-sm p-2">
              <div class="card-header bg-white border-0">
                <h4 class="fw-bold mb-0 primary-blue-text">
                  My Achievements
                </h4>
              </div>
              <div class="card-body">
                <div class="row g-3">
                @forelse($achievements as $achievement)
                <div class="col-md-4">
                  <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                      <div class="mb-3">
                        <i class="bi {{ $achievement->icon ?? 'bi-award' }} fs-1 text-{{ $achievement->color ?? 'primary' }}"></i>
                      </div>
                      <h6 class="fw-bold">{{ $achievement->title }}</h6>
                      <p class="small text-muted mb-2">{{ $achievement->description }}</p>
                      <span class="badge bg-{{ $achievement->color ?? 'primary' }}">
                        Earned {{ optional($achievement->pivot->earned_at ?? null)->format('Y-m-d') ?? now()->format('Y-m-d') }}
                      </span>
                    </div>
                  </div>
                </div>
                @empty
                <div class="col-12 text-center text-muted py-3">No achievements yet. Keep learning!</div>
                @endforelse
              </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Scoring History -->
        <div class="row mb-4">
          <div class="col-lg-10 col-12 mx-auto">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                <h5 class="fw-bold mb-0 primary-blue-text">
                  <i class="bi bi-star-half me-2 text-warning"></i>Scoring History
                </h5>
                <a href="{{ route('leaderboard') }}" class="btn btn-sm btn-outline-primary">View Leaderboard</a>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Reason</th>
                        <th>Type</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($scoreHistory as $entry)
                      <tr>
                        <td>{{ $entry->created_at->format('M d, Y H:i') }}</td>
                        <td>
                          <span class="badge {{ $entry->amount >= 0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $entry->amount >= 0 ? '+' : '' }}{{ number_format($entry->amount) }}
                          </span>
                        </td>
                        <td>{{ $entry->reason ?? '—' }}</td>
                        <td><span class="text-muted text-capitalize">{{ str_replace('_', ' ', $entry->type) }}</span></td>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No scoring history yet.</td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
          <div class="col-lg-10 col-12 mx-auto">
            <div class="card border-0 shadow-sm p-2">
              <div class="card-header bg-white border-0">
                <h5 class="fw-bold mb-0 primary-blue-text">
                  Recent Activity
                </h5>
              </div>
              <div class="card-body">
@php
                  $validActivities = $activities->filter(fn($a) => $a->type !== 'test_activity' && !str_starts_with(strtolower($a->message), 'test'));
                @endphp
                @forelse($validActivities as $activity)
                @php
                  $isHex = str_starts_with($activity->color, '#');
                  $bgStyle  = $isHex ? 'background:' . $activity->color . ';' : '';
                  $bgClass  = $isHex ? '' : 'bg-' . $activity->color;
                @endphp
                <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                  <div class="flex-shrink-0 me-3">
                    <div class="{{ $bgClass }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;{{ $bgStyle }}">
                      <i class="bi {{ $activity->icon ?? 'bi-circle' }}"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <p class="mb-1">{{ $activity->message }}</p>
                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                  </div>
                </div>
                @empty
                <p class="text-muted text-center py-3">No recent activity.</p>
                @endforelse
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Modals-->
  <!-- Notifications modal -->
  <div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="notificationsModalLabel">
            Notifications
          </h5>
          <div class="ms-3 small-muted" id="notifCountText">0 unread</div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="small-muted">Recent notifications</div>
            <div>
              <button id="btnMarkAllRead" class="btn btn-sm btn-outline-primary">
                Mark all read
              </button>
            </div>
          </div>

          <div id="notificationsList" class="list-group">
            <!-- each notification injected here -->
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="courseProgressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Course Progress</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="courseProgressContent"></div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="profileModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Profile Settings</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="profileForm">
            <div class="text-center mb-4">
              <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&size=100&background=1f8fff&color=fff' }}"
                alt="Profile" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover" />
              <div class="mt-2">
                <button type="button" class="btn btn-sm btn-outline-primary">
                  Change Photo
                </button>
              </div>
            </div>
            <div class="mb-3">
              <label for="fullName" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="fullName" value="{{ auth()->user()->name }}" />
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" value="{{ auth()->user()->email }}" />
            </div>
            <div class="mb-3">
              <label for="phone" class="form-label">Phone</label>
              <input type="tel" class="form-control" id="phone" value="{{ auth()->user()->phone ?? '' }}" />
            </div>
            <div class="mb-3">
              <label for="bio" class="form-label">Bio</label>
              <textarea class="form-control" id="bio" rows="3">{{ auth()->user()->bio ?? '' }}</textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancel
          </button>
          <button type="button" class="btn btn-primary" onclick="saveProfile()">
            Save Changes
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modern Footer -->
  

  <!-- Bootstrap 5 JS -->
  
  <!-- Custom JS -->
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('assets/js/student-dashboard.js') }}"></script>
<script src="{{ asset('assets/js/student-dashboard-charts.js') }}"></script>
<script src="{{ asset('assets/js/modern-footer.js') }}"></script>

<script>
    // Show class ended alert
    function showClassEndedAlert(data) {
        // Create modal overlay
        const overlay = document.createElement('div');
        overlay.id = 'class-ended-overlay';
        overlay.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 10000; display: flex; align-items: center; justify-content: center;';

        overlay.innerHTML = `
            <div style="background: white; border-radius: 16px; padding: 40px; text-align: center; max-width: 400px; animation: slideIn 0.3s ease;">
                <div style="background: #fee2e2; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="bi bi-stop-circle" style="font-size: 40px; color: #dc2626;"></i>
                </div>
                <h3 style="color: #1f2937; margin-bottom: 10px;">Class Ended</h3>
                <p style="color: #6b7280; margin-bottom: 20px;">Your class "${data.course_title}" has been ended by the teacher.</p>
                <p style="color: #9ca3af; font-size: 14px; margin-bottom: 20px;">You will be redirected to dashboard in <span id="countdown">5</span> seconds...</p>
                <button onclick="window.location.href = '{{ route('student.dashboard') }}'" style="background: #3b82f6; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 16px; cursor: pointer;">
                    <i class="bi bi-house-door"></i> Go to Dashboard Now
                </button>
            </div>
        `;

        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';

        // Countdown and redirect
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

        // Also close MiroTalk window if in iframe
        if (window.parent !== window) {
            window.parent.postMessage('class-ended', '*');
        }
    }

    // Play notification sound
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

        // Update button
        const btn = document.getElementById('enableSoundBtn');
        if (btn) {
            btn.innerHTML = '<i class="bi bi-volume-up"></i> Sound Enabled';
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-success');
            setTimeout(() => btn.style.display = 'none', 2000);
        }

        // Test sound
        playNotificationSound();
    }

    function playNotificationSound() {
        if (!notificationSoundEnabled) {
            console.log('Sound not enabled - click on page first');
            return;
        }

        try {
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            // Two-tone beep pattern
            oscillator.frequency.setValueAtTime(880, audioContext.currentTime); // A5
            oscillator.frequency.setValueAtTime(1100, audioContext.currentTime + 0.1);
            oscillator.frequency.setValueAtTime(880, audioContext.currentTime + 0.2);

            gainNode.gain.setValueAtTime(0.5, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.4);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.4);

            // Second beep
            const oscillator2 = audioContext.createOscillator();
            const gainNode2 = audioContext.createGain();
            oscillator2.connect(gainNode2);
            gainNode2.connect(audioContext.destination);

            oscillator2.frequency.setValueAtTime(880, audioContext.currentTime + 0.5);
            gainNode2.gain.setValueAtTime(0.5, audioContext.currentTime + 0.5);
            gainNode2.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.9);

            oscillator2.start(audioContext.currentTime + 0.5);
            oscillator2.stop(audioContext.currentTime + 0.9);

        } catch (e) {
            console.log('Audio play failed:', e);
        }
    }

    // Initialize audio on first user interaction
    document.addEventListener('click', initAudioContext, { once: true });
    document.addEventListener('touchstart', initAudioContext, { once: true });
    document.addEventListener('keydown', initAudioContext, { once: true });

    // Browser notification
    function showBrowserNotification(message, title) {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Edvora - Class Started!', {
                body: message,
                icon: '{{ asset('assets/images/logo.png') }}',
                tag: 'class-started'
            });
        }
    }

    // Add notification to list
    function addNotificationToList(data) {
        const notifList = document.querySelector('.notif-list');
        if (notifList) {
            const newNotif = document.createElement('div');
            newNotif.className = 'notif-item notif-unread';
            newNotif.innerHTML = `
                <div class="notif-icon-wrapper bg-success">
                    <i class="bi bi-camera-video-fill"></i>
                </div>
                <div class="notif-content">
                    <div class="notif-title">${data.course_title}</div>
                    <p class="notif-text">${data.message}</p>
                    <span class="notif-time">Just now</span>
                </div>
                <a href="${data.room_url}" target="_blank" class="btn btn-sm btn-primary">
                    <i class="bi bi-box-arrow-up-right"></i> Join
                </a>
            `;
            notifList.insertBefore(newNotif, notifList.firstChild);
        }
    }

    // Show toast notification
    function showToastNotification(data) {
        // Create toast container if not exists
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 350px;';
            document.body.appendChild(toastContainer);
        }

        // Create toast
        const toast = document.createElement('div');
        toast.style.cssText = 'background: #fff; border-left: 4px solid #22c55e; border-radius: 8px; padding: 15px; margin-bottom: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.15); animation: slideIn 0.3s ease;';
        toast.innerHTML = `
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="background: #22c55e; color: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="bi bi-camera-video-fill"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: #1f2937; margin-bottom: 4px;">${data.course_title}</div>
                    <p style="font-size: 13px; color: #6b7280; margin: 0 0 8px 0;">${data.message}</p>
                    <a href="${data.room_url}" target="_blank" style="display: inline-block; background: #3b82f6; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px;">
                        Join Class <i class="bi bi-box-arrow-up-right" style="margin-left: 4px;"></i>
                    </a>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 0;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        `;

        toastContainer.appendChild(toast);

        // Auto remove after 10 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 10000);
    }

    // Update badge count
    function updateNotificationBadge() {
        const badge = document.querySelector('.notif-unread-badge');
        if (badge) {
            const currentCount = parseInt(badge.textContent) || 0;
            badge.textContent = currentCount + 1;
        } else {
            const title = document.querySelector('.section-title');
            if (title) {
                const newBadge = document.createElement('span');
                newBadge.className = 'notif-unread-badge';
                newBadge.textContent = '1';
                title.appendChild(newBadge);
            }
        }
    }

    // Request notification permission on load
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }

    // Fallback: Polling for new notifications every 10 seconds
    let lastCheck = new Date().toISOString();
    const processedNotifIds = new Set(); // Track already processed notification IDs

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
                let newNotifCount = 0;
                data.notifications.forEach(notif => {
                    // Skip if already processed
                    if (processedNotifIds.has(notif.id)) {
                        return;
                    }
                    processedNotifIds.add(notif.id);
                    newNotifCount++;

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
                if (newNotifCount > 0) {
                    updateNotificationBadge();
                }
            }
            lastCheck = new Date().toISOString();
        })
        .catch(err => console.log('Polling error:', err));
    }

    // Check for active class session (to detect when teacher ends it)
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
                // Class was active but now ended
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

    // Start polling every 10 seconds
    setInterval(checkNewNotifications, 10000);
    setInterval(checkActiveClass, 8000); // Check active class more frequently

    // Initial check
    checkNewNotifications();
    checkActiveClass();
</script>
@endpush
