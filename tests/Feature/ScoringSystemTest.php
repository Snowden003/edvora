<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Point;
use App\Models\User;
use App\Services\ScoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoringSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_score_is_calculated_from_points(): void
    {
        $student = User::factory()->create(['role' => 'student', 'xp' => 0]);

        ScoreService::adjust($student, 50, 'manual', 'Good work');
        ScoreService::adjust($student, -10, 'manual', 'Late');

        $this->assertEquals(40, $student->totalScore());
        $this->assertEquals(50, $student->earnedPoints());
        $this->assertEquals(10, $student->deductedPoints());
    }

    public function test_user_xp_is_synced_when_points_change(): void
    {
        $student = User::factory()->create(['role' => 'student', 'xp' => 0]);

        ScoreService::adjust($student, 25, 'manual', 'Bonus');

        $student->refresh();
        $this->assertEquals(25, $student->xp);
    }

    public function test_attendance_rules_are_auto_created(): void
    {
        $student = User::factory()->create(['role' => 'student', 'xp' => 0]);

        $point = ScoreService::award($student, 'attendance_on_time', 'Present');

        $this->assertEquals(10, $point->amount);
        $this->assertDatabaseHas('scoring_rules', [
            'action_name' => 'attendance_on_time',
            'default_score' => 10,
        ]);
    }
}
