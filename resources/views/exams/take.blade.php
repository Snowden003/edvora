@extends('layouts.app')

@section('title', 'Taking Quiz: ' . $quiz->title . ' - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/quiz-take.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="dashboard-wrapper quiz-taking-mode">
    <x-student-sidebar />

    <main class="main-content quiz-main">
        <!-- Quiz Header -->
        <div class="quiz-header-sticky">
            <div class="quiz-header-content">
                <div class="quiz-header-left">
                    <a href="{{ route('student.exams.show', $quiz) }}" class="quiz-back-btn">
                        <i class="bi bi-x-lg"></i>
                    </a>
                    <div class="quiz-title-info">
                        <h5 class="quiz-title">{{ $quiz->title }}</h5>
                        <span class="quiz-course">{{ $quiz->course->title }}</span>
                    </div>
                </div>
                <div class="quiz-header-center">
                    @if($quiz->duration_minutes)
                    <div class="quiz-timer" id="quizTimer">
                        <i class="bi bi-clock-fill"></i>
                        <span id="timerDisplay">{{ sprintf('%02d:%02d', floor($quiz->duration_minutes), 0) }}:00</span>
                    </div>
                    @endif
                </div>
                <div class="quiz-header-right">
                    <div class="quiz-progress">
                        <span id="answeredCount">0</span> / <span id="totalCount">{{ $quiz->questions->count() }}</span>
                    </div>
                    <button type="submit" form="quizForm" class="quiz-submit-btn">
                        <i class="bi bi-check2-circle"></i>
                        Submit
                    </button>
                </div>
            </div>
            <div class="quiz-progress-bar">
                <div class="quiz-progress-fill" id="progressBar" style="width: 0%"></div>
            </div>
        </div>

        <!-- Quiz Content -->
        <div class="quiz-container">
            <form id="quizForm" action="{{ route('student.exams.submit', $quiz) }}" method="POST" class="quiz-form">
                @csrf

                <div class="quiz-questions-wrapper">
                    @foreach($quiz->questions->sortBy('order') as $index => $question)
                    <div class="quiz-question-card" data-question-id="{{ $question->id }}" data-question-type="{{ $question->type }}">
                        <div class="question-header">
                            <span class="question-number">Question {{ $index + 1 }}</span>
                            <span class="question-points">{{ $question->points }} pts</span>
                        </div>

                        <div class="question-content">
                            <h4 class="question-text">{!! nl2br(e($question->question)) !!}</h4>

                            @if($question->type === 'multiple_choice')
                                <div class="options-grid">
                                    @foreach($question->options ?? [] as $optionIndex => $option)
                                    <label class="option-card">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" class="option-input">
                                        <span class="option-letter">{{ chr(65 + $optionIndex) }}</span>
                                        <span class="option-text">{{ $option }}</span>
                                        <i class="bi bi-check-circle-fill option-check"></i>
                                    </label>
                                    @endforeach
                                </div>

                            @elseif($question->type === 'true_false')
                                <div class="true-false-options">
                                    <label class="tf-option tf-true">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="True" class="tf-input">
                                        <div class="tf-content">
                                            <i class="bi bi-check-lg"></i>
                                            <span>True</span>
                                        </div>
                                    </label>
                                    <label class="tf-option tf-false">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="False" class="tf-input">
                                        <div class="tf-content">
                                            <i class="bi bi-x-lg"></i>
                                            <span>False</span>
                                        </div>
                                    </label>
                                </div>

                            @elseif($question->type === 'fill_in_blank')
                                <div class="fill-blank-container">
                                    @php
                                        $blankCount = count($question->blank_answers ?? ['']);
                                    @endphp
                                    @for($i = 0; $i < $blankCount; $i++)
                                    <div class="blank-input-wrapper">
                                        <span class="blank-label">Blank {{ $i + 1 }}</span>
                                        <input type="text"
                                               name="answers[{{ $question->id }}][{{ $i }}]"
                                               class="blank-input"
                                               placeholder="Type your answer..."
                                               autocomplete="off">
                                    </div>
                                    @endfor
                                </div>

                            @else
                                <div class="short-answer-wrapper">
                                    <textarea name="answers[{{ $question->id }}]"
                                              class="short-answer-input"
                                              rows="4"
                                              placeholder="Write your answer here..."></textarea>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Submit Section -->
                <div class="quiz-submit-section">
                    <div class="submit-card">
                        <i class="bi bi-clipboard-check submit-icon"></i>
                        <h4>Ready to Submit?</h4>
                        <p>You've answered <strong id="finalAnsweredCount">0</strong> out of <strong>{{ $quiz->questions->count() }}</strong> questions.</p>
                        <div class="submit-actions">
                            <button type="submit" class="btn-submit-quiz">
                                <i class="bi bi-check2-all"></i>
                                Submit Quiz
                            </button>
                            <a href="{{ route('student.exams.show', $quiz) }}" class="btn-cancel-quiz">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const form = document.getElementById('quizForm');
    const progressBar = document.getElementById('progressBar');
    const answeredCount = document.getElementById('answeredCount');
    const finalAnsweredCount = document.getElementById('finalAnsweredCount');
    const totalCount = parseInt(document.getElementById('totalCount').textContent);
    const questionCards = document.querySelectorAll('.quiz-question-card');

    // Timer functionality
    @if($quiz->duration_minutes)
    let totalSeconds = {{ $quiz->duration_minutes }} * 60;
    const timerDisplay = document.getElementById('timerDisplay');
    const timerElement = document.getElementById('quizTimer');

    function updateTimer() {
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        if (hours > 0) {
            timerDisplay.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        } else {
            timerDisplay.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }

        // Warning colors
        if (totalSeconds <= 60) {
            timerElement.classList.add('timer-danger');
        } else if (totalSeconds <= 300) {
            timerElement.classList.add('timer-warning');
        }

        if (totalSeconds <= 0) {
            form.submit();
            return;
        }

        totalSeconds--;
    }

    setInterval(updateTimer, 1000);
    @endif

    // Update progress
    function updateProgress() {
        let answered = 0;

        questionCards.forEach(card => {
            const type = card.dataset.questionType;
            const questionId = card.dataset.questionId;
            let isAnswered = false;

            if (type === 'multiple_choice' || type === 'true_false') {
                const checked = card.querySelector('input[type="radio"]:checked');
                isAnswered = !!checked;
            } else if (type === 'fill_in_blank') {
                const inputs = card.querySelectorAll('.blank-input');
                isAnswered = Array.from(inputs).some(input => input.value.trim() !== '');
            } else {
                const textarea = card.querySelector('.short-answer-input');
                isAnswered = textarea && textarea.value.trim() !== '';
            }

            if (isAnswered) {
                answered++;
                card.classList.add('answered');
            } else {
                card.classList.remove('answered');
            }
        });

        const progress = (answered / totalCount) * 100;
        progressBar.style.width = progress + '%';
        answeredCount.textContent = answered;
        finalAnsweredCount.textContent = answered;
    }

    // Listen for changes
    form.addEventListener('change', updateProgress);
    form.addEventListener('input', updateProgress);

    // Initialize
    updateProgress();

    // Form submission confirmation
    form.addEventListener('submit', function(e) {
        const answered = parseInt(answeredCount.textContent);
        if (answered < totalCount) {
            const unanswered = totalCount - answered;
            if (!confirm(`You have ${unanswered} unanswered question(s). Are you sure you want to submit?`)) {
                e.preventDefault();
            }
        }
    });
})();
</script>
@endpush
