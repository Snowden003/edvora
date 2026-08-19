<?php

namespace Database\Seeders;

use App\Models\Competition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompetitionSeeder extends Seeder
{
    public function run(): void
    {
        $competitions = [
            [
                'title'              => 'Coding Challenge 2026',
                'description'        => 'Annual competitive programming contest open to all students. Solve algorithmic problems and win prizes.',
                'status'             => 'active',
                'prizes'             => ['1st' => '$500', '2nd' => '$300', '3rd' => '$100'],
                'start_date'         => now()->subDays(5),
                'end_date'           => now()->addDays(25),
                'participants_count' => 1250,
            ],
            [
                'title'              => 'Data Science Hackathon',
                'description'        => 'Build a data-driven solution in 48 hours. Teams of up to 4 members.',
                'status'             => 'active',
                'prizes'             => ['1st' => '$400', '2nd' => '$200'],
                'start_date'         => now()->subDays(2),
                'end_date'           => now()->addDays(30),
                'participants_count' => 680,
            ],
            [
                'title'              => 'Design Innovation Contest',
                'description'        => 'Create the best UI/UX design for a given problem statement. Judged by industry experts.',
                'status'             => 'active',
                'prizes'             => ['1st' => '$300', '2nd' => '$150', '3rd' => '$75'],
                'start_date'         => now()->subDays(1),
                'end_date'           => now()->addDays(35),
                'participants_count' => 340,
            ],
            [
                'title'              => 'Open Source Sprint',
                'description'        => 'Contribute to open source projects and get recognized. Top contributors win.',
                'status'             => 'upcoming',
                'prizes'             => ['1st' => '$250', '2nd' => '$100'],
                'start_date'         => now()->addDays(14),
                'end_date'           => now()->addDays(44),
                'participants_count' => 0,
            ],
        ];

        foreach ($competitions as $data) {
            Competition::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                $data
            );
        }
    }
}
