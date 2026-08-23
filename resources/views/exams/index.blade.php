@extends('layouts.app')

@section('title', 'Quizzes & Practice Tests - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/exams.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <!-- Dashboard Layout Wrapper -->
    <div class="dashboard-wrapper">
        <!-- Sidebar Navigation -->
        @if(Auth::user()->role === 'teacher')
            <x-teacher-sidebar />
        @else
            <x-student-sidebar />
        @endif

        <!-- Main Dashboard Content -->
        <main class="main-content">
            <div class="container-fluid py-5">

                <!-- Quizzes Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
                    <div>
                        <h2 class="fw-bold mb-1 text-premium">Quizzes & Practice Tests</h2>
                        <p class="text-muted mb-0">Interactive exercises to reinforce learning.</p>
                    </div>
                    <div>
                        @if(auth()->user()->role === 'teacher')
                        <a href="{{ route('teacher.exams.create') }}" class="btn btn-premium-solid rounded-pill shadow-lg">
                            <i class="bi bi-plus-circle me-2"></i>Create New Quiz
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Stats Snippet -->
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-lg glass-card-premium stat-hover-premium h-100 quiz-stat-card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon-premium accent-purple">
                                        <i class="bi bi-clipboard-check-fill"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="fw-bold text-premium mb-0">{{ $totalQuizzes }}</h4>
                                        <p class="text-muted small mb-0">Total Quizzes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-lg glass-card-premium stat-hover-premium h-100 quiz-stat-card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon-premium accent-blue">
                                        <i class="bi bi-play-circle-fill"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="fw-bold text-premium mb-0">{{ $quizzes->where('is_published', true)->count() }}</h4>
                                        <p class="text-muted small mb-0">Published Quizzes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-lg glass-card-premium stat-hover-premium h-100 quiz-stat-card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon-premium accent-green">
                                        <i class="bi bi-trophy-fill"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="fw-bold text-premium mb-0">{{ round($avgCompletion) }}%</h4>
                                        <p class="text-muted small mb-0">Completion Rate</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="quizs-controls quiz-controls">
                    <div class="quizs-filters quiz-filters">
                        <button class="filter-btn active" data-filter="all">All Quizzes</button>
                        <button class="filter-btn" data-filter="active">Published</button>
                        <button class="filter-btn" data-filter="draft">Draft</button>
                    </div>
                    <div class="input-group quiz-search" style="max-width: 300px;">
                        <span class="input-group-text bg-white border-end-0 rounded-start-pill"><i
                                class="bi bi-search text-muted"></i></span>
                        <input type="text" id="quizSearch"
                            class="form-control border-start-0 rounded-end-pill shadow-none"
                            placeholder="Search quizzes...">
                    </div>
                </div>

                <!-- Quizs Grid -->
                <div class="row g-4" id="quizsGrid">
                    @forelse ($quizzes as $quiz)
                        <div class="col-xl-4 col-md-6 quiz-card-wrapper" data-status="{{ $quiz->status }}">
                            <div class="quiz-card status-{{ $quiz->status }} shadow-lg">
                                <div class="quiz-card-header">
                                    <div>
                                        <span class="quiz-course-tag">{{ $quiz->course->title }}</span>
                                        <h4 class="quiz-title">{{ $quiz->title }}</h4>
                                    </div>
                                    <div class="quiz-status-badge">
                                        @if($quiz->is_published)
                                            <i class="bi bi-play-circle-fill" style="color: #22c55e;"></i>
                                            <span style="color: #22c55e;">Published</span>
                                        @else
                                            <i class="bi bi-pencil-square" style="color: #f59e0b;"></i>
                                            <span style="color: #f59e0b;">Draft</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="quiz-card-body">
                                    <p class="text-muted small mb-3">{{ $quiz->description ?: 'No description available.' }}</p>

                                    <div class="quiz-details-grid">
                                        <div class="quiz-detail-item">
                                            <i class="bi bi-clock quiz-detail-icon"></i>
                                            <div>
                                                <span class="quiz-detail-label">Duration</span>
                                                <span class="quiz-detail-value">{{ $quiz->duration_minutes }} Minutes</span>
                                            </div>
                                        </div>
                                        <div class="quiz-detail-item">
                                            <i class="bi bi-stars quiz-detail-icon" style="color: #f59e0b;"></i>
                                            <div>
                                                <span class="quiz-detail-label">XP Reward</span>
                                                <span class="quiz-detail-value" style="color: #f59e0b; font-weight: 600;">+{{ $quiz->xp_reward }} XP</span>
                                            </div>
                                        </div>
                                        <div class="quiz-detail-item">
                                            <i class="bi bi-people quiz-detail-icon"></i>
                                            <div>
                                                <span class="quiz-detail-label">Participants</span>
                                                <span class="quiz-detail-value">{{ $quiz->total_participants }} Students</span>
                                            </div>
                                        </div>
                                        <div class="quiz-detail-item">
                                            <i class="bi bi-question-circle quiz-detail-icon"></i>
                                            <div>
                                                <span class="quiz-detail-label">Questions</span>
                                                <span class="quiz-detail-value">{{ $quiz->questions->count() }} Questions</span>
                                            </div>
                                        </div>
                                    </div>

                                    @if($quiz->total_participants > 0)
                                        <div class="result-progress-container mt-4">
                                            <div class="result-progress-text mb-1">
                                                <span class="fw-bold text-dark">Average Score: {{ round($quiz->average_score) }}%</span>
                                                <span class="text-muted">{{ $quiz->attempts->count() }} attempts</span>
                                            </div>
                                            <div class="result-progress-bar">
                                                <div class="result-progress-fill" style="width: {{ round($quiz->average_score) }}%;"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="quiz-card-footer">
                                    @if(auth()->user()->role === 'teacher')
                                        @if(!$quiz->is_published)
                                            <a href="{{ route('teacher.exams.edit', $quiz) }}" class="quiz-btn quiz-btn-secondary"><i class="bi bi-pencil"></i> Edit</a>
                                            <a href="{{ route('teacher.exams.questions', $quiz) }}" class="quiz-btn quiz-btn-primary"><i class="bi bi-question-circle"></i> Questions</a>
                                            <form action="{{ route('teacher.exams.destroy', $quiz) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this quiz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="quiz-btn quiz-btn-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        @else
                                            <a href="{{ route('teacher.exams.show', $quiz) }}" class="quiz-btn quiz-btn-primary"><i class="bi bi-bar-chart-fill"></i> Results</a>
                                            <form action="{{ route('teacher.exams.destroy', $quiz) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this quiz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="quiz-btn quiz-btn-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        @endif
                                    @else
                                        <a href="{{ route('student.exams.show', $quiz) }}" class="quiz-btn quiz-btn-primary"><i class="bi bi-eye"></i> View Quiz</a>
                                        @if($quiz->is_published)
                                            <a href="{{ route('student.exams.take', $quiz) }}" class="quiz-btn quiz-btn-secondary"><i class="bi bi-play-fill"></i> Start Quiz</a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5 quiz-empty-state">
                                <div class="quiz-empty-icon">
                                    <i class="bi bi-clipboard-check"></i>
                                </div>
                                <h4 class="text-muted mt-3">No quizzes yet</h4>
                                <p class="text-muted">Create your first quiz to get started!</p>
                                @if(auth()->user()->role === 'teacher')
                                <a href="{{ route('teacher.exams.create') }}" class="btn btn-premium-solid mt-3">
                                    <i class="bi bi-plus-circle me-2"></i>Create Quiz
                                </a>
                                @endif
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </main>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/teacher-dashboard.js') }}"></script>
<script src="{{ asset('assets/js/modern-footer.js') }}"></script>
<script src="{{ asset('assets/js/pages/exams.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
<script>
// Simple filter for Published/Draft quizzes
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const quizCards = document.querySelectorAll('.quiz-card-wrapper');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');

            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            quizCards.forEach(card => {
                const status = card.getAttribute('data-status');

                if (filter === 'all' || status === filter) {
                    card.style.display = 'block';
                    setTimeout(() => card.style.opacity = '1', 10);
                } else {
                    card.style.opacity = '0';
                    setTimeout(() => card.style.display = 'none', 300);
                }
            });
        });
    });
});
</script>
@endpush
