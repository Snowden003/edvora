@extends('layouts.app')

@section('title', 'Edit Quiz - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/exams.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
@endpush

@section('content')
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
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-premium">Edit Quiz</h2>
                    <p class="text-muted mb-0">Update quiz settings and information.</p>
                </div>
                <a href="{{ route('teacher.exams.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Quizzes
                </a>
            </div>

            <!-- Edit Exam Form -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-4">
                            <form action="{{ route('teacher.exams.update', $quiz) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <!-- Course Selection -->
                                <div class="mb-4">
                                    <label for="course_id" class="form-label fw-semibold">Select Course</label>
                                    <select class="form-select" id="course_id" name="course_id" required>
                                        <option value="">Choose a course...</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id', $quiz->course_id) == $course->id ? 'selected' : '' }}>
                                                {{ $course->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Quiz Title -->
                                <div class="mb-4">
                                    <label for="title" class="form-label fw-semibold">Quiz Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                           value="{{ old('title', $quiz->title) }}" required placeholder="e.g., Chapter 3 Review Quiz">
                                    @error('title')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <label for="description" class="form-label fw-semibold">Description</label>
                                    <textarea class="form-control" id="description" name="description"
                                              rows="3" placeholder="What topics does this quiz cover? Any instructions for students?">{{ old('description', $quiz->description) }}</textarea>
                                    @error('description')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Quiz Settings -->
                                <div class="quiz-section-header">
                                    <i class="bi bi-gear-fill"></i>
                                    <span>Quiz Settings</span>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label for="duration_minutes" class="form-label fw-semibold">
                                            <i class="bi bi-clock me-1 text-primary"></i>Time Limit (Optional)
                                        </label>
                                        <input type="number" class="form-control" id="duration_minutes"
                                               name="duration_minutes" value="{{ old('duration_minutes', $quiz->duration_minutes) }}" min="1" placeholder="e.g., 30">
                                        <small class="text-muted">Leave empty for untimed quiz</small>
                                        @error('duration_minutes')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="xp_reward" class="form-label fw-semibold">
                                            <i class="bi bi-stars me-1 text-warning"></i>XP Reward
                                        </label>
                                        <input type="number" class="form-control" id="xp_reward"
                                               name="xp_reward" value="{{ old('xp_reward', $quiz->xp_reward ?? 50) }}" min="0" max="1000" required>
                                        <small class="text-muted">XP earned for completing (0-1000)</small>
                                        @error('xp_reward')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="max_attempts" class="form-label fw-semibold">
                                            <i class="bi bi-arrow-repeat me-1 text-warning"></i>Max Attempts
                                        </label>
                                        <input type="number" class="form-control" id="max_attempts"
                                               name="max_attempts" value="{{ old('max_attempts', $quiz->max_attempts ?? 1) }}" min="1" required>
                                        <small class="text-muted">How many times can retake</small>
                                        @error('max_attempts')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-premium-solid btn-lg">
                                        <i class="bi bi-check-circle me-2"></i>Update Quiz
                                    </button>
                                    <a href="{{ route('teacher.exams.questions', $quiz) }}" class="btn btn-outline-primary btn-lg">
                                        <i class="bi bi-question-circle me-2"></i>Manage Questions
                                    </a>
                                    <a href="{{ route('teacher.exams.index') }}" class="btn btn-outline-secondary btn-lg">
                                        Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-lg mb-4 quiz-info-card">
                        <div class="card-body p-4">
                            <h5 class="card-title fw-bold mb-3">
                                <i class="bi bi-info-circle-fill text-primary me-2"></i>Quiz Status
                            </h5>
                            <div class="mb-3">
                                <span class="badge {{ $quiz->is_published ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-2 fs-6">
                                    {{ $quiz->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </div>
                            <p class="text-muted small">
                                Questions Count: <strong>{{ $quiz->questions->count() }}</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
