@extends('layouts.app')

@section('title', $student->name . ' - Scoring History')

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
                            <h2 class="fw-bold mb-1">Scoring History</h2>
                            <p class="text-muted mb-0">{{ $student->name }} &middot; {{ $course->title }}</p>
                        </div>
                        <a href="{{ route('teacher.courses.points', $course->id) }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Scoring
                        </a>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-4">
                        <div class="text-muted small mb-1">Total Score</div>
                        <div class="display-6 fw-bold {{ $totalScore >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($totalScore) }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-4">
                        <div class="text-muted small mb-1">Earned</div>
                        <div class="display-6 fw-bold text-success">+{{ number_format($earned) }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-4">
                        <div class="text-muted small mb-1">Deducted</div>
                        <div class="display-6 fw-bold text-danger">-{{ number_format($deducted) }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-4">
                        <div class="text-muted small mb-1">Course Rank</div>
                        <div class="display-6 fw-bold text-primary">#{{ $rank ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="fw-bold mb-0"><i class="bi bi-list-ul me-2 text-primary"></i>Full History</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Reason</th>
                                        <th>Type</th>
                                        <th>Awarded By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($history as $entry)
                                    <tr>
                                        <td>{{ $entry->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <span class="badge {{ $entry->amount >= 0 ? 'bg-success' : 'bg-danger' }}">
                                                {{ $entry->amount >= 0 ? '+' : '' }}{{ number_format($entry->amount) }}
                                            </span>
                                        </td>
                                        <td>{{ $entry->reason ?? '—' }}</td>
                                        <td><span class="text-muted text-capitalize">{{ str_replace('_', ' ', $entry->type) }}</span></td>
                                        <td>{{ $entry->creator?->name ?? 'System' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No scoring history yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($history->hasPages())
                        <div class="card-footer bg-white border-0 d-flex justify-content-center">
                            {{ $history->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
