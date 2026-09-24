<?php

namespace App\Console\Commands;

use App\Models\Course;
use Illuminate\Console\Command;

class CloseExpiredCourses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:close-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close enrollments and mark courses as completed when their end_date has expired.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for expired courses...');

        $closedCount = Course::closeExpiredCourses();

        $this->info("Completed successfully. Closed {$closedCount} expired course(s) and synchronized enrollments.");

        return Command::SUCCESS;
    }
}
