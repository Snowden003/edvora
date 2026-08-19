<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationController;
use App\Models\Course;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendClassReminders extends Command
{
    protected $signature   = 'notifications:class-reminders';
    protected $description = 'Send 5-minute class reminders to enrolled students and teachers';

    public function handle(): void
    {
        $now      = Carbon::now();
        $dayName  = strtolower($now->format('l'));
        $timeNow  = $now->format('H:i');
        $timePlus = $now->copy()->addMinutes(5)->format('H:i');

        $courses = Course::where('status', 'published')
            ->whereNotNull('primary_class_start')
            ->get();

        $sent = 0;

        foreach ($courses as $course) {
            $days = $course->primary_class_days ?? [];

            if (!in_array($dayName, $days)) {
                continue;
            }

            $classTime = substr($course->primary_class_start, 0, 5);

            if ($classTime !== $timePlus) {
                continue;
            }

            $enrolledUserIds = $course->enrollments()
                ->where('status', 'active')
                ->pluck('user_id');

            foreach ($enrolledUserIds as $studentId) {
                NotificationController::createCourseReminder(
                    $studentId,
                    $course->id,
                    $course->title,
                    Carbon::createFromFormat('H:i', $classTime)
                );
                $sent++;
            }

            if ($course->teacher_id) {
                NotificationController::createCourseReminder(
                    $course->teacher_id,
                    $course->id,
                    $course->title,
                    Carbon::createFromFormat('H:i', $classTime)
                );
                $sent++;
            }
        }

        $this->info("Sent {$sent} class reminder notifications.");
    }
}
