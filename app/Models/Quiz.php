<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'lesson_id',
        'title',
        'description',
        'duration_minutes',
        'xp_reward',
        'max_attempts',
        'is_published',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'xp_reward' => 'integer',
        'max_attempts' => 'integer',
        'is_published' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function getAverageScoreAttribute()
    {
        return $this->attempts()
            ->whereNotNull('completed_at')
            ->avg('score') ?? 0;
    }

    public function getTotalParticipantsAttribute()
    {
        return $this->attempts()
            ->whereNotNull('completed_at')
            ->distinct('user_id')
            ->count();
    }

    public function getStatusAttribute()
    {
        // Simple status: published = active, not published = draft
        if ($this->is_published) {
            return 'active';
        }

        return 'draft';
    }

    public function getLeaderboardAttribute()
    {
        // Get top 10 students who completed this quiz, ordered by score
        // Use subquery to get best attempt per user for MySQL strict mode compatibility
        return $this->attempts()
            ->with('user')
            ->whereNotNull('completed_at')
            ->select('quiz_attempts.*')
            ->join(
                DB::raw('(SELECT user_id, MAX(score) as max_score FROM quiz_attempts WHERE quiz_id = ? AND completed_at IS NOT NULL GROUP BY user_id) as best'),
                function ($join) {
                    $join->on('quiz_attempts.user_id', '=', 'best.user_id')
                         ->on('quiz_attempts.score', '=', 'best.max_score');
                }
            )
            ->setBindings([$this->id], 'join')
            ->orderByDesc('quiz_attempts.score')
            ->orderBy('quiz_attempts.completed_at')
            ->take(10)
            ->get()
            ->map(function ($attempt, $index) {
                $attempt->rank = $index + 1;
                $attempt->best_score = $attempt->score;
                return $attempt;
            });
    }
}
