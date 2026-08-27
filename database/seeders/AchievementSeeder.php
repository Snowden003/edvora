<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
            ['title' => 'First Course Completed', 'description' => 'Completed your first course',           'icon' => 'bi-award',      'color' => 'success', 'points' => 100],
            ['title' => 'Speed Learner',           'description' => 'Completed 3 courses in a month',       'icon' => 'bi-lightning',  'color' => 'warning', 'points' => 200],
            ['title' => 'Top 10%',                 'description' => 'Ranked in top 10% globally',           'icon' => 'bi-trophy',     'color' => 'primary', 'points' => 300],
            ['title' => 'Knowledge Master',        'description' => 'Completed 10 courses',                 'icon' => 'bi-mortarboard','color' => 'info',    'points' => 400],
            ['title' => 'Community Helper',        'description' => 'Helped 50+ students in discussions',  'icon' => 'bi-people',     'color' => 'secondary','points' => 250],

        foreach ($achievements as $data) {
            Achievement::firstOrCreate(['title' => $data['title']], $data);
        }
    }
}
