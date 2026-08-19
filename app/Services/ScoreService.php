<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Point;
use App\Models\ScoringRule;
use App\Models\User;

class ScoreService
{
    /**
     * Default scoring rules seeded if missing.
     */
    public static function defaultRules(): array
    {
        return [
            [
                'action_name' => 'attendance_on_time',
                'label' => 'On-time Attendance',
                'type' => 'attendance',
                'default_score' => 10,
                'description' => 'Awarded when a student attends class on time.',
            ],
            [
                'action_name' => 'attendance_late',
                'label' => 'Late Attendance',
                'type' => 'attendance',
                'default_score' => -5,
                'description' => 'Deducted when a student arrives late to class.',
            ],
            [
                'action_name' => 'attendance_absent',
                'label' => 'Absence',
                'type' => 'attendance',
                'default_score' => -15,
                'description' => 'Deducted when a student is absent from class.',
            ],
            [
                'action_name' => 'assignment_completed',
                'label' => 'Assignment Completed',
                'type' => 'assignment',
                'default_score' => 20,
                'description' => 'Awarded when a student completes an assignment.',
            ],
            [
                'action_name' => 'assignment_not_completed',
                'label' => 'Assignment Not Completed',
                'type' => 'assignment',
                'default_score' => -10,
                'description' => 'Deducted when a student fails to complete an assignment.',
            ],
            [
                'action_name' => 'participation',
                'label' => 'Active Participation',
                'type' => 'participation',
                'default_score' => 5,
                'description' => 'Awarded for active participation during class.',
            ],
            [
                'action_name' => 'manual',
                'label' => 'Manual Adjustment',
                'type' => 'manual',
                'default_score' => 0,
                'description' => 'Instructor-defined point adjustment with custom reason.',
            ],
        ];
    }

    /**
     * Seed default scoring rules if they do not already exist.
     */
    public static function seedDefaultRules(): void
    {
        foreach (self::defaultRules() as $rule) {
            ScoringRule::firstOrCreate(
                ['action_name' => $rule['action_name']],
                $rule
            );
        }
    }

    /**
     * Find a scoring rule or create it from defaults on the fly.
     */
    public static function getOrCreateRule(string $actionName): ScoringRule
    {
        $rule = ScoringRule::where('action_name', $actionName)->first();
        if ($rule) {
            return $rule;
        }

        $default = collect(self::defaultRules())->firstWhere('action_name', $actionName);
        if ($default) {
            return ScoringRule::create($default);
        }

        return ScoringRule::create([
            'action_name' => $actionName,
            'label' => ucwords(str_replace('_', ' ', $actionName)),
            'type' => 'manual',
            'default_score' => 0,
            'description' => 'Auto-generated rule.',
        ]);
    }

    /**
     * Award points using a scoring rule action.
     */
    public static function award(
        User $user,
        string $actionName,
        ?string $reason = null,
        ?int $courseId = null,
        ?User $createdBy = null,
        ?array $related = null,
        bool $notify = true
    ): Point {
        $rule = self::getOrCreateRule($actionName);
        $amount = $rule->default_score;

        return self::adjust(
            user: $user,
            amount: $amount,
            type: $rule->action_name,
            reason: $reason ?? $rule->label,
            courseId: $courseId,
            createdBy: $createdBy,
            related: $related,
            notify: $notify
        );
    }

    /**
     * Manually adjust points (positive or negative).
     */
    public static function adjust(
        User $user,
        int $amount,
        string $type = 'manual',
        ?string $reason = null,
        ?int $courseId = null,
        ?User $createdBy = null,
        ?array $related = null,
        bool $notify = true
    ): Point {
        $point = Point::create([
            'user_id' => $user->id,
            'course_id' => $courseId,
            'amount' => $amount,
            'reason' => $reason,
            'type' => $type,
            'related_type' => $related['type'] ?? null,
            'related_id' => $related['id'] ?? null,
            'created_by' => $createdBy?->id,
        ]);

        // Sync the legacy xp column so existing leaderboard code keeps working.
        $totalScore = $user->totalScore();
        $user->update(['xp' => max(0, $totalScore)]);

        // Log activity for transparency.
        $user->activities()->create([
            'type' => $amount >= 0 ? 'points_earned' : 'points_deducted',
            'icon' => $amount >= 0 ? 'bi-plus-circle' : 'bi-dash-circle',
            'message' => sprintf(
                '%s %d points: %s',
                $amount >= 0 ? 'Earned' : 'Deducted',
                abs($amount),
                $reason ?? ucfirst(str_replace('_', ' ', $type))
            ),
            'color' => $amount >= 0 ? '#22c55e' : '#ef4444',
        ]);

        if ($notify) {
            $sign = $amount >= 0 ? '+' : '';
            Notification::create([
                'user_id' => $user->id,
                'type' => $amount >= 0 ? Notification::TYPE_POINTS_EARNED : Notification::TYPE_POINTS_DEDUCTED,
                'title' => $amount >= 0 ? 'Points Earned' : 'Points Deducted',
                'message' => "{$sign}{$amount} points: " . ($reason ?? ucfirst(str_replace('_', ' ', $type))),
                'data' => [
                    'point_id' => $point->id,
                    'amount' => $amount,
                    'type' => $type,
                ],
                'is_read' => false,
            ]);
        }

        return $point;
    }

    /**
     * Recalculate a user's total score from points.
     */
    public static function recalc(User $user): int
    {
        $total = (int) $user->points()->sum('amount');
        $user->update(['xp' => max(0, $total)]);

        return $total;
    }
}
