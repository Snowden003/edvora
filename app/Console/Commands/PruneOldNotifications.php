<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;

class PruneOldNotifications extends Command
{
    protected $signature   = 'notifications:prune {--days=7 : Delete notifications older than this many days}';
    protected $description = 'Delete each user\'s notifications that are older than the given number of days';

    public function handle(): int
    {
        $days    = (int) $this->option('days');
        $cutoff  = now()->subDays($days);

        $deleted = Notification::where('created_at', '<', $cutoff)->delete();

        $this->info("Pruned {$deleted} notification(s) older than {$days} day(s).");

        return self::SUCCESS;
    }
}
