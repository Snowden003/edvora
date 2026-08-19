@extends('layouts.app')

@section('title', 'Leaderboard - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/leaderboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
<!-- Header -->

    <!-- Leaderboard Hero -->
    <header class="leaderboard-hero">
        <div class="leaderboard-bg-animation">
            <div class="particle p1"
                style="position:absolute; top:20%; left:10%; width:5px; height:5px; background:white; border-radius:50%; opacity:0.3;">
            </div>
            <div class="particle p2"
                style="position:absolute; top:60%; left:85%; width:7px; height:7px; background:white; border-radius:50%; opacity:0.2;">
            </div>
            <div class="particle p3"
                style="position:absolute; top:30%; left:70%; width:4px; height:4px; background:white; border-radius:50%; opacity:0.4;">
            </div>
        </div>

        <div class="container position-relative z-top" style="z-index: 10;">
            <h1 class="hero-title">{{ $monthName }} Wall of Records</h1>
            <p class="hero-subtitle">Celebrating the brilliance and hard work of our top performing students in {{ $monthName }} {{ $currentYear }}</p>

            <!-- Filters -->
            <form method="GET" action="{{ route('leaderboard') }}" class="row g-2 justify-content-center mb-4">
                <div class="col-md-4 col-sm-6">
                    <select name="course_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Courses</option>
                        @foreach($courses as $id => $title)
                        <option value="{{ $id }}" {{ $courseId == $id ? 'selected' : '' }}>{{ $title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <select name="range" class="form-select" onchange="this.form.submit()">
                        <option value="weekly" {{ $range === 'weekly' ? 'selected' : '' }}>This Week</option>
                        <option value="monthly" {{ $range === 'monthly' ? 'selected' : '' }}>This Month</option>
                        <option value="all_time" {{ $range === 'all_time' ? 'selected' : '' }}>All Time</option>
                    </select>
                </div>
            </form>

            @if($top3->count() >= 3)
            <!-- Top 3 Podium -->
            <div class="podium-container">
                @php
                    $podiumOrder = [1, 0, 2]; // Rank 2, Rank 1, Rank 3
                    $rankClasses = ['rank-1', 'rank-2', 'rank-3'];
                @endphp

                <!-- Rank 2 -->
                @php $second = $top3->get(1); @endphp
                <div class="podium-item rank-2">
                    <div class="podium-rank">2</div>
                    <div class="student-img-container">
                        @php
                            $avatar2 = $second->user->avatar
                                ? (str_starts_with($second->user->avatar, 'http') ? $second->user->avatar : asset('storage/' . $second->user->avatar))
                                : 'https://ui-avatars.com/api/?name=' . urlencode($second->user->name) . '&size=200&background=c0c0c0&color=fff';
                        @endphp
                        <img src="{{ $avatar2 }}" class="student-img" alt="{{ $second->user->name }}"
                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($second->user->name) }}&size=200&background=c0c0c0&color=fff'">
                    </div>
                    <h3 class="student-name">{{ $second->user->name }}</h3>
                    <p class="student-dept">{{ $second->user->department ?? 'Student' }}</p>
                    <div class="student-score">{{ number_format($second->xp) }} <span class="xp-label">XP</span></div>
                </div>

                <!-- Rank 1 -->
                @php $first = $top3->get(0); @endphp
                <div class="podium-item rank-1">
                    <div class="podium-rank"><i class="bi bi-trophy-fill"></i></div>
                    <div class="student-img-container">
                        @php
                            $avatar1 = $first->user->avatar
                                ? (str_starts_with($first->user->avatar, 'http') ? $first->user->avatar : asset('storage/' . $first->user->avatar))
                                : 'https://ui-avatars.com/api/?name=' . urlencode($first->user->name) . '&size=200&background=ffd700&color=fff';
                        @endphp
                        <img src="{{ $avatar1 }}" class="student-img" alt="{{ $first->user->name }}"
                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($first->user->name) }}&size=200&background=ffd700&color=fff'">
                    </div>
                    <h3 class="student-name">{{ $first->user->name }}</h3>
                    <p class="student-dept">{{ $first->user->department ?? 'Student' }}</p>
                    <div class="student-score">{{ number_format($first->xp) }} <span class="xp-label">XP</span></div>
                </div>

                <!-- Rank 3 -->
                @php $third = $top3->get(2); @endphp
                <div class="podium-item rank-3">
                    <div class="podium-rank">3</div>
                    <div class="student-img-container">
                        @php
                            $avatar3 = $third->user->avatar
                                ? (str_starts_with($third->user->avatar, 'http') ? $third->user->avatar : asset('storage/' . $third->user->avatar))
                                : 'https://ui-avatars.com/api/?name=' . urlencode($third->user->name) . '&size=200&background=cd7f32&color=fff';
                        @endphp
                        <img src="{{ $avatar3 }}" class="student-img" alt="{{ $third->user->name }}"
                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($third->user->name) }}&size=200&background=cd7f32&color=fff'">
                    </div>
                    <h3 class="student-name">{{ $third->user->name }}</h3>
                    <p class="student-dept">{{ $third->user->department ?? 'Student' }}</p>
                    <div class="student-score">{{ number_format($third->xp) }} <span class="xp-label">XP</span></div>
                </div>
            </div>
            @elseif($top3->isEmpty())
            <div class="text-center mt-5">
                <i class="bi bi-trophy text-white" style="font-size: 3rem; opacity: 0.5;"></i>
                <p class="text-white mt-3" style="opacity: 0.8;">No leaderboard data available for {{ $monthName }} {{ $range !== 'all_time' ? $currentYear : '' }} yet.</p>
            </div>
            @endif
        </div>
    </header>

    <!-- Rankings Section -->
    <main class="leaderboard-list-section">
        <div class="container">
            <div class="leaderboard-table-container">
                <!-- Table Header -->
                <div class="header-row"
                    style="background:rgba(31, 143, 255, 0.05); border-bottom:2px solid rgba(31, 143, 255, 0.1);">
                    <div class="row-rank text-primary">#</div>
                    <div class="card-info header-info text-primary">
                        <div class="card-name pb-0 mb-0">Student Name</div>
                        <div class="card-dept mt-0 pt-0">Department</div>
                    </div>
                    <div class="card-score text-primary text-end">{{ $monthName }} XP Score</div>
                </div>

                @php $restStartRank = $top3->count() > 0 ? 4 : 1; @endphp
                @forelse($rest as $index => $entry)
                <div class="ranking-card">
                    <div class="card-rank">{{ $index + $restStartRank }}</div>
                    @php
                        $entryAvatar = $entry->user->avatar
                            ? (str_starts_with($entry->user->avatar, 'http') ? $entry->user->avatar : asset('storage/' . $entry->user->avatar))
                            : 'https://ui-avatars.com/api/?name=' . urlencode($entry->user->name) . '&size=100&background=1f8fff&color=fff';
                    @endphp
                    <img src="{{ $entryAvatar }}" class="card-avatar" alt="{{ $entry->user->name }}"
                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($entry->user->name) }}&size=100&background=1f8fff&color=fff'">
                    <div class="card-info">
                        <div class="card-name">{{ $entry->user->name }}</div>
                        <div class="card-dept">{{ $entry->user->department ?? 'Student' }}</div>
                    </div>
                    <div class="card-score">{{ number_format($entry->xp) }} <span class="xp-symbol">XP</span></div>
                </div>
                @empty
                    @if($top3->count() < 3)
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-trophy" style="font-size: 2.5rem; opacity: 0.3;"></i>
                        <p class="mt-3">No ranking data available yet.</p>
                    </div>
                    @endif
                @endforelse
            </div>
        </div>
    </main>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
