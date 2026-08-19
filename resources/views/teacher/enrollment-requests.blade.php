@extends('layouts.app')

@section('title', 'Enrollment Requests - Edvora Tech')

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
                                    <i class="bi bi-person-check me-2"></i>Enrollment Requests
                                </h2>
                                <p class="text-muted mb-0">Review and manage student enrollment requests for your courses</p>
                            </div>
                            @if($pendingCount > 0)
                            <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                                <i class="bi bi-hourglass-split me-1"></i>{{ $pendingCount }} Pending
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="enroll-filter-card">
                            <form method="GET" action="{{ route('teacher.enrollment-requests') }}">
                                <div class="enroll-filter-inner">
                                    <div class="enroll-filter-group">
                                        <label class="enroll-filter-label">
                                            <i class="bi bi-journal-bookmark me-1"></i>Course
                                        </label>
                                        <select name="course_id" class="enroll-filter-select">
                                            <option value="">All Courses</option>
                                            @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->title }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="enroll-filter-group">
                                        <label class="enroll-filter-label">
                                            <i class="bi bi-circle-half me-1"></i>Status
                                        </label>
                                        <select name="status" class="enroll-filter-select">
                                            <option value="">All Status</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </div>
                                    <div class="enroll-filter-actions">
                                        <button type="submit" class="enroll-filter-btn-apply">
                                            <i class="bi bi-funnel-fill me-1"></i>Apply Filter
                                        </button>
                                        <a href="{{ route('teacher.enrollment-requests') }}" class="enroll-filter-btn-clear">
                                            <i class="bi bi-x-lg me-1"></i>Clear
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Requests List -->
                <div class="row">
                    <div class="col-12">
                        @forelse($requests as $req)
                        <div class="enrollment-request-card mb-3 {{ $req->status }}" id="request-{{ $req->id }}">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="row align-items-center">
                                        <!-- Student Info -->
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $req->user->avatar ? (str_starts_with($req->user->avatar, 'http') ? $req->user->avatar : asset('storage/' . $req->user->avatar)) : 'https://ui-avatars.com/api/?name=' . urlencode($req->user->name) . '&size=50' }}"
                                                     alt="{{ $req->user->name }}"
                                                     class="rounded-circle me-3"
                                                     style="width:50px; height:50px; object-fit:cover;">
                                                <div>
                                                    <h6 class="fw-bold mb-0">{{ $req->user->name }}</h6>
                                                    <small class="text-muted">{{ $req->user->email }}</small>
                                                    <br>
                                                    <small class="text-muted">Requested: {{ $req->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Course Info -->
                                        <div class="col-md-3">
                                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                                {{ $req->course->title }}
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
                                            <button class="btn btn-sm btn-outline-info me-1"
                                                    onclick="viewStudentProfile({{ $req->id }})">
                                                <i class="bi bi-person-badge"></i> Profile
                                            </button>
                                            <button class="btn btn-sm btn-success me-1"
                                                    onclick="approveRequest({{ $req->id }})">
                                                <i class="bi bi-check-lg"></i> Approve
                                            </button>
                                            <button class="btn btn-sm btn-danger"
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
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-3 text-muted"></i>
                            <h5 class="mt-3 text-muted">No enrollment requests yet</h5>
                            <p class="text-muted">When students request to join your courses, they'll appear here.</p>
                        </div>
                        @endforelse

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $requests->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Profile Modal -->
            <div class="modal fade" id="studentProfileModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bi bi-person-badge me-2"></i>Student Profile</h5>
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
                    <div class="modal-content">
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
