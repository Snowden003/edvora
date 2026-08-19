<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Activity;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Leaderboard;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentDataSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            ['name' => 'Ahmad Khan',   'email' => 'ahmad@edvora.tech',  'xp' => 1200],
            ['name' => 'Sara Ali',     'email' => 'sara@edvora.tech',   'xp' => 1080],
            ['name' => 'Omar Rahimi',  'email' => 'omar@edvora.tech',   'xp' => 990],
            ['name' => 'Layla Hassan', 'email' => 'layla@edvora.tech',  'xp' => 870],
            ['name' => 'Test Student', 'email' => 'student@edvora.tech','xp' => 750],
        ];

        $courses = Course::where('status', 'published')->get();
        $achievements = Achievement::all();

        foreach ($students as $i => $data) {
            $student = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('password'),
                    'role'     => 'student',
                    'status'   => 'active',
                ]
            );

            Leaderboard::updateOrCreate(
                ['user_id' => $student->id, 'period' => 'monthly', 'year' => now()->year, 'month' => now()->month],
                ['xp' => $data['xp'], 'rank' => $i + 1]
            );

            $enrolled = $courses->random(min(3, $courses->count()));
            foreach ($enrolled as $j => $course) {
                Enrollment::firstOrCreate(
                    ['user_id' => $student->id, 'course_id' => $course->id],
                    [
                        'status'              => $j === 0 ? 'completed' : 'active',
                        'progress_percentage' => $j === 0 ? 100 : rand(10, 85),
                        'completed_at'        => $j === 0 ? now()->subDays(rand(5, 30)) : null,
                    ]
                );
            }

            $earnedAchievements = $achievements->random(min(3, $achievements->count()));
            foreach ($earnedAchievements as $achievement) {
                if (!$student->achievements()->where('achievement_id', $achievement->id)->exists()) {
                    $student->achievements()->attach($achievement->id, ['earned_at' => now()->subDays(rand(1, 60))]);
                }
            }

            $activitySamples = [
                ['type' => 'course',      'icon' => 'bi-play-circle',    'color' => 'success',   'message' => 'Completed a lesson in ' . ($enrolled->first()->title ?? 'a course')],
                ['type' => 'achievement', 'icon' => 'bi-trophy',         'color' => 'warning',   'message' => 'Earned "' . ($earnedAchievements->first()->title ?? 'Achievement') . '" badge'],
                ['type' => 'competition', 'icon' => 'bi-award',          'color' => 'primary',   'message' => 'Joined Coding Challenge 2026'],
                ['type' => 'event',       'icon' => 'bi-calendar-check', 'color' => 'info',      'message' => 'Registered for AI & Machine Learning Summit'],
            ];

            foreach ($activitySamples as $act) {
                Activity::create([
                    'user_id' => $student->id,
                    'type'    => $act['type'],
                    'icon'    => $act['icon'],
                    'color'   => $act['color'],
                    'message' => $act['message'],
                    'created_at' => now()->subHours(rand(1, 168)),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
