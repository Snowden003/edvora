@extends('layouts.app')

@section('title', 'Teacher Dashboard - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
<!-- Header -->
    <!-- Header -->
    

    <!-- Dashboard Layout Wrapper -->
    <div class="dashboard-wrapper">
        <!-- Sidebar Navigation -->
        <x-teacher-sidebar />

        <!-- Main Dashboard Content -->
        <main class="main-content">
            <div class="container-fluid py-5">
                <!-- Welcome Section -->
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="welcome-banner-premium">
                            <!-- Banner Background Animation -->
                            <div class="banner-bg-animation">
                                <div class="floating-icon icon-1"><i class="bi bi-mortarboard"></i></div>
                                <div class="floating-icon icon-2"><i class="bi bi-book"></i></div>
                                <div class="floating-icon icon-3"><i class="bi bi-pencil-square"></i></div>
                                <div class="floating-icon icon-4"><i class="bi bi-award"></i></div>
                                <div class="banner-particle p-1"></div>
                                <div class="banner-particle p-2"></div>
                                <div class="banner-particle p-3"></div>
                            </div>

                            <div class="card-body p-0">
                                <div class="glass-inner-panel">
                                    <div class="row align-items-center g-4">
                                        <div class="col-lg-8 welcome-content">
                                            <h1 class="display-5 fw-bold mb-3 text-white main-title">
                                                Welcome back, <span class="highlight-text">{{ $user->name }}!</span> 👩‍🏫
                                            </h1>
                                            <p class="mb-4 fs-5 text-white-50 description-text">
                                                @if($activeCourses->count() > 0 || $totalStudents > 0)
                                                    Your learning community is thriving. You have <span
                                                        class="fw-bold text-white">{{ $activeCourses->count() }}
                                                        active course{{ $activeCourses->count() !== 1 ? 's' : '' }}</span> with <span class="fw-bold text-white">{{ $totalStudents }}
                                                        student{{ $totalStudents !== 1 ? 's' : '' }}</span> enrolled.
                                                @else
                                                    Welcome! Your courses and students will appear here once you get started.
                                                @endif
                                            </p>

                                        </div>
                                        <div class="col-lg-4 text-center">
                                            <div class="profile-container">
                                                <div class="rotating-ring ring-1"></div>
                                                <div class="rotating-ring ring-2"></div>
                                                <div class="rotating-ring ring-3"></div>
                                                @php
                                                    if ($user->avatar) {
                                                        if (str_starts_with($user->avatar, 'http')) {
                                                            $heroAvatar = $user->avatar;
                                                        } elseif (str_starts_with($user->avatar, '/storage/') || str_starts_with($user->avatar, 'storage/')) {
                                                            $heroAvatar = asset(ltrim($user->avatar, '/'));
                                                        } else {
                                                            $heroAvatar = asset('storage/' . $user->avatar);
                                                        }
                                                    } else {
                                                        $heroAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=1F8FFF&color=fff&size=128';
                                                    }
                                                @endphp
                                                <img src="{{ $heroAvatar }}"
                                                     class="profile-img shadow-2xl" style="object-fit: cover;">
                                                <div class="status-indicator online"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row g-5 mb-5 mt-2">
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-lg glass-card h-100 stat-hover-premium">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="stat-icon-premium accent-blue">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-muted fw-medium mb-0 small">Total Students</p>
                                        <h2 class="fw-bold text-premium mb-0">{{ $totalStudents }}</h2>
                                    </div>
                                </div>
                                <div class="stat-badge success">
                                    <i class="bi bi-people-fill me-1"></i> Total
                                    <span class="ms-1 text-muted opacity-50 small">enrolled</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-lg glass-card h-100 stat-hover-premium">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="stat-icon-premium accent-cyan">
                                        <i class="bi bi-book-fill"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-muted fw-medium mb-0 small">Active Courses</p>
                                        <h2 class="fw-bold text-premium mb-0">{{ $activeCourses->count() }}</h2>
                                    </div>
                                </div>
                                <div class="stat-badge info">
                                    <i class="bi bi-journal-bookmark me-1"></i> {{ $courses->count() }} Total
                                    <span class="ms-1 text-muted opacity-50 small">courses</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-lg glass-card h-100 stat-hover-premium">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="stat-icon-premium accent-yellow">
                                        <i class="bi bi-star-fill"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-muted fw-medium mb-0 small">Avg. Rating</p>
                                        <h2 class="fw-bold text-premium mb-0">{{ number_format($avgRating, 1) }}</h2>
                                    </div>
                                </div>
                                <div class="stat-badge warning">
                                    <i class="bi bi-star-fill me-1"></i> Avg
                                    <span class="ms-1 text-muted opacity-50 small">rating</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row g-5 mb-5">
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-lg glass-card-premium h-100">
                            <div class="card-header border-0 pt-4 px-4">
                                <h5 class="fw-bold mb-1 text-premium">Course Status</h5>
                                <p class="text-muted small mb-0">Breakdown of your courses by status</p>
                            </div>
                            <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                                <div style="width:100%; max-width:260px;">
                                    <canvas id="courseStatusChart"></canvas>
                                </div>
                                <div class="d-flex flex-wrap justify-content-center gap-3 mt-4" id="statusLegend"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-lg glass-card-premium h-100">
                            <div class="card-header border-0 pt-4 px-4">
                                <h5 class="fw-bold mb-1 text-premium">Students per Course</h5>
                                <p class="text-muted small mb-0">Enrollment & rating overview</p>
                            </div>
                            <div class="card-body p-4">
                                <canvas id="courseProgressChart" style="max-height:260px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Courses & Assignments -->
                <div class="row g-5 mb-5">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-lg glass-card-premium h-100">
                            <div class="card-header border-0 pt-4 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="fw-bold mb-0 text-premium">My Courses</h5>
                                        <p class="text-muted small mb-0">Manage your active educational content</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0 mt-3">
                                <div class="table-responsive">
                                    <table class="table table-premium mb-0">
                                        <thead>
                                            <tr>
                                                <th>Course Name</th>
                                                <th>Enrolled</th>
                                                <th>Progression</th>
                                                <th>Avg. Rating</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="myCoursesTable">
                                            @forelse($courses as $course)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <h6 class="mb-1">{{ $course->title }}</h6>
                                                        <span class="badge {{ $course->status === 'active' ? 'bg-success' : ($course->status === 'draft' ? 'bg-warning text-dark' : 'bg-secondary') }}">{{ ucfirst($course->status) }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $course->enrolled_count ?? 0 }} students</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress me-2" style="width: 60px; height: 8px;">
                                                            <div class="progress-bar" style="width: {{ $course->enrolled_count > 0 ? 100 : 0 }}%; background: linear-gradient(90deg, #1F8FFF, #1F8FFF);"></div>
                                                        </div>
                                                        <small>{{ $course->enrolled_count > 0 ? '100' : '0' }}%</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-1">{{ number_format($course->rating ?? 0, 1) }}</span>
                                                        <div class="text-warning">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                @if($i <= floor($course->rating ?? 0))
                                                                    <i class="bi bi-star-fill"></i>
                                                                @elseif($i - ($course->rating ?? 0) < 1 && $i - ($course->rating ?? 0) > 0)
                                                                    <i class="bi bi-star-half"></i>
                                                                @else
                                                                    <i class="bi bi-star"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="actions-wrapper">
                                                        <button class="btn-action-premium btn-action-view" data-tooltip="View Course">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <button class="btn-action-premium btn-action-edit" data-tooltip="Edit Course">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                                                    No courses assigned yet.
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-lg glass-card-premium h-100">
                            <div class="card-header border-0 pt-4 px-4">
                                <h5 class="fw-bold mb-0 text-premium">Pending Reviews</h5>
                                <p class="text-muted small mb-0">Assignments waiting for your feedback</p>
                            </div>
                            <div class="card-body p-4" id="pendingReviews">
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-clipboard-check fs-3 d-block mb-2"></i>
                                    <p class="mb-0">No pending reviews at the moment.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Classes Schedule -->
                <div class="row g-5 mb-5">
                    <div class="col-12">
                        <div class="card border-0 shadow-lg glass-card-premium">
                            <div class="card-header border-0 pt-4 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="fw-bold mb-0 text-premium">Today's Schedule</h5>
                                        <p class="text-muted small mb-0">Your upcoming live sessions</p>
                                    </div>
                                    <button class="btn-premium-outline btn-premium-sm rounded-pill px-4"
                                        data-bs-toggle="modal" data-bs-target="#scheduleClassModal">
                                        <i class="bi bi-clock-history me-1"></i> Schedule Class
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-4 mt-2">
                                <div class="row g-4" id="todaySchedule">
                                    <div class="col-12 text-center text-muted py-4">
                                        <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                                        <p class="mb-0">No classes scheduled for today.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="card border-0 shadow-lg glass-card-premium">
                            <div class="card-header border-0 pt-4 px-4">
                                <h5 class="fw-bold mb-0 text-premium">Recent Activity</h5>
                                <p class="text-muted small mb-0">Track engagement and system events</p>
                            </div>
                            <div class="card-body p-4" id="recentActivity">
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-clock-history fs-3 d-block mb-2"></i>
                                    <p class="mb-0">No recent activity to display.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Schedule Class Modal -->
            <div class="modal fade" id="scheduleClassModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Schedule Live Class</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="scheduleClassForm">
                                <div class="mb-3">
                                    <label for="classTitle" class="form-label">Class Title</label>
                                    <input type="text" class="form-control" id="classTitle" required>
                                </div>
                                <div class="mb-3">
                                    <label for="classCourse" class="form-label">Course</label>
                                    <select class="form-select" id="classCourse" required>
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="classDate" class="form-label">Date</label>
                                        <input type="date" class="form-control" id="classDate" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="classTime" class="form-label">Time</label>
                                        <input type="time" class="form-control" id="classTime" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="classDuration" class="form-label">Duration (minutes)</label>
                                    <select class="form-select" id="classDuration" required>
                                        <option value="30">30 minutes</option>
                                        <option value="60" selected>1 hour</option>
                                        <option value="90">1.5 hours</option>
                                        <option value="120">2 hours</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="classDescription" class="form-label">Description</label>
                                    <textarea class="form-control" id="classDescription" rows="3"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-premium-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn-premium-primary" onclick="scheduleClass()">Schedule
                                Class</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Modal -->
            <div class="modal fade" id="notificationsModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Notifications</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="notificationsContent">
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                                <p class="mb-0">No notifications yet.</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-premium-outline" onclick="markAllRead()">Mark All
                                Read</button>
                            <button type="button" class="btn-premium-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Modern Footer -->
    


    <!-- Bootstrap 5 JS -->
    
    <!-- Custom JS -->
