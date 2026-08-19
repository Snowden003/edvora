@extends('layouts.app')

@section('title', 'Manage Questions - ' . $quiz->title . ' - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/exams.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/quiz-questions.css') }}" rel="stylesheet" />
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
                    <h2 class="fw-bold text-premium mb-1">Manage Questions</h2>
                    <p class="text-muted mb-0">Exam: {{ $quiz->title }} ({{ $quiz->course->title }})</p>
                </div>
                <div class="d-flex gap-2">
                    @if($quiz->questions->count() > 0)
                        @if(!$quiz->is_published)
                            <form action="{{ route('teacher.exams.publish', $quiz) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-2"></i>Publish Quiz
                                </button>
                            </form>
                        @else
                            <span class="badge bg-success fs-6">
                                <i class="bi bi-check-circle-fill me-1"></i>Published
                            </span>
                        @endif
                    @endif
                    <a href="{{ route('teacher.exams.show', $quiz) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Exam
                    </a>
                </div>
            </div>

            <!-- Exam Info -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Duration</small>
                            <p class="fw-bold mb-0">{{ $quiz->duration_minutes }} minutes</p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">XP Reward</small>
                            <p class="fw-bold mb-0 text-warning">+{{ $quiz->xp_reward }} XP</p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Status</small>
                            <p class="fw-bold mb-0">
                                @if($quiz->is_published)
                                    <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Published</span>
                                @else
                                    <span class="text-muted"><i class="bi bi-draft me-1"></i>Draft</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Add Question Form -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-lg">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-plus-circle me-2"></i>Add New Question
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('teacher.exams.questions.store', $quiz) }}" method="POST">
                                @csrf
                                
                                <!-- Question Type -->
                                <div class="mb-3">
                                    <label for="type" class="form-label fw-semibold">Question Type</label>
                                    <select class="form-select" id="type" name="type" required onchange="toggleQuestionOptions()">
                                        <option value="multiple_choice">Multiple Choice</option>
                                        <option value="true_false">True / False</option>
                                        <option value="short_answer">Short Answer</option>
                                        <option value="fill_in_blank">Fill in the Blank</option>
                                    </select>
                                </div>

                                <!-- Question Text -->
                                <div class="mb-3">
                                    <label for="question" class="form-label fw-semibold">Question</label>
                                    <textarea class="form-control" id="question" name="question" 
                                              rows="3" required placeholder="Enter your question here..."></textarea>
                                    @error('question')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Multiple Choice Options -->
                                <div id="multipleChoiceOptions" class="mb-3">
                                    <label class="form-label fw-semibold">Answer Options</label>
                                    <div class="options-container">
                                        <div class="option-input-row">
                                            <span class="option-label">A</span>
                                            <input type="text" class="form-control" name="options[]"
                                                   placeholder="Enter option A" data-required="true">
                                        </div>
                                        <div class="option-input-row">
                                            <span class="option-label">B</span>
                                            <input type="text" class="form-control" name="options[]"
                                                   placeholder="Enter option B" data-required="true">
                                        </div>
                                        <div class="option-input-row">
                                            <span class="option-label">C</span>
                                            <input type="text" class="form-control" name="options[]"
                                                   placeholder="Enter option C (optional)">
                                        </div>
                                        <div class="option-input-row">
                                            <span class="option-label">D</span>
                                            <input type="text" class="form-control" name="options[]"
                                                   placeholder="Enter option D (optional)">
                                        </div>
                                    </div>
                                </div>

                                <!-- Fill in the Blank Options -->
                                <div id="fillInBlankOptions" class="mb-3" style="display: none;">
                                    <div class="fib-help-box">
                                        <i class="bi bi-info-circle-fill"></i>
                                        <div>
                                            <strong>Tip:</strong> Use <code>[blank]</code> or <code>___</code> in your question text to mark where blanks should appear.
                                            Add the correct answers below.
                                        </div>
                                    </div>
                                    <div id="blankAnswersContainer">
                                        <div class="blank-answer-row">
                                            <span class="blank-label">Blank 1</span>
                                            <input type="text" class="form-control" name="blank_answers[]"
                                                   placeholder="Correct answer for blank 1" required>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addBlankAnswer()">
                                        <i class="bi bi-plus-circle me-1"></i>Add Another Blank
                                    </button>
                                </div>

                                <!-- Correct Answer -->
                                <div class="mb-3" id="correctAnswerSection">
                                    <label for="correct_answer" class="form-label fw-semibold">Correct Answer</label>
                                    <input type="text" class="form-control" id="correct_answer"
                                           name="correct_answer" placeholder="Enter the correct answer">
                                    <small class="text-muted" id="correctAnswerHint">For multiple choice, enter the exact option text (e.g., 'Option A')</small>
                                    @error('correct_answer')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-premium-solid w-100">
                                    <i class="bi bi-plus-circle me-2"></i>Add Question
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Questions List -->
                <div class="col-lg-7">
                    <div class="eq-card">
                        {{-- Header --}}
                        <div class="eq-card-header">
                            <div class="eq-header-left">
                                <div class="eq-header-icon">
                                    <i class="bi bi-list-check"></i>
                                </div>
                                <div>
                                    <div class="eq-header-title">Questions</div>
                                    <div class="eq-header-sub">{{ $quiz->questions->count() }} questions total</div>
                                </div>
                            </div>
                        </div>

                        {{-- Body --}}
                        @if($quiz->questions->count() > 0)
                        <div class="eq-table-wrap">
                            <table class="eq-table">
                                <thead>
                                    <tr>
                                        <th style="width:44px;">#</th>
                                        <th>Question</th>
                                        <th>Type</th>
                                        <th style="width:80px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quiz->questions->sortBy('order') as $question)
                                    <tr>
                                        <td><span class="eq-q-num">{{ $question->order }}</span></td>
                                        <td>
                                            <div class="eq-q-text" title="{{ $question->question }}">
                                                {{ $question->question }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($question->type === 'multiple_choice')
                                                <span class="eq-badge eq-badge-blue"><i class="bi bi-list-ul me-1"></i>Multiple Choice</span>
                                            @elseif($question->type === 'true_false')
                                                <span class="eq-badge eq-badge-green"><i class="bi bi-check-square me-1"></i>True/False</span>
                                            @elseif($question->type === 'fill_in_blank')
                                                <span class="eq-badge eq-badge-purple"><i class="bi bi-input-cursor me-1"></i>Fill in Blank</span>
                                            @else
                                                <span class="eq-badge eq-badge-gray"><i class="bi bi-text-paragraph me-1"></i>Short Answer</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="eq-actions">
                                                <button class="eq-action-btn eq-edit" title="Edit"><i class="bi bi-pencil"></i></button>
                                                <button class="eq-action-btn eq-delete" title="Delete"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="eq-empty">
                            <i class="bi bi-question-circle"></i>
                            <p>No questions yet. Add your first question.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
let blankCount = 1;

function toggleQuestionOptions() {
    const type = document.getElementById('type').value;
    const multipleChoiceOptions = document.getElementById('multipleChoiceOptions');
    const fillInBlankOptions = document.getElementById('fillInBlankOptions');
    const correctAnswerSection = document.getElementById('correctAnswerSection');
    const correctAnswerInput = document.getElementById('correct_answer');
    const correctAnswerHint = document.getElementById('correctAnswerHint');
    const optionInputs = multipleChoiceOptions.querySelectorAll('input[name="options[]"]');
    const blankInputs = document.querySelectorAll('input[name="blank_answers[]"]');

    // Hide all option sections first
    multipleChoiceOptions.style.display = 'none';
    fillInBlankOptions.style.display = 'none';

    // Remove required from all dynamic inputs
    optionInputs.forEach(input => input.removeAttribute('required'));
    blankInputs.forEach(input => input.removeAttribute('required'));

    if (type === 'multiple_choice') {
        multipleChoiceOptions.style.display = 'block';
        correctAnswerSection.style.display = 'block';
        correctAnswerInput.placeholder = 'Enter the exact option text (e.g., "Option A")';
        correctAnswerHint.textContent = 'For multiple choice, enter the exact option text (e.g., "Option A")';
        // Make options required
        optionInputs.forEach((input, index) => {
            if (index < 2) input.setAttribute('required', '');
        });
    } else if (type === 'true_false') {
        correctAnswerSection.style.display = 'block';
        correctAnswerInput.placeholder = 'Enter "True" or "False"';
        correctAnswerHint.textContent = 'Enter exactly "True" or "False"';
    } else if (type === 'fill_in_blank') {
        fillInBlankOptions.style.display = 'block';
        correctAnswerSection.style.display = 'none';
        // Make blank answers required
        blankInputs.forEach(input => input.setAttribute('required', ''));
    } else {
        // short_answer
        correctAnswerSection.style.display = 'block';
        correctAnswerInput.placeholder = 'Enter the correct answer (for reference)';
        correctAnswerHint.textContent = 'This is for teacher reference only';
    }
}

function addBlankAnswer() {
    blankCount++;
    const container = document.getElementById('blankAnswersContainer');
    const newRow = document.createElement('div');
    newRow.className = 'blank-answer-row';
    newRow.innerHTML = `
        <span class="blank-label">Blank ${blankCount}</span>
        <div class="input-group">
            <input type="text" class="form-control" name="blank_answers[]"
                   placeholder="Correct answer for blank ${blankCount}" required>
            <button type="button" class="btn btn-outline-danger" onclick="removeBlankAnswer(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newRow);
}

function removeBlankAnswer(button) {
    if (blankCount <= 1) {
        alert('You must have at least one blank.');
        return;
    }
    button.closest('.blank-answer-row').remove();
    blankCount--;
    // Re-number the blanks
    document.querySelectorAll('.blank-answer-row .blank-label').forEach((label, index) => {
        label.textContent = `Blank ${index + 1}`;
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleQuestionOptions();
});
</script>

@endpush
