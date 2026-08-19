@extends('layouts.app')

@section('title', $quiz->title . ' - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/exams.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/exam-show.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="dashboard-wrapper">
    @if(Auth::user()->role === 'teacher')
        <x-teacher-sidebar />
    @else
        <x-student-sidebar />
    @endif

    <main class="main-content">
        <div class="container-fluid py-4 px-xl-5">

            @php
                $statusMap = [
                    'active'    => ['bg'=>'#dcfce7','color'=>'#15803d','icon'=>'bi-play-circle-fill'],
                    'upcoming'  => ['bg'=>'#dbeafe','color'=>'#1d4ed8','icon'=>'bi-clock-fill'],
                    'completed' => ['bg'=>'#f1f5f9','color'=>'#475569','icon'=>'bi-check-circle-fill'],
                    'draft'     => ['bg'=>'#fef9c3','color'=>'#92400e','icon'=>'bi-pencil-fill'],
                ];
                $sc = $statusMap[$quiz->status] ?? $statusMap['draft'];
            @endphp

            {{-- Back + Title --}}
            <div class="es-page-header mb-4">
                <a href="{{ Auth::user()->role === 'teacher' ? route('teacher.exams.index') : route('student.exams.index') }}"
                   class="es-back-btn">
                    <i class="bi bi-arrow-left me-1"></i>Back to Exams
                </a>
                <div class="es-title-row">
                    <div>
                        <h4 class="es-title">{{ $quiz->title }}</h4>
                        <span class="es-course-tag">{{ $quiz->course->title ?? '—' }}</span>
                    </div>
                    @if(Auth::user()->role === 'teacher')
                    <div class="es-actions">
                        <a href="{{ route('teacher.exams.questions', $quiz) }}" class="es-btn es-btn-purple">
                            <i class="bi bi-list-check me-2"></i>Manage Questions
                        </a>
                        @if(!$quiz->is_published)
                        <form method="POST" action="{{ route('teacher.exams.publish', $quiz) }}">
                            @csrf
                            <button type="submit" class="es-btn es-btn-green">
                                <i class="bi bi-send-check me-2"></i>Publish Exam
                            </button>
                        </form>
                        @else
                        <span class="es-published-badge">
                            <i class="bi bi-check-circle-fill me-1"></i>Published
                        </span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="row g-4">

                {{-- LEFT COLUMN --}}
                <div class="col-lg-8">

                    {{-- Status + Description --}}
                    <div class="es-card mb-4">
                        <div class="es-card-header">
                            <span class="es-section-title"><i class="bi bi-info-circle me-2"></i>Exam Details</span>
                            <span class="es-status-badge" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">
                                <i class="bi {{ $sc['icon'] }} me-1"></i>{{ ucfirst($quiz->status) }}
                            </span>
                        </div>
                        @if($quiz->description)
                        <p class="es-description">{{ $quiz->description }}</p>
                        @endif

                        {{-- Stats row --}}
                        <div class="es-stats-row">
                            <div class="es-stat-box" style="border-color:#1F8FFF;">
                                <span class="es-stat-num" style="color:#1F8FFF;">{{ $quiz->questions->count() }}</span>
                                <span class="es-stat-lbl">Questions</span>
                            </div>
                            <div class="es-stat-box" style="border-color:#8b5cf6;">
                                <span class="es-stat-num" style="color:#8b5cf6;">{{ $quiz->duration_minutes }} min</span>
                                <span class="es-stat-lbl">Duration</span>
                            </div>
                            <div class="es-stat-box" style="border-color:#f59e0b;">
                                <span class="es-stat-num" style="color:#f59e0b;">+{{ $quiz->xp_reward }}</span>
                                <span class="es-stat-lbl">XP Reward</span>
                            </div>
                            <div class="es-stat-box" style="border-color:#10b981;">
                                <span class="es-stat-num" style="color:#10b981;">{{ $quiz->max_attempts }}×</span>
                                <span class="es-stat-lbl">Max Attempts</span>
                            </div>
                        </div>
                    </div>

                    {{-- Leaderboard --}}
                    <div class="es-card mb-4">
                        <div class="es-card-header">
                            <span class="es-section-title"><i class="bi bi-trophy-fill me-2"></i>Leaderboard</span>
                        </div>
                        @php $leaderboard = $quiz->leaderboard; @endphp
                        @if($leaderboard->count() > 0)
                            <div class="es-leaderboard">
                                @foreach($leaderboard as $entry)
                                <div class="es-leaderboard-item">
                                    <div class="es-rank">
                                        @if($entry->rank == 1)
                                            <span class="es-rank-badge es-rank-1"><i class="bi bi-trophy-fill"></i> 1st</span>
                                        @elseif($entry->rank == 2)
                                            <span class="es-rank-badge es-rank-2"><i class="bi bi-medal"></i> 2nd</span>
                                        @elseif($entry->rank == 3)
                                            <span class="es-rank-badge es-rank-3"><i class="bi bi-award"></i> 3rd</span>
                                        @else
                                            <span class="es-rank-badge">#{{ $entry->rank }}</span>
                                        @endif
                                    </div>
                                    <div class="es-user-info">
                                        <img src="{{ $entry->user->avatar ?? asset('assets/images/default-avatar.png') }}" class="es-user-avatar" alt="{{ $entry->user->name }}">
                                        <span class="es-user-name">{{ $entry->user->name }}</span>
                                    </div>
                                    <div class="es-score">
                                        <span class="es-score-val">{{ $entry->best_score }}</span>
                                        <span class="es-score-total">/{{ $entry->total_points }}</span>
                                        @if($entry->earned_xp > 0)
                                            <span class="es-xp-badge">+{{ $entry->earned_xp }} XP</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="es-empty">
                                <i class="bi bi-trophy"></i>
                                <p>No one has completed this quiz yet. Be the first!</p>
                            </div>
                        @endif
                    </div>

                    {{-- Questions List --}}
                    <div class="es-card">
                        <div class="es-card-header">
                            <span class="es-section-title"><i class="bi bi-list-check me-2"></i>Questions ({{ $quiz->questions->count() }})</span>
                        </div>
                        @forelse($quiz->questions->sortBy('order') as $q)
                        <div class="es-question-row {{ !$loop->last ? 'es-question-row--border' : '' }}">
                            <div class="es-q-num">{{ $loop->iteration }}</div>
                            <div class="es-q-body">
                                <div class="es-q-text">{{ $q->question }}</div>
                                <div class="es-q-meta">
                                    <span class="es-q-type">{{ str_replace('_',' ',ucfirst($q->type)) }}</span>
                                    <span class="es-q-pts">{{ $q->points }} pt{{ $q->points != 1 ? 's' : '' }}</span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="es-empty">
                            <i class="bi bi-question-circle"></i>
                            <p>No questions added yet.</p>
                        </div>
                        @endforelse
                    </div>

                </div>

                {{-- RIGHT COLUMN --}}
                <div class="col-lg-4">

                    {{-- Student: Take quiz --}}
                    @if(Auth::user()->role === 'student' && $quiz->is_published)
                    @php $userAttempt = $quiz->attempts->where('user_id', Auth::id())->first(); @endphp
                    <div class="es-card es-take-card mb-4">
                        <i class="bi bi-pencil-square es-take-icon"></i>
                        <h6 class="es-take-title">Ready to take this quiz?</h6>
                        <p class="es-take-sub">You have {{ $quiz->duration_minutes }} minutes.</p>
                        @if($userAttempt)
                        <div class="es-attempt-info">
                            <i class="bi bi-info-circle me-1"></i>
                            Your best: <strong>{{ $userAttempt->score }}/{{ $userAttempt->total_points }}</strong>
                            @if($userAttempt->earned_xp > 0)
                                <span class="text-warning ms-1">(+{{ $userAttempt->earned_xp }} XP)</span>
                            @endif
                        </div>
                        @endif
                        @php $attemptsCount = $quiz->attempts->where('user_id', Auth::id())->count(); @endphp
                        @if($attemptsCount < $quiz->max_attempts)
                        <a href="{{ route('student.exams.take', $quiz) }}" class="es-btn es-btn-indigo w-100">
                            <i class="bi bi-play-fill me-2"></i>Start Quiz
                        </a>
                        <small class="text-muted d-block mt-2 text-center">{{ $attemptsCount }}/{{ $quiz->max_attempts }} attempts used</small>
                        @else
                        <button class="es-btn es-btn-disabled w-100" disabled>Max attempts reached</button>
                        @endif
                    </div>
                    @endif

                    {{-- Teacher: Statistics --}}
                    @if(Auth::user()->role === 'teacher')
                    <div class="es-card mb-4">
                        <div class="es-card-header">
                            <span class="es-section-title"><i class="bi bi-bar-chart-fill me-2"></i>Statistics</span>
                        </div>
                        <div class="es-stat-list">
                            <div class="es-stat-row">
                                <span>Total Attempts</span>
                                <strong>{{ $quiz->attempts->count() }}</strong>
                            </div>
                            <div class="es-stat-row">
                                <span>Participants</span>
                                <strong>{{ $quiz->total_participants }}</strong>
                            </div>
                            <div class="es-stat-row">
                                <span>Avg. Score</span>
                                <strong>{{ round($quiz->average_score, 1) }}</strong>
                            </div>
                            <div class="es-stat-row">
                                <span>Total Points</span>
                                <strong>{{ $quiz->questions->sum('points') }}</strong>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Course Info --}}
                    <div class="es-card">
                        <div class="es-card-header">
                            <span class="es-section-title"><i class="bi bi-book-fill me-2"></i>Course</span>
                        </div>
                        <div class="es-course-name">{{ $quiz->course->title ?? '—' }}</div>
                        <div class="es-course-cat">{{ $quiz->course->category->name ?? 'Uncategorized' }}</div>
                        @if(Auth::user()->role === 'teacher')
                        <a href="{{ route('teacher.courses.detail', $quiz->course_id) }}" class="es-btn es-btn-light w-100 mt-3">
                            <i class="bi bi-arrow-up-right-square me-1"></i>View Course
                        </a>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </main>
</div>
@endsection
