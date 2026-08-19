<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Leaderboard;

class LeaderboardEntrySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Add XP to specific student
        $user = User::where('email', 'mobinhassani299@gmail.com')->first();

        if ($user) {
            $leaderboard = Leaderboard::firstOrNew(
                [
                    'user_id' => $user->id,
                    'year' => $now->year,
                    'month' => $now->month,
                ],
                [
                    'xp' => 0,
                    'rank' => 1,
                    'period' => $now->format('Y-m'),
                ]
            );
            $leaderboard->xp = 2500; // Set initial XP for seeder
            $leaderboard->save();

            // Update user's total XP
            $user->xp = 2500;
            $user->save();

            $this->command->info("✅ Added 2500 XP to {$user->name} - Rank #1!");
        } else {
            $this->command->warn("User mobinhassani299@gmail.com not found");
        }

        // Add some other random students for a nice leaderboard
        $otherStudents = User::where('role', 'student')
            ->where('email', '!=', 'mobinhassani299@gmail.com')
            ->take(5)
            ->get();

        $xpValues = [1800, 1200, 900, 600, 300];
        $ranks = [2, 3, 4, 5, 6];

        foreach ($otherStudents as $index => $student) {
            $leaderboard = Leaderboard::firstOrNew(
                [
                    'user_id' => $student->id,
                    'year' => $now->year,
                    'month' => $now->month,
                ],
                [
                    'xp' => 0,
                    'rank' => $ranks[$index] ?? 99,
                    'period' => $now->format('Y-m'),
                ]
            );
            $leaderboard->xp = $xpValues[$index] ?? 100;
            $leaderboard->save();

            // Update user's total XP
            $student->xp = $xpValues[$index] ?? 100;
            $student->save();
        }

        $this->command->info("✅ Leaderboard populated with " . ($otherStudents->count() + 1) . " students!");
    }
}
