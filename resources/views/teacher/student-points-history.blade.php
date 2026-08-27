@extends('layouts.app')

@section('title', $student->name . ' - Scoring History - ' . $course->title)

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
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $student->publicAvatarUrl() }}" class="rounded-circle border border-2 border-white shadow-sm" width="56" height="56" style="object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=1f8fff&color=fff'">
                            <div>
                                <span class="badge bg-primary bg-opacity-25 text-info px-3 py-1 rounded-pill mb-1 fw-semibold border border-info border-opacity-25">
                                    <i class="bi bi-clock-history me-1"></i> Student Scoring Log
                                </span>
                                <h2 class="fw-bold mb-0 text-white">{{ $student->name }}</h2>
                                <p class="text-white-50 mb-0 small">{{ $student->email }} &middot; {{ $course->title }}</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('teacher.courses.points', $course->id) }}" class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold" style="backdrop-filter: blur(6px);">
                                <i class="bi bi-arrow-left me-1"></i> Back to Scoring
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Score Metric Cards -->
            <div class="row g-3 mb-4 mb-lg-5">
                <div class="col-12 col-xl-11 mx-auto">
                    <div class="row g-3">
                        <div class="col-sm-6 col-lg-3">
                            <div class="glass-card-premium text-center p-4 h-100">
                                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Score</span>
                                <div class="display-6 fw-bold {{ $totalScore >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($totalScore) }}
                                </div>
                                <span class="badge bg-light text-muted mt-2">Net Points</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="glass-card-premium text-center p-4 h-100">
                                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Earned Points</span>
                                <div class="display-6 fw-bold text-success">+{{ number_format($earned) }}</div>
                                <span class="badge bg-success bg-opacity-10 text-success mt-2">Positive Gains</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="glass-card-premium text-center p-4 h-100">
                                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Deductions</span>
                                <div class="display-6 fw-bold text-danger">-{{ number_format($deducted) }}</div>
                                <span class="badge bg-danger bg-opacity-10 text-danger mt-2">Penalties</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="glass-card-premium text-center p-4 h-100">
                                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Course Rank</span>
                                <div class="display-6 fw-bold text-primary">#{{ $rank ?? '—' }}</div>
                                <span class="badge bg-primary bg-opacity-10 text-primary mt-2">Leaderboard</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full History Table -->
            <div class="row mb-4">
                <div class="col-12 col-xl-11 mx-auto">
                    <div class="glass-card-premium">
                        <div class="card-header-premium">
                            <h5 class="card-title-premium">
                                <i class="bi bi-list-ul text-primary"></i> Complete Score Activity Log
                            </h5>
                            <span class="badge bg-secondary bg-opacity-10 text-dark px-3 py-2 rounded-pill fw-semibold">{{ $history->total() ?? $history->count() }} Entries</span>
                        </div>
                        <div class="p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                    <thead class="table-light text-muted" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                        <tr>
                                            <th class="ps-4 py-3">Date & Time</th>
                                            <th class="py-3">Points</th>
                                            <th class="py-3">Reason</th>
                                            <th class="py-3">Category</th>
                                            <th class="pe-4 py-3">Awarded By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($history as $entry)
                                        <tr>
                                            <td class="ps-4 text-dark fw-medium">{{ $entry->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <span class="badge {{ $entry->amount >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} px-3 py-1 rounded-pill fw-bold">
                                                    {{ $entry->amount >= 0 ? '+' : '' }}{{ number_format($entry->amount) }}
                                                </span>
                                            </td>
                                            <td class="text-dark">{{ $entry->reason ?? '—' }}</td>
                                            <td><span class="text-muted text-capitalize">{{ str_replace('_', ' ', $entry->type) }}</span></td>
                                            <td class="pe-4 text-muted small">{{ $entry->creator?->name ?? 'System' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="bi bi-clock-history fs-2 d-block mb-2 opacity-25"></i>
                                                <p class="small mb-0">No scoring history recorded for this student yet.</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($history->hasPages())
                            <div class="p-3 border-top d-flex justify-content-center">
                                {{ $history->links() }}
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
