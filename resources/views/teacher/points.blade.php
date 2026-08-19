@extends('layouts.app')

@section('title', 'Student Scoring - ' . $course->title)

@push('styles')
<link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="dashboard-wrapper">
    <x-teacher-sidebar />

    <main class="main-content">
        <div class="container-fluid py-5">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h2 class="fw-bold mb-1">Student Scoring</h2>
                            <p class="text-muted mb-0">{{ $course->title }}</p>
                        </div>
                        <a href="{{ route('teacher.courses.detail', $course->id) }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Course
                        </a>
                    </div>
                </div>
            </div>

            @if(session('points_success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('points_success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Quick Points Form -->
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="fw-bold mb-0"><i class="bi bi-plus-slash-minus me-2 text-primary"></i>Adjust Points</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('teacher.courses.points.store', $course->id) }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Student</label>
                                        <select name="user_id" class="form-select" required>
                                            <option value="">Select student</option>
                                            @foreach($enrollments as $enrollment)
                                            <option value="{{ $enrollment->user_id }}" {{ old('user_id') == $enrollment->user_id ? 'selected' : '' }}>
                                                {{ $enrollment->user->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Amount</label>
                                        <input type="number" name="amount" class="form-control" placeholder="e.g. 10 or -5" required>
                                        <div class="form-text">Positive adds points, negative deducts.</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Type</label>
                                        <select name="type" class="form-select">
                                            @foreach($rules->groupBy('type') as $type => $group)
                                            <optgroup label="{{ ucfirst($type) }}">
                                                @foreach($group as $rule)
                                                <option value="{{ $rule->action_name }}">{{ $rule->label }} ({{ $rule->default_score >= 0 ? '+' : '' }}{{ $rule->default_score }})</option>
                                                @endforeach
                                            </optgroup>
                                            @endforeach
                                            <option value="manual">Manual Adjustment</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-check-lg me-1"></i>Apply
                                        </button>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Reason</label>
                                        <input type="text" name="reason" class="form-control" placeholder="e.g. Late to class" required maxlength="500">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Overview -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="fw-bold mb-3"><i class="bi bi-people me-2 text-primary"></i>Student Scores</h5>
                    <div class="card border-0 shadow-sm">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Total Score</th>
                                        <th>Earned</th>
                                        <th>Deducted</th>
                                        <th>Rank</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($enrollments as $enrollment)
                                    @php
                                        $student = $enrollment->user;
                                        $earned = $student->earnedPoints();
                                        $deducted = $student->deductedPoints();
                                        $total = $student->totalScore();
                                        $rank = $student->leaderboardRank($course->id);
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $avatar = $student->avatar
                                                        ? (str_starts_with($student->avatar, 'http') ? $student->avatar : asset('storage/' . $student->avatar))
                                                        : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&background=1f8fff&color=fff';
                                                @endphp
                                                <img src="{{ $avatar }}" class="rounded-circle me-3" width="40" height="40" style="object-fit:cover" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=1f8fff&color=fff'">
                                                <div>
                                                    <div class="fw-semibold">{{ $student->name }}</div>
                                                    <small class="text-muted">{{ $student->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="fw-bold {{ $total >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($total) }}</span></td>
                                        <td class="text-success">+{{ number_format($earned) }}</td>
                                        <td class="text-danger">-{{ number_format($deducted) }}</td>
                                        <td><span class="badge bg-primary bg-opacity-10 text-primary">#{{ $rank ?? '—' }}</span></td>
                                        <td>
                                            <a href="{{ route('teacher.courses.points.student', [$course->id, $student->id]) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-clock-history me-1"></i>History
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No students enrolled in this course yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent History -->
            <div class="row">
                <div class="col-12">
                    <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Point Changes</h5>
                    <div class="card border-0 shadow-sm">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Amount</th>
                                        <th>Reason</th>
                                        <th>Type</th>
                                        <th>By</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($points as $point)
                                    <tr>
                                        <td>{{ $students[$point->user_id]->name ?? 'Unknown' }}</td>
                                        <td>
                                            <span class="badge {{ $point->amount >= 0 ? 'bg-success' : 'bg-danger' }}">
                                                {{ $point->amount >= 0 ? '+' : '' }}{{ number_format($point->amount) }}
                                            </span>
                                        </td>
                                        <td>{{ $point->reason ?? '—' }}</td>
                                        <td><span class="text-muted text-capitalize">{{ str_replace('_', ' ', $point->type) }}</span></td>
                                        <td>{{ $point->creator?->name ?? 'System' }}</td>
                                        <td>{{ $point->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No point entries yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($points->hasPages())
                        <div class="card-footer bg-white border-0 d-flex justify-content-center">
                            {{ $points->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
