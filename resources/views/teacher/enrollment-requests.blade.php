@extends('layouts.app')

@section('title', 'Enrolled Students & Points - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/enrollment-requests.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <div class="dashboard-wrapper">
        <x-teacher-sidebar />

        <main class="main-content">
            <div class="container-fluid py-4 py-lg-5 px-3 px-lg-4">
                
                <!-- Page Header -->
                <div class="enr-page-header">
                    <div class="enr-header-left">
                        <div class="enr-header-icon-wrap">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div>
                            <h1 class="enr-page-title">Enrolled Students</h1>
                            <p class="enr-page-subtitle">
                                Monitor academic performance, manage gamification points, and view complete student profiles.
                            </p>
                        </div>
                    </div>
                    <div class="enr-header-right">
                        <div class="enr-count-badge">
                            <i class="bi bi-people-fill"></i>
                            <span>{{ $enrolledTotalCount }} {{ \Illuminate\Support\Str::plural('Student', $enrolledTotalCount) }}</span>
                        </div>
                    </div>
                </div>

                <!-- 4 Premium Stat Metric Cards -->
                <div class="enr-stats-grid">
                    <div class="enr-stat-card">
                        <div class="enr-stat-icon blue">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div class="enr-stat-info">
                            <div class="enr-stat-value">{{ $enrolledTotalCount }}</div>
                            <div class="enr-stat-label">Enrolled</div>
                        </div>
                    </div>
                    <div class="enr-stat-card">
                        <div class="enr-stat-icon green">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div class="enr-stat-info">
                            <div class="enr-stat-value">{{ $activeEnrolledCount }}</div>
                            <div class="enr-stat-label">Active</div>
                        </div>
                    </div>
                    <div class="enr-stat-card">
                        <div class="enr-stat-icon cyan">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <div class="enr-stat-info">
                            <div class="enr-stat-value">{{ $completedCount }}</div>
                            <div class="enr-stat-label">Completed</div>
                        </div>
                    </div>
                    <div class="enr-stat-card">
                        <div class="enr-stat-icon gold">
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <div class="enr-stat-info">
                            <div class="enr-stat-value">{{ number_format($totalPointsAwarded ?? 0) }}</div>
                            <div class="enr-stat-label">Total XP</div>
                        </div>
                    </div>
                </div>

                <!-- Filter Toolbar -->
                <div class="enr-filter-bar">
                    <form method="GET" action="{{ route('teacher.enrollment-requests') }}" id="filterForm">
                        <input type="hidden" name="tab" value="enrolled">
                        <div class="enr-filter-row">
                            <div class="enr-filter-search">
                                <i class="bi bi-search"></i>
                                <input type="text"
                                       name="search"
                                       placeholder="Search by student name or email..."
                                       value="{{ request('search') }}">
                            </div>
                            <div class="enr-filter-select-group">
                                <select name="course_id" class="enr-filter-select">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $c)
                                    <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->title }}
                                    </option>
                                    @endforeach
                                </select>
                                <select name="status" class="enr-filter-select">
                                    <option value="">All Statuses</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                                </select>
                            </div>
                            <div class="enr-filter-actions">
                                <button type="submit" class="enr-btn-filter">
                                    <i class="bi bi-funnel-fill"></i> Filter
                                </button>
                                <a href="{{ route('teacher.enrollment-requests') }}" class="enr-btn-reset" title="Reset Filters">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Enrolled Students Cards List -->
                <div class="enr-students-list">
                    @forelse($enrollments as $en)
                    @php
                        $studentUser = $en->user;
                        $courseItem  = $en->course;
                        $prog = (int)($en->progress_percentage ?? 0);
                        $progColor = ($en->status === 'banned') ? '#ef4444' : ($prog >= 80 ? '#10b981' : ($prog >= 40 ? '#f59e0b' : '#1F8FFF'));
                        $avatarUrl = $studentUser?->avatar
                            ? (str_starts_with($studentUser->avatar, 'http') ? $studentUser->avatar : asset('storage/' . $studentUser->avatar))
                            : 'https://ui-avatars.com/api/?name=' . urlencode($studentUser?->name ?? 'Student') . '&size=80&background=1F8FFF&color=fff';
                        $studentScore = $studentUser ? $studentUser->totalScore() : 0;
                    @endphp
                    <div class="enr-student-card {{ $en->status === 'banned' ? 'is-banned' : '' }}" id="enrollment-{{ $en->id }}">
                        <!-- Top Section: Student Identity -->
                        <div class="enr-card-top">
                            <div class="enr-card-identity">
                                <div class="enr-avatar-wrap">
                                    <img src="{{ $avatarUrl }}"
                                         alt="{{ $studentUser?->name ?? 'Student' }}"
                                         class="enr-avatar-img">
                                    <span class="enr-status-indicator {{ $en->status }}"></span>
                                </div>
                                <div class="enr-identity-info">
                                    <h6 class="enr-student-name">{{ $studentUser?->name ?? 'Unknown Student' }}</h6>
                                    <span class="enr-student-email">{{ $studentUser?->email ?? 'No email provided' }}</span>
                                </div>
                            </div>
                            <div class="enr-card-badges">
                                @if($en->status === 'active')
                                    <span class="enr-status-badge active"><i class="bi bi-circle-fill"></i> Active</span>
                                @elseif($en->status === 'completed')
                                    <span class="enr-status-badge completed"><i class="bi bi-check-circle-fill"></i> Completed</span>
                                @elseif($en->status === 'banned')
                                    <span class="enr-status-badge banned"><i class="bi bi-slash-circle-fill"></i> Banned</span>
                                @else
                                    <span class="enr-status-badge default">{{ ucfirst($en->status) }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Middle Section: Course + Progress + Score -->
                        <div class="enr-card-middle">
                            <div class="enr-course-section">
                                <div class="enr-course-chip">
                                    <i class="bi bi-journal-bookmark-fill"></i>
                                    <span>{{ $courseItem?->title ?? 'Course' }}</span>
                                </div>
                                <div class="enr-enrolled-date">
                                    <i class="bi bi-calendar3"></i>
                                    Enrolled {{ $en->created_at ? $en->created_at->format('M d, Y') : 'N/A' }}
                                </div>
                            </div>
                            <div class="enr-progress-section">
                                <div class="enr-progress-header">
                                    <span class="enr-progress-label">Progress</span>
                                    <span class="enr-progress-percent" style="color: {{ $progColor }};">{{ $prog }}%</span>
                                </div>
                                <div class="enr-progress-track">
                                    <div class="enr-progress-fill" style="width: {{ $prog }}%; background: {{ $progColor }};"></div>
                                </div>
                            </div>
                            <div class="enr-score-section">
                                <div class="enr-xp-display">
                                    <i class="bi bi-star-fill"></i>
                                    <span class="enr-xp-value student-score-display" data-user-id="{{ $studentUser?->id }}" id="score-badge-{{ $studentUser?->id }}">{{ $studentScore }}</span>
                                    <span class="enr-xp-label">XP</span>
                                </div>
                                @if($studentUser && $en->status !== 'banned')
                                <div class="enr-xp-controls">
                                    <button type="button"
                                            class="enr-xp-btn add"
                                            onclick="quickAdjustPoints({{ $studentUser->id }}, '{{ addslashes($studentUser->name) }}', 5, {{ $en->course_id }})"
                                            title="Award +5 XP">+5</button>
                                    <button type="button"
                                            class="enr-xp-btn deduct"
                                            onclick="quickAdjustPoints({{ $studentUser->id }}, '{{ addslashes($studentUser->name) }}', -5, {{ $en->course_id }})"
                                            title="Deduct -5 XP">-5</button>
                                    <button type="button"
                                            class="enr-xp-btn edit"
                                            onclick="openPointsModal({{ $studentUser->id }}, '{{ addslashes($studentUser->name) }}', {{ $studentScore }}, {{ $en->course_id }})"
                                            title="Custom Score">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Bottom Section: Action Buttons -->
                        <div class="enr-card-actions">
                            @if($studentUser)
                            <button type="button"
                                    class="enr-action-btn profile"
                                    onclick="viewStudentProfile({{ $studentUser->id }})"
                                    title="View Full Profile">
                                <i class="bi bi-person-badge-fill"></i>
                                <span>Profile</span>
                            </button>
                            <button type="button"
                                    class="enr-action-btn points"
                                    onclick="openPointsModal({{ $studentUser->id }}, '{{ addslashes($studentUser->name) }}', {{ $studentScore }}, {{ $en->course_id }})"
                                    title="Adjust Points">
                                <i class="bi bi-star-fill"></i>
                                <span>Points</span>
                            </button>
                            @endif

                            <a href="{{ route('teacher.courses.detail', $en->course_id) }}"
                               class="enr-action-btn course"
                               title="View Course Details">
                                <i class="bi bi-journal-bookmark-fill"></i>
                                <span>Course Detail</span>
                            </a>

                            @if($en->status === 'banned')
                            <form method="POST" action="{{ route('teacher.courses.students.unban', [$en->course_id, $en->user_id]) }}" class="d-inline">
                                @csrf
                                <button type="submit"
                                        class="enr-action-btn reinstate"
                                        onclick="return confirm('Reinstate access for {{ addslashes($studentUser?->name ?? 'Student') }}?')"
                                        title="Reinstate Student">
                                    <i class="bi bi-person-check-fill"></i>
                                    <span>Reinstate</span>
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('teacher.courses.students.ban', [$en->course_id, $en->user_id]) }}" class="d-inline">
                                @csrf
                                <button type="submit"
                                        class="enr-action-btn ban"
                                        onclick="return confirm('Are you sure you want to ban {{ addslashes($studentUser?->name ?? 'Student') }} from this course?')">
                                    <i class="bi bi-slash-circle-fill"></i>
                                    <span>Ban</span>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="enr-empty-state">
                        <div class="enr-empty-icon">
                            <i class="bi bi-mortarboard"></i>
                        </div>
                        <h5>No Enrolled Students Found</h5>
                        <p>No learners match your search criteria. Try clearing the filters or check back when students join your courses.</p>
                    </div>
                    @endforelse

                    <!-- Pagination -->
                    @if($enrollments->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $enrollments->links() }}
                    </div>
                    @endif
                </div>

            </div>

            <!-- POPUP 1: Comprehensive Student Profile Modal (English) -->
            <div class="modal fade modal-student-profile" id="studentProfileModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-body p-0" id="studentProfileContent">
                            <div class="text-center py-5 my-4">
                                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                                <h6 class="fw-bold text-dark mt-3 mb-1">Loading Student Profile</h6>
                                <p class="text-muted small">Fetching comprehensive academic and personal records...</p>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top py-2 px-4">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- POPUP 2: Quick Adjust Points Modal (Redesigned & English) -->
            <div class="modal fade" id="quickPointsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content enr-points-modal">
                        <div class="enr-pm-header">
                            <div class="enr-pm-header-content">
                                <div class="enr-pm-icon">
                                    <i class="bi bi-award-fill"></i>
                                </div>
                                <div>
                                    <h5 class="enr-pm-title">Adjust Student Score</h5>
                                    <p class="enr-pm-subtitle">
                                        Student: <span class="enr-pm-student-tag" id="quickPointsStudentName"></span>
                                    </p>
                                </div>
                            </div>
                            <button type="button" class="enr-pm-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>

                        <div class="enr-pm-body">
                            <input type="hidden" id="quickPointsUserId" value="">
                            
                            <!-- Live Score Preview -->
                            <div class="enr-pm-score-preview">
                                <div class="enr-pm-score-box">
                                    <div class="enr-pm-score-lbl">Current</div>
                                    <div class="enr-pm-score-num" id="quickPointsCurrentScore">0</div>
                                    <div class="enr-pm-score-unit">XP</div>
                                </div>
                                <div class="enr-pm-score-arrow">
                                    <span class="enr-pm-delta" id="quickPreviewDelta">+10</span>
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                                <div class="enr-pm-score-box projected">
                                    <div class="enr-pm-score-lbl">Projected</div>
                                    <div class="enr-pm-score-num" id="quickPreviewNewScore">10</div>
                                    <div class="enr-pm-score-unit">XP</div>
                                </div>
                            </div>

                            <!-- Mode Toggle -->
                            <div class="enr-pm-mode-toggle">
                                <button type="button" class="enr-pm-mode active add" id="quickModeAdd" onclick="setQuickPointsMode('add')">
                                    <i class="bi bi-plus-circle-fill"></i> Award
                                </button>
                                <button type="button" class="enr-pm-mode" id="quickModeDeduct" onclick="setQuickPointsMode('deduct')">
                                    <i class="bi bi-dash-circle-fill"></i> Deduct
                                </button>
                            </div>

                            <!-- Points Input -->
                            <div class="enr-pm-amount-section">
                                <label class="enr-pm-label">Points Amount</label>
                                <input type="number" id="quickPointsAmount" class="enr-pm-amount-input" value="10" min="-10000" max="10000" oninput="updateQuickPointsLivePreview()">
                                <div class="enr-pm-chips">
                                    <button type="button" class="enr-pm-chip" onclick="setQuickAmount(5)">5</button>
                                    <button type="button" class="enr-pm-chip active" onclick="setQuickAmount(10)">10</button>
                                    <button type="button" class="enr-pm-chip" onclick="setQuickAmount(25)">25</button>
                                    <button type="button" class="enr-pm-chip" onclick="setQuickAmount(50)">50</button>
                                    <button type="button" class="enr-pm-chip" onclick="setQuickAmount(100)">100</button>
                                    <button type="button" class="enr-pm-chip" onclick="setQuickAmount(250)">250</button>
                                </div>
                            </div>

                            <!-- Course Dropdown -->
                            <div class="enr-pm-field">
                                <label class="enr-pm-label">
                                    <i class="bi bi-journal-bookmark"></i> Associated Course
                                </label>
                                <select id="quickPointsCourseId" class="enr-pm-select">
                                    <option value="">General (Course Independent)</option>
                                    @foreach($courses as $c)
                                    <option value="{{ $c->id }}">{{ $c->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Reason Input -->
                            <div class="enr-pm-field">
                                <label class="enr-pm-label">
                                    <i class="bi bi-chat-left-quote"></i> Reason <span class="enr-pm-required">*</span>
                                </label>
                                <input type="text" id="quickPointsReason" class="enr-pm-text-input" placeholder="e.g., Exceptional participation in class discussion">
                                <div class="enr-pm-reason-tags">
                                    <button type="button" class="enr-pm-reason-tag" onclick="setQuickReason('Active class participation')">
                                        <i class="bi bi-hand-thumbs-up"></i> Participation
                                    </button>
                                    <button type="button" class="enr-pm-reason-tag" onclick="setQuickReason('Homework excellence bonus')">
                                        <i class="bi bi-check2-all"></i> Homework
                                    </button>
                                    <button type="button" class="enr-pm-reason-tag" onclick="setQuickReason('Quiz excellence high score')">
                                        <i class="bi bi-award"></i> Quiz
                                    </button>
                                    <button type="button" class="enr-pm-reason-tag" onclick="setQuickReason('Peer collaboration & teamwork')">
                                        <i class="bi bi-people"></i> Teamwork
                                    </button>
                                    <button type="button" class="enr-pm-reason-tag" onclick="setQuickReason('Missed assignment deadline')">
                                        <i class="bi bi-clock-history"></i> Late
                                    </button>
                                    <button type="button" class="enr-pm-reason-tag" onclick="setQuickReason('Classroom disruption')">
                                        <i class="bi bi-exclamation-octagon"></i> Disruption
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="enr-pm-footer">
                            <button type="button" class="enr-pm-cancel" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="enr-pm-submit" id="quickPointsSubmitBtn" onclick="submitQuickPointsAdjust()">
                                <i class="bi bi-plus-circle"></i> Award Points
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/teacher-dashboard.js') }}"></script>
<script src="{{ asset('assets/js/enrollment-requests.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