@endsection

@push('scripts')
<script>
    window.teacherDashboardData = {
        courseStatusActive: {{ $courses->whereIn('status', ['active', 'published'])->count() }},
        courseStatusDraft: {{ $courses->where('status', 'draft')->count() }},
        courseStatusCompleted: {{ $courses->where('status', 'completed')->count() }},
        courseStatusArchived: {{ $courses->where('status', 'archived')->count() }},
        totalCourses: {{ $courses->count() }},
        courseLabels: {!! json_encode($courses->pluck('title')->map(fn($t) => strlen($t) > 20 ? substr($t, 0, 20).'...' : $t)->values()) !!},
        courseStudents: {!! json_encode($courses->pluck('enrolled_count')->map(fn($v) => (int)($v ?? 0))->values()) !!},
        courseRatings: {!! json_encode($courses->pluck('rating')->map(fn($v) => round((float)($v ?? 0), 1))->values()) !!}
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const d = window.teacherDashboardData;

    // ── 1. Doughnut – Course Status ──────────────────────────────────
    const statusLabels  = ['Published / Active', 'Draft', 'Completed', 'Archived'];
    const statusValues  = [d.courseStatusActive, d.courseStatusDraft, d.courseStatusCompleted, d.courseStatusArchived];
    const statusColors  = ['#1F8FFF', '#FBBF24', '#10B981', '#94A3B8'];
    const statusIcons   = ['bi-broadcast', 'bi-pencil-square', 'bi-check-circle', 'bi-archive'];

    const hasAny = statusValues.some(v => v > 0);

    const ctxStatus = document.getElementById('courseStatusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: hasAny ? statusValues : [1],
                backgroundColor: hasAny ? statusColors : ['#e2e8f0'],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            cutout: '72%',
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: hasAny,
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed} course${ctx.parsed !== 1 ? 's' : ''}`
                    }
                }
            }
        }
    });

    // Custom legend
    const legend = document.getElementById('statusLegend');
    if (hasAny) {
        statusLabels.forEach((lbl, i) => {
            if (statusValues[i] > 0) {
                legend.innerHTML += `
                    <div class="d-flex align-items-center gap-1 small">
                        <span style="width:10px;height:10px;border-radius:50%;background:${statusColors[i]};display:inline-block;"></span>
                        <span class="text-muted">${lbl}</span>
                        <strong>${statusValues[i]}</strong>
                    </div>`;
            }
        });
    } else {
        legend.innerHTML = '<span class="text-muted small">No courses yet</span>';
    }

    // ── 2. Bar – Students per Course ─────────────────────────────────
    const ctxBar = document.getElementById('courseProgressChart').getContext('2d');

    if (d.courseLabels && d.courseLabels.length > 0) {
        const gradient = ctxBar.createLinearGradient(0, 0, 0, 260);
        gradient.addColorStop(0,   'rgba(31,143,255,0.85)');
        gradient.addColorStop(1,   'rgba(99,102,241,0.55)');

        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: d.courseLabels,
                datasets: [
                    {
                        label: 'Students Enrolled',
                        data: d.courseStudents,
                        backgroundColor: gradient,
                        borderRadius: 10,
                        borderSkipped: false,
                        yAxisID: 'yStudents'
                    },
                    {
                        label: 'Rating',
                        data: d.courseRatings,
                        type: 'line',
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16,185,129,0.12)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#10B981',
                        pointRadius: 5,
                        tension: 0.4,
                        yAxisID: 'yRating',
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, font: { size: 12 }, color: '#64748b' }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.dataset.label === 'Rating'
                                ? ` Rating: ${ctx.parsed.y} / 5`
                                : ` ${ctx.parsed.y} student${ctx.parsed.y !== 1 ? 's' : ''}`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 11 } }
                    },
                    yStudents: {
                        position: 'left',
                        beginAtZero: true,
                        grid: { color: 'rgba(148,163,184,0.15)' },
                        ticks: { color: '#94a3b8', precision: 0 },
                        title: { display: true, text: 'Students', color: '#94a3b8', font: { size: 11 } }
                    },
                    yRating: {
                        position: 'right',
                        min: 0, max: 5,
                        grid: { display: false },
                        ticks: { color: '#10B981', stepSize: 1 },
                        title: { display: true, text: 'Rating', color: '#10B981', font: { size: 11 } }
                    }
                }
            }
        });
    } else {
        ctxBar.canvas.parentNode.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="bi bi-bar-chart-line fs-2 d-block mb-2"></i>
                <p class="mb-0 small">No course data to display yet.</p>
            </div>`;
    }
});
</script>
<script src="{{ asset('assets/js/teacher-dashboard.js') }}"></script>
<script src="{{ asset('assets/js/modern-footer.js') }}"></script>
<script src="{{ asset('assets/js/pages/teacher-dashboard.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
