@extends('layouts.app')

@section('title', 'Create New Quiz - Edvora Tech')

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
                    <h2 class="fw-bold text-premium">Create New Quiz</h2>
                    <p class="text-muted mb-0">Design an interactive quiz to test your students' knowledge.</p>
                </div>
                <a href="{{ route('teacher.exams.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Quizzes
                </a>
            </div>

            <!-- Create Exam Form -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-4">
                            <form action="{{ route('teacher.exams.store') }}" method="POST">
                                @csrf
                                
                                <!-- Course Selection -->
                                <div class="mb-4">
                                    <label for="course_id" class="form-label fw-semibold">Select Course</label>
                                    <select class="form-select" id="course_id" name="course_id" required>
                                        <option value="">Choose a course...</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}">{{ $course->title }}</option>
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
                                           value="{{ old('title') }}" required placeholder="e.g., Chapter 3 Review Quiz">
                                    @error('title')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <label for="description" class="form-label fw-semibold">Description</label>
                                    <textarea class="form-control" id="description" name="description"
                                              rows="3" placeholder="What topics does this quiz cover? Any instructions for students?">{{ old('description') }}</textarea>
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
                                               name="duration_minutes" value="{{ old('duration_minutes', '') }}" min="1" placeholder="e.g., 30">
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
                                               name="xp_reward" value="{{ old('xp_reward', 50) }}" min="0" max="1000" required>
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
                                               name="max_attempts" value="{{ old('max_attempts', 1) }}" min="1" required>
                                        <small class="text-muted">How many times can retake</small>
                                        @error('max_attempts')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-premium-solid btn-lg">
                                        <i class="bi bi-plus-circle me-2"></i>Create Quiz
                                    </button>
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
                                <i class="bi bi-info-circle-fill text-primary me-2"></i>Quiz Guidelines
                            </h5>
                            <ul class="small text-muted quiz-guidelines">
                                <li class="mb-2"><i class="bi bi-check2 text-primary me-1"></i>Quizzes are perfect for chapter reviews and practice tests</li>
                                <li class="mb-2"><i class="bi bi-check2 text-primary me-1"></i>Make time limits optional for untimed practice</li>
                                <li class="mb-2"><i class="bi bi-check2 text-primary me-1"></i>Set XP rewards to motivate students (50-200 recommended)</li>
                                <li class="mb-2"><i class="bi bi-check2 text-primary me-1"></i>Use fill-in-blank for vocabulary and key concepts</li>
                                <li><i class="bi bi-check2 text-primary me-1"></i>After creating, add questions and publish</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card border-0 shadow-lg quiz-tip-card">
                        <div class="card-body p-4">
                            <h5 class="card-title fw-bold mb-3">
                                <i class="bi bi-lightbulb-fill text-warning me-2"></i>Question Types
                            </h5>
                            <div class="question-type-list">
                                <div class="q-type-item">
                                    <i class="bi bi-list-ul text-blue"></i>
                                    <span><strong>Multiple Choice:</strong> Best for concept testing</span>
                                </div>
                                <div class="q-type-item">
                                    <i class="bi bi-check-square text-green"></i>
                                    <span><strong>True/False:</strong> Quick knowledge checks</span>
                                </div>
                                <div class="q-type-item">
                                    <i class="bi bi-input-cursor text-purple"></i>
                                    <span><strong>Fill in Blank:</strong> Vocabulary & formulas</span>
                                </div>
                                <div class="q-type-item">
                                    <i class="bi bi-text-paragraph text-gray"></i>
                                    <span><strong>Short Answer:</strong> Detailed responses</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum datetime to current time
    const now = new Date();
    const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
        .toISOString()
        .slice(0, 16);
    
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    
    startTimeInput.min = localDateTime;
    endTimeInput.min = localDateTime;
    
    // Update end time minimum when start time changes
    startTimeInput.addEventListener('change', function() {
        endTimeInput.min = this.value;
        if (endTimeInput.value && endTimeInput.value <= this.value) {
            const endTime = new Date(this.value);
            endTime.setHours(endTime.getHours() + 1);
            endTimeInput.value = endTime.toISOString().slice(0, 16);
        }
    });
});
</script>
@endpush
