@extends('layouts.app')

@section('title', 'Student Scoring - ' . $course->title)

@push('styles')
<link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
@endpush

@section('hide_header', true)
@section('hide_footer', true)

@section('content')
<div class="dashboard-wrapper">
    <x-teacher-sidebar />

    <main class="main-content" id="mainContent">
        <div class="container-fluid py-4 py-lg-5 px-3 px-md-4">
            
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12 col-xl-11 mx-auto">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-4 rounded-4" style="background: linear-gradient(135deg, #090e1a 0%, #0f1c3f 50%, #132b68 100%); color: white; box-shadow: 0 10px 30px rgba(15, 28, 63, 0.25);">
                        <div>
                            <span class="badge bg-primary bg-opacity-25 text-info px-3 py-1 rounded-pill mb-2 fw-semibold border border-info border-opacity-25">
                                <i class="bi bi-star-fill me-1"></i> Student Gamification
                            </span>
                            <h2 class="fw-bold mb-1 text-white">Student Points & Scoring</h2>
                            <p class="text-white-50 mb-0 small">{{ $course->title }}</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('teacher.courses.detail', $course->id) }}" class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold" style="backdrop-filter: blur(6px);">
                                <i class="bi bi-arrow-left me-1"></i> Back to Course
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('points_success'))
            <div class="row mb-4">
                <div class="col-12 col-xl-11 mx-auto">
                    <div class="alert alert-success border-0 rounded-4 shadow-sm d-flex align-items-center justify-content-between p-3" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            <span class="fw-medium">{{ session('points_success') }}</span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Points Adjustment Form -->
            <div class="row mb-4 mb-lg-5">
                <div class="col-12 col-xl-11 mx-auto">
                    <div class="glass-card-premium p-4">
                        <div class="card-header-premium p-0 pb-3 mb-3">
                            <h5 class="card-title-premium">
                                <i class="bi bi-plus-slash-minus text-primary"></i> Adjust Student Points
                            </h5>
                            <span class="text-muted small">Award or deduct XP/points directly</span>
                        </div>
                        
                        <form action="{{ route('teacher.courses.points.store', $course->id) }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark">Select Student</label>
                                    <select name="user_id" class="form-select rounded-3 shadow-none border" required>
                                        <option value="">Choose enrolled student...</option>
                                        @foreach($enrollments as $enrollment)
                                        <option value="{{ $enrollment->user_id }}" {{ old('user_id') == $enrollment->user_id ? 'selected' : '' }}>
                                            {{ $enrollment->user->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-dark">Points (+ / -)</label>
                                    <input type="number" name="amount" class="form-control rounded-3 shadow-none border" placeholder="e.g. 10 or -5" required>
                                    <span class="text-muted" style="font-size: 0.72rem;">Positive adds, negative deducts.</span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-dark">Scoring Category</label>
                                    <select name="type" class="form-select rounded-3 shadow-none border">
                                        @foreach($rules->groupBy('type') as $type => $group)
                                        <optgroup label="{{ ucfirst($type) }}">
                                            @foreach($group as $rule)
                                            <option value="{{ $rule->action_name }}">{{ $rule->label }} ({{ $rule->default_score >= 0 ? '+' : '' }}{{ $rule->default_score }})</option>
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                        <option value="manual">Manual Custom Adjustment</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold shadow-sm">
                                        <i class="bi bi-check-lg me-1"></i> Apply
                                    </button>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-dark">Reason / Note for Student</label>
                                    <input type="text" name="reason" class="form-control rounded-3 shadow-none border" placeholder="e.g. Great participation in live class session" required maxlength="500">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Students Overview Table -->
            <div class="row mb-4 mb-lg-5">
                <div class="col-12 col-xl-11 mx-auto">
                    <div class="glass-card-premium">
                        <div class="card-header-premium">
                            <h5 class="card-title-premium">
                                <i class="bi bi-people-fill text-primary"></i> Enrolled Students Scores & Rankings
                            </h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold">{{ $enrollments->count() }} Students</span>
                        </div>
                        <div class="p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                    <thead class="table-light text-muted" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                        <tr>
                                            <th class="ps-4 py-3">Student</th>
                                            <th class="py-3">Total Score</th>
                                            <th class="py-3">Earned</th>
                                            <th class="py-3">Deducted</th>
                                            <th class="py-3">Rank</th>
                                            <th class="pe-4 py-3 text-end">History</th>
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
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    @php
                                                        $avatar = $student->publicAvatarUrl();
                                                    @endphp
                                                    <img src="{{ $avatar }}" class="rounded-circle shadow-sm" width="38" height="38" style="object-fit:cover" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=1f8fff&color=fff'">
                                                    <div>
                                                        <div class="fw-bold text-dark small">{{ $student->name }}</div>
                                                        <small class="text-muted" style="font-size: 0.75rem;">{{ $student->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge {{ $total >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} px-3 py-1 rounded-pill fw-bold fs-6">
                                                    {{ number_format($total) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-success fw-semibold">+{{ number_format($earned) }}</span>
                                            </td>
                                            <td>
                                                <span class="text-danger fw-semibold">-{{ number_format($deducted) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning bg-opacity-15 text-dark px-3 py-1 rounded-pill fw-bold">
                                                    #{{ $rank ?? '—' }}
                                                </span>
                                            </td>
                                            <td class="pe-4 text-end">
                                                <a href="{{ route('teacher.courses.points.student', [$course->id, $student->id]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                                    <i class="bi bi-clock-history me-1"></i> Logs
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="bi bi-people fs-2 d-block mb-2 opacity-25"></i>
                                                <p class="small mb-0">No students enrolled in this course yet.</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Point Changes Table -->
            <div class="row mb-4">
                <div class="col-12 col-xl-11 mx-auto">
                    <div class="glass-card-premium">
                        <div class="card-header-premium">
                            <h5 class="card-title-premium">
                                <i class="bi bi-clock-history text-primary"></i> Recent Point Changes Log
                            </h5>
                            <a href="{{ route('scoring.help') }}" class="btn-header-link">How Scoring Works →</a>
                        </div>
                        <div class="p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                    <thead class="table-light text-muted" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                        <tr>
                                            <th class="ps-4 py-3">Student</th>
                                            <th class="py-3">Points</th>
                                            <th class="py-3">Reason</th>
                                            <th class="py-3">Category</th>
                                            <th class="py-3">Awarded By</th>
                                            <th class="pe-4 py-3">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($points as $point)
                                        <tr>
                                            <td class="ps-4 fw-semibold text-dark">{{ $students[$point->user_id]->name ?? 'Unknown' }}</td>
                                            <td>
                                                <span class="badge {{ $point->amount >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} px-2 py-1 fw-bold">
                                                    {{ $point->amount >= 0 ? '+' : '' }}{{ number_format($point->amount) }}
                                                </span>
                                            </td>
                                            <td class="text-dark">{{ $point->reason ?? '—' }}</td>
                                            <td><span class="text-muted text-capitalize">{{ str_replace('_', ' ', $point->type) }}</span></td>
                                            <td class="text-muted small">{{ $point->creator?->name ?? 'System' }}</td>
                                            <td class="pe-4 text-muted small">{{ $point->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="bi bi-journal-x fs-2 d-block mb-2 opacity-25"></i>
                                                <p class="small mb-0">No point entries recorded yet.</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($points->hasPages())
                            <div class="p-3 border-top d-flex justify-content-center">
                                {{ $points->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>
@endsection
