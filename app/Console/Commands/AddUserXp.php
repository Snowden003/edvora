<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Leaderboard;

class AddUserXp extends Command
{
    protected $signature = 'user:add-xp {email} {xp}';
    protected $description = 'Add XP to a user and update leaderboard';

    public function handle()
    {
        $email = $this->argument('email');
        $xp = (int) $this->argument('xp');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        $now = now();

        // Find existing entry or create new one
        $leaderboard = Leaderboard::firstOrNew(
            [
                'user_id' => $user->id,
                'year' => $now->year,
                'month' => $now->month,
            ],
            [
                'xp' => 0,
                'rank' => 0,
                'period' => $now->format('Y-m'),
            ]
        );

        // Add XP to existing value (accumulate)
        $leaderboard->xp += $xp;
        $leaderboard->save();

        // Also update user's total XP
        $user->xp += $xp;
        $user->save();

        $this->info("✅ Successfully added {$xp} XP to {$user->name} ({$email})");
        $this->info("Monthly XP: {$leaderboard->xp} | Total XP: {$user->xp} for {$now->format('F Y')}");

        return 0;
    }
}
