@extends('layouts.app')

@section('title', 'Enrolled Students & Requests - Edvora Tech')

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
            <div class="container-fluid py-5">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h2 class="fw-bold text-premium mb-1">
                                    <i class="bi bi-people-fill me-2"></i>Students & Enrollments
                                </h2>
                                <p class="text-muted mb-0">View all students enrolled in your courses and manage incoming requests</p>
                            </div>
                            @if($pendingCount > 0)
                            <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                                <i class="bi bi-hourglass-split me-1"></i>{{ $pendingCount }} Pending Requests
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Stats Overview Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="enroll-stat-card">
                            <div class="enroll-stat-icon primary">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                            <div>
                                <div class="enroll-stat-val">{{ $enrolledTotalCount }}</div>
                                <div class="enroll-stat-title">Enrolled Students</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="enroll-stat-card">
                            <div class="enroll-stat-icon success">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                            <div>
                                <div class="enroll-stat-val">{{ $activeEnrolledCount }}</div>
                                <div class="enroll-stat-title">Active Learning</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="enroll-stat-card">
                            <div class="enroll-stat-icon info">
                                <i class="bi bi-award-fill"></i>
                            </div>
                            <div>
                                <div class="enroll-stat-val">{{ $completedCount }}</div>
                                <div class="enroll-stat-title">Completed Course</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="enroll-stat-card">
                            <div class="enroll-stat-icon warning">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <div class="enroll-stat-val">{{ $pendingCount }}</div>
                                <div class="enroll-stat-title">Pending Requests</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="enroll-nav-tabs">
                                <button type="button"
                                        class="enroll-nav-tab {{ $activeTab === 'enrolled' ? 'active' : '' }}"
                                        data-tab="enrolled"
                                        onclick="switchEnrollmentTab('enrolled')">
                                    <i class="bi bi-people-fill"></i>
                                    <span>Enrolled Students</span>
                                    <span class="enroll-tab-badge badge-primary">{{ $enrollments->total() }}</span>
                                </button>
                                <button type="button"
                                        class="enroll-nav-tab {{ $activeTab === 'requests' ? 'active' : '' }}"
                                        data-tab="requests"
                                        onclick="switchEnrollmentTab('requests')">
                                    <i class="bi bi-inbox-fill"></i>
                                    <span>Enrollment Requests</span>
                                    @if($pendingCount > 0)
                                        <span class="enroll-tab-badge badge-warning">{{ $pendingCount }} Pending</span>
                                    @else
                                        <span class="enroll-tab-badge badge-primary">{{ $requests->total() }}</span>
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="enroll-filter-card">
                            <form method="GET" action="{{ route('teacher.enrollment-requests') }}" id="filterForm">
                                <input type="hidden" name="tab" id="filterTabInput" value="{{ $activeTab }}">
                                <div class="enroll-filter-inner">
                                    <div class="enroll-filter-group" style="flex: 2 1 240px;">
                                        <label class="enroll-filter-label">
                                            <i class="bi bi-search me-1"></i>Search Student
                                        </label>
                                        <input type="text"
                                               name="search"
                                               class="enroll-filter-select"
                                               placeholder="Name or email..."
                                               value="{{ request('search') }}">
                                    </div>
                                    <div class="enroll-filter-group">
                                        <label class="enroll-filter-label">
                                            <i class="bi bi-journal-bookmark me-1"></i>Course
                                        </label>
                                        <select name="course_id" class="enroll-filter-select">
                                            <option value="">All Courses</option>
                                            @foreach($courses as $c)
                                            <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                                                {{ $c->title }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="enroll-filter-group">
                                        <label class="enroll-filter-label">
                                            <i class="bi bi-circle-half me-1"></i>Status
                                        </label>
                                        <select name="status" class="enroll-filter-select">
                                            <option value="">All Statuses</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending (Request)</option>
                                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved (Request)</option>
                                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected (Request)</option>
                                        </select>
                                    </div>
                                    <div class="enroll-filter-actions">
                                        <button type="submit" class="enroll-filter-btn-apply">
                                            <i class="bi bi-funnel-fill me-1"></i>Filter
                                        </button>
                                        <a href="{{ route('teacher.enrollment-requests', ['tab' => $activeTab]) }}" class="enroll-filter-btn-clear">
                                            <i class="bi bi-x-lg me-1"></i>Clear
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tab Pane 1: Enrolled Students -->
                <div id="pane-enrolled" class="{{ $activeTab === 'enrolled' ? '' : 'd-none' }}">
                    <div class="row">
                        <div class="col-12">
                            @forelse($enrollments as $en)
                            @php
                                $studentUser = $en->user;
                                $courseItem  = $en->course;
                                $prog = (int)($en->progress_percentage ?? 0);
                                $progColor = ($en->status === 'banned') ? '#ef4444' : ($prog >= 80 ? '#10b981' : ($prog >= 40 ? '#f59e0b' : '#1F8FFF'));
                                $avatarUrl = $studentUser?->avatar
                                    ? (str_starts_with($studentUser->avatar, 'http') ? $studentUser->avatar : asset('storage/' . $studentUser->avatar))
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($studentUser?->name ?? 'Student') . '&size=60';
                            @endphp
                            <div class="enrollment-request-card enrolled-student-card mb-3" id="enrollment-{{ $en->id }}">
                                <div class="card border-0 shadow-sm" style="{{ $en->status === 'banned' ? 'border-left: 4px solid #ef4444 !important; background: #fffdfd;' : 'border-left: 4px solid #10b981 !important;' }}">
                                    <div class="card-body p-4">
                                        <div class="row align-items-center">
                                            <!-- Student Info -->
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $avatarUrl }}"
                                                         alt="{{ $studentUser?->name ?? 'Student' }}"
                                                         class="rounded-circle me-3"
                                                         style="width:52px; height:52px; object-fit:cover; border: 2.5px solid #1F8FFF;">
                                                    <div>
                                                        <h6 class="fw-bold mb-0 text-dark">{{ $studentUser?->name ?? 'Student' }}</h6>
                                                        <small class="text-muted">{{ $studentUser?->email ?? 'No email' }}</small>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar-check me-1"></i>Enrolled: {{ $en->created_at ? $en->created_at->format('M d, Y') : 'N/A' }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Course & Progress -->
                                            <div class="col-md-3">
                                                <a href="{{ route('teacher.courses.detail', $en->course_id) }}" class="text-decoration-none">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-semibold">
                                                        <i class="bi bi-journal-bookmark me-1"></i>{{ $courseItem?->title ?? 'Course' }}
                                                    </span>
                                                </a>
                                                <div class="student-progress-wrapper mt-2">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <small class="text-muted" style="font-size: 0.75rem;">Progress</small>
                                                        <small class="fw-bold" style="font-size: 0.75rem; color: {{ $progColor }};">{{ $prog }}%</small>
                                                    </div>
                                                    <div class="student-progress-bar">
                                                        <div class="student-progress-fill" style="width: {{ $prog }}%; background: {{ $progColor }};"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            <div class="col-md-2 text-center">
                                                @if($en->status === 'active')
                                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-semibold">
                                                        <i class="bi bi-check-circle-fill me-1"></i>Active
                                                    </span>
                                                @elseif($en->status === 'completed')
                                                    <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill fw-semibold">
                                                        <i class="bi bi-trophy-fill me-1"></i>Completed
                                                    </span>
                                                @elseif($en->status === 'banned')
                                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-semibold">
                                                        <i class="bi bi-slash-circle me-1"></i>Banned
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary px-3 py-2 rounded-pill">
                                                        {{ ucfirst($en->status) }}
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Actions -->
                                            <div class="col-md-3 text-end d-flex gap-1 justify-content-end align-items-center flex-wrap">
                                                @if($studentUser)
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-info"
                                                        onclick="viewStudentProfile({{ $studentUser->id }}, 'user')"
                                                        title="View Student Profile">
                                                    <i class="bi bi-person-badge"></i> Profile
                                                </button>
                                                @endif

                                                <a href="{{ route('teacher.courses.detail', $en->course_id) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="View Course Details">
                                                    <i class="bi bi-eye"></i> Course
                                                </a>

                                                @if($en->status === 'banned')
                                                <form method="POST" action="{{ route('teacher.courses.students.unban', [$en->course_id, $en->user_id]) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-success"
                                                            onclick="return confirm('Reinstate {{ addslashes($studentUser?->name ?? 'Student') }} to this course?')"
                                                            title="Reinstate access">
                                                        <i class="bi bi-person-check"></i>
                                                    </button>
                                                </form>
                                                @else
                                                <form method="POST" action="{{ route('teacher.courses.students.ban', [$en->course_id, $en->user_id]) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Are you sure you want to ban {{ addslashes($studentUser?->name ?? 'Student') }} from this course?')"
                                                            title="Ban student">
                                                        <i class="bi bi-slash-circle"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5 bg-white rounded-4 shadow-sm border p-4">
                                <i class="bi bi-people display-3 text-muted"></i>
                                <h5 class="mt-3 text-muted fw-bold">No enrolled students found</h5>
                                <p class="text-muted">When students register or get approved for your courses, they will appear here.</p>
                            </div>
                            @endforelse

                            <!-- Enrolled Pagination -->
                            <div class="mt-4">
                                {{ $enrollments->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Pane 2: Enrollment Requests -->
                <div id="pane-requests" class="{{ $activeTab === 'requests' ? '' : 'd-none' }}">
                    <div class="row">
                        <div class="col-12">
                            @forelse($requests as $req)
                            @php
                                $reqAvatar = $req->user?->avatar
                                    ? (str_starts_with($req->user->avatar, 'http') ? $req->user->avatar : asset('storage/' . $req->user->avatar))
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($req->user?->name ?? 'Student') . '&size=50';
                            @endphp
                            <div class="enrollment-request-card mb-3 {{ $req->status }}" id="request-{{ $req->id }}">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <div class="row align-items-center">
                                            <!-- Student Info -->
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $reqAvatar }}"
                                                         alt="{{ $req->user?->name }}"
                                                         class="rounded-circle me-3"
                                                         style="width:50px; height:50px; object-fit:cover;">
                                                    <div>
                                                        <h6 class="fw-bold mb-0">{{ $req->user?->name ?? 'Unknown Student' }}</h6>
                                                        <small class="text-muted">{{ $req->user?->email }}</small>
                                                        <br>
                                                        <small class="text-muted">Requested: {{ $req->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Course Info -->
                                            <div class="col-md-3">
                                                <span class="badge bg-primary rounded-pill px-3 py-2">
                                                    {{ $req->course?->title ?? 'Course' }}
                                                </span>
                                                @if($req->student_message)
                                                <p class="text-muted small mt-2 mb-0">
                                                    <i class="bi bi-chat-quote me-1"></i>{{ \Illuminate\Support\Str::limit($req->student_message, 50) }}
                                                </p>
                                                @endif
                                            </div>

                                            <!-- Status -->
                                            <div class="col-md-2 text-center">
                                                @if($req->status === 'pending')
                                                    <span class="badge bg-warning text-dark px-3 py-2">
                                                        <i class="bi bi-hourglass-split me-1"></i>Pending
                                                    </span>
                                                @elseif($req->status === 'approved')
                                                    <span class="badge bg-success px-3 py-2">
                                                        <i class="bi bi-check-circle me-1"></i>Approved
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger px-3 py-2">
                                                        <i class="bi bi-x-circle me-1"></i>Rejected
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Actions -->
                                            <div class="col-md-3 text-end">
                                                @if($req->status === 'pending')
                                                <button type="button" class="btn btn-sm btn-outline-info me-1"
                                                        onclick="viewStudentProfile({{ $req->id }}, 'request')">
                                                    <i class="bi bi-person-badge"></i> Profile
                                                </button>
                                                <button type="button" class="btn btn-sm btn-success me-1"
                                                        onclick="approveRequest({{ $req->id }})">
                                                    <i class="bi bi-check-lg"></i> Approve
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="openRejectModal({{ $req->id }})">
                                                    <i class="bi bi-x-lg"></i> Reject
                                                </button>
                                                @elseif($req->status === 'rejected')
                                                    <small class="text-muted">
                                                        <i class="bi bi-info-circle me-1"></i>
                                                        {{ \Illuminate\Support\Str::limit($req->rejection_reason, 40) }}
                                                    </small>
                                                @else
                                                    <small class="text-success">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        Reviewed {{ $req->reviewed_at?->diffForHumans() }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5 bg-white rounded-4 shadow-sm border p-4">
                                <i class="bi bi-inbox display-3 text-muted"></i>
                                <h5 class="mt-3 text-muted fw-bold">No enrollment requests yet</h5>
                                <p class="text-muted">When students request admission to your courses, they'll appear here.</p>
                            </div>
                            @endforelse

                            <!-- Requests Pagination -->
                            <div class="mt-4">
                                {{ $requests->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Profile Modal -->
            <div class="modal fade" id="studentProfileModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header border-bottom">
                            <h5 class="modal-title fw-bold"><i class="bi bi-person-badge me-2 text-primary"></i>Student Profile</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center" id="studentProfileContent">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reject Modal -->
            <div class="modal fade" id="rejectModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Reject Request</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted mb-3">Please provide a reason for rejecting this student's enrollment request. This message will be sent to the student via email.</p>
                            <div class="mb-3">
                                <label for="rejectionReason" class="form-label fw-bold">Rejection Reason</label>
                                <textarea class="form-control" id="rejectionReason" rows="4"
                                          placeholder="e.g., Prerequisite courses not completed, class is full, etc."></textarea>
                            </div>
                            <input type="hidden" id="rejectRequestId" value="">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" onclick="submitRejection()">
                                <i class="bi bi-x-lg me-1"></i>Reject & Send Email
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
