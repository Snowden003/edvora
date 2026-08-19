<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question',
        'type',
        'options',
        'blank_positions',
        'blank_answers',
        'correct_answer',
        'points',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'blank_positions' => 'array',
        'blank_answers' => 'array',
        'points' => 'integer',
        'order' => 'integer',
    ];

    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    public const TYPE_TRUE_FALSE = 'true_false';
    public const TYPE_SHORT_ANSWER = 'short_answer';
    public const TYPE_FILL_IN_BLANK = 'fill_in_blank';

    public static function getTypes(): array
    {
        return [
            self::TYPE_MULTIPLE_CHOICE => 'Multiple Choice',
            self::TYPE_TRUE_FALSE => 'True / False',
            self::TYPE_SHORT_ANSWER => 'Short Answer',
            self::TYPE_FILL_IN_BLANK => 'Fill in the Blank (خانه خالی)',
        ];
    }

    public function isFillInBlank(): bool
    {
        return $this->type === self::TYPE_FILL_IN_BLANK;
    }

    public function getQuestionWithBlanks(): string
    {
        if (!$this->isFillInBlank()) {
            return $this->question;
        }

        $question = $this->question;
        $positions = $this->blank_positions ?? [];
        
        // Replace [blank] or ___ placeholders with input fields
        $question = preg_replace('/\[blank\]|___+|\.{3,}/', '<input type="text" class="quiz-blank-input" placeholder="...">', $question);
        
        return $question;
    }

    public function getBlankCount(): int
    {
        if (!$this->isFillInBlank()) {
            return 0;
        }

        $answers = $this->blank_answers ?? [];
        return count($answers);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
