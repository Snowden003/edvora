<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizAttempt;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Activity;
use App\Services\ScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'teacher') {
            // For teachers, show quizzes from their courses
            $quizzes = Quiz::with(['course', 'attempts'])
                ->whereHas('course', function($query) use ($user) {
                    $query->where('teacher_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // For students, show only published quizzes from courses they're enrolled in
            $quizzes = Quiz::with(['course', 'attempts'])
                ->where('is_published', true)
                ->whereHas('course', function($query) use ($user) {
                    $query->whereHas('enrollments', function($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id);
                    });
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Calculate statistics
        $totalQuizzes = $quizzes->count();
        $upcomingQuizzes = $quizzes->filter(function($quiz) {
            return $quiz->status === 'upcoming';
        })->count();
        
        $avgCompletion = 0;
        if ($totalQuizzes > 0) {
            $completedQuizzes = $quizzes->filter(function($quiz) {
                return $quiz->status === 'completed';
            });
            
            if ($completedQuizzes->count() > 0) {
                $totalCompletionRate = $completedQuizzes->sum(function($quiz) {
                    $totalStudents = $quiz->course->enrollments()->count();
                    $completedStudents = $quiz->total_participants;
                    return $totalStudents > 0 ? ($completedStudents / $totalStudents) * 100 : 0;
                });
                $avgCompletion = $totalCompletionRate / $completedQuizzes->count();
            }
        }

        return view('exams', compact('quizzes', 'totalQuizzes', 'avgCompletion'));
    }

    public function show(Quiz $quiz)
    {
        $quiz->load(['questions', 'course', 'attempts' => function($query) {
            $query->where('user_id', Auth::id());
        }]);

        return view('exams.show', compact('quiz'));
    }

    public function take(Quiz $quiz)
    {
        // Check if user is enrolled in the course
        $isEnrolled = $quiz->course->enrollments()
            ->where('user_id', Auth::id())
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'You are not enrolled in this course');
        }

        $quiz->load('questions');

        return view('exams.take', compact('quiz'));
    }

    public function create()
    {
        $user = Auth::user();
        
        if ($user->role !== 'teacher') {
            abort(403, 'Only teachers can create exams');
        }

        $courses = Course::where('teacher_id', $user->id)->get();
        
        return view('exams.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'xp_reward' => 'required|integer|min:0|max:1000',
            'max_attempts' => 'required|integer|min:1',
            'lesson_id' => 'nullable|exists:lessons,id',
        ]);

        $quiz = Quiz::create([
            'course_id' => $request->course_id,
            'lesson_id' => $request->lesson_id,
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes ?? 60,
            'xp_reward' => $request->xp_reward,
            'max_attempts' => $request->max_attempts,
            'is_published' => false,
        ]);

        return redirect()->route('teacher.exams.questions', $quiz)
            ->with('success', 'Quiz created successfully! Now add questions.');
    }

    public function edit(Quiz $quiz)
    {
        $user = Auth::user();
        
        if ($user->role !== 'teacher' || $quiz->course->teacher_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $courses = Course::where('teacher_id', $user->id)->get();
        
        return view('exams.edit', compact('quiz', 'courses'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $user = Auth::user();
        
        if ($user->role !== 'teacher' || $quiz->course->teacher_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'xp_reward' => 'required|integer|min:0|max:1000',
            'max_attempts' => 'required|integer|min:1',
        ]);

        $quiz->update($request->only(['title', 'description', 'duration_minutes', 'xp_reward', 'max_attempts']));

        return redirect()->route('teacher.exams.show', $quiz)
            ->with('success', 'Exam updated successfully!');
    }

    public function questions(Quiz $quiz)
    {
        $user = Auth::user();
        
        if ($user->role !== 'teacher' || $quiz->course->teacher_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $quiz->load('questions');

        return view('exams.questions', compact('quiz'));
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $user = Auth::user();
        
        if ($user->role !== 'teacher' || $quiz->course->teacher_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,true_false,short_answer,fill_in_blank',
            'options' => 'sometimes|required_if:type,multiple_choice|array',
            'blank_answers' => 'sometimes|required_if:type,fill_in_blank|array',
            'correct_answer' => 'nullable|string',
        ]);

        // Filter out empty options
        $options = null;
        if ($request->type === 'multiple_choice' && $request->has('options')) {
            $options = array_filter($request->options, fn($opt) => !empty($opt));
        }

        // Filter out empty blank answers
        $blankAnswers = null;
        if ($request->type === 'fill_in_blank' && $request->has('blank_answers')) {
            $blankAnswers = array_filter($request->blank_answers, fn($ans) => !empty($ans));
        }

        // For fill_in_blank, use first blank answer as correct_answer if not provided
        $correctAnswer = $request->correct_answer;
        if ($request->type === 'fill_in_blank' && empty($correctAnswer) && !empty($blankAnswers)) {
            $correctAnswer = $blankAnswers[0] ?? null;
        }

        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => $request->question,
            'type' => $request->type,
            'options' => $options,
            'blank_answers' => $blankAnswers,
            'correct_answer' => $correctAnswer,
            'points' => 1,
            'order' => $quiz->questions()->count() + 1,
        ]);

        return redirect()->route('teacher.exams.questions', $quiz)
            ->with('success', 'Question added successfully!');
    }

    public function publish(Quiz $quiz)
    {
        $user = Auth::user();
        
        if ($user->role !== 'teacher' || $quiz->course->teacher_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if ($quiz->questions()->count() === 0) {
            return redirect()->route('teacher.exams.questions', $quiz)
                ->with('error', 'You must add at least one question before publishing.');
        }

        $quiz->update(['is_published' => true]);

        // Notify enrolled students
        $course = $quiz->course;
        $enrolledUserIds = $course->enrollments()->where('status', 'active')->pluck('user_id');
        foreach ($enrolledUserIds as $studentId) {
            NotificationController::notifyExamPublished($studentId, $course->title, $quiz->title, $quiz->id, $course->slug);
        }

        return redirect()->route('teacher.exams.show', $quiz)
            ->with('success', 'Quiz published successfully!');
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        $answers = $request->answers;
        $score = 0;
        $totalPoints = $quiz->questions->sum('points');

        foreach ($quiz->questions as $question) {
            if (!isset($answers[$question->id])) {
                continue;
            }

            $userAnswer = $answers[$question->id];

            if ($question->type === 'fill_in_blank') {
                // Check fill in the blank answers
                $correctBlanks = $question->blank_answers ?? [];
                $userBlanks = is_array($userAnswer) ? $userAnswer : [$userAnswer];

                $correctCount = 0;
                foreach ($correctBlanks as $index => $correctBlank) {
                    if (isset($userBlanks[$index])) {
                        // Case-insensitive comparison, trimmed
                        if (strcasecmp(trim($userBlanks[$index]), trim($correctBlank)) === 0) {
                            $correctCount++;
                        }
                    }
                }

                // Award partial points based on correct blanks
                if (count($correctBlanks) > 0) {
                    $blankPoints = $question->points / count($correctBlanks);
                    $score += $correctCount * $blankPoints;
                }
            } elseif ($question->type === 'short_answer') {
                // For short answer, check if the answer contains key words (simple implementation)
                // Teacher can manually review later
                // For now, mark as correct if any answer provided
                if (!empty($userAnswer)) {
                    $score += $question->points;
                }
            } else {
                // Multiple choice and true/false - exact match
                if ($userAnswer === $question->correct_answer) {
                    $score += $question->points;
                }
            }
        }

        // Award XP based on completion (if student answered at least one question correctly)
        $earnedXP = 0;
        if ($score > 0 && $quiz->xp_reward > 0) {
            // Calculate percentage score and award proportional XP
            $percentage = ($score / $totalPoints) * 100;
            if ($percentage >= 50) {
                $earnedXP = $quiz->xp_reward;
            } else {
                // Partial XP for scores below 50%
                $earnedXP = (int)($quiz->xp_reward * ($percentage / 50));
            }

            // Add XP to user's profile through the scoring system
            $user = Auth::user();
            if ($user && $earnedXP > 0) {
                ScoreService::adjust(
                    user: $user,
                    amount: $earnedXP,
                    type: 'quiz',
                    reason: "Completed quiz: {$quiz->title}",
                    courseId: $quiz->course_id,
                    createdBy: $user,
                    related: ['type' => Quiz::class, 'id' => $quiz->id],
                    notify: true
                );
            }
        }

        $attempt = QuizAttempt::create([
            'user_id' => Auth::id(),
            'quiz_id' => $quiz->id,
            'answers' => $answers,
            'score' => $score,
            'total_points' => $totalPoints,
            'earned_xp' => $earnedXP,
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        // Log activity for streak (only once per day)
        $user = Auth::user();
        $todayActivity = $user->activities()
            ->whereDate('created_at', now()->toDateString())
            ->first();

        if (!$todayActivity) {
            $user->activities()->create([
                'type'    => 'quiz_completed',
                'icon'    => 'bi-check-circle',
                'message' => "Completed quiz: {$quiz->title} (+{$earnedXP} XP)",
                'color'   => $earnedXP > 0 ? '#22c55e' : '#f59e0b',
            ]);
        }

        return redirect()->route('student.exams.show', $quiz)
            ->with('success', "Quiz completed! You earned {$earnedXP} XP!");
    }

    public function destroy(Quiz $quiz)
    {
        $user = Auth::user();

        if ($user->role !== 'teacher' || $quiz->course->teacher_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Delete related attempts and questions first
        $quiz->attempts()->delete();
        $quiz->questions()->delete();
        $quiz->delete();

        return redirect()->route('teacher.exams.index')
            ->with('success', 'Quiz deleted successfully!');
    }
}
