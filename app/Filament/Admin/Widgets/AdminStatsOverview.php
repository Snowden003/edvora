<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Event;
use App\Models\ClassSession;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'System Overview';

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 4;
    }

    public function getGridColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        // 1. Active Students
        $activeStudents = User::where('role', 'student')
            ->where('status', 'active')
            ->count();
        $totalStudents = User::where('role', 'student')->count();

        // 2. Active Classes
        $activeClasses = Course::whereIn('status', ['active', 'published'])->count();
        $totalCourses = Course::count();

        // 3. Verified Teachers
        $verifiedTeachers = Teacher::where('is_verified', true)->count();
        $totalTeachers = Teacher::count();

        // 4. Active Events
        $activeEvents = Event::where('status', 'active')->count();
        $totalEvents = Event::count();

        // 6. Classes in last 24 hours
        $classes24h = ClassSession::where('started_at', '>=', Carbon::now()->subHours(24))->count();

        // 7. Events in last week
        $eventsWeek = Event::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        return [
            Stat::make('Active Students', number_format($activeStudents))
                ->description('Total: ' . number_format($totalStudents))
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Active Classes', number_format($activeClasses))
                ->description('Total Courses: ' . number_format($totalCourses))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),

            Stat::make('Verified Teachers', number_format($verifiedTeachers))
                ->description('Total Teachers: ' . number_format($totalTeachers))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),

            Stat::make('Active Events', number_format($activeEvents))
                ->description('Total Events: ' . number_format($totalEvents))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('Classes (24h)', number_format($classes24h))
                ->description('Sessions held today')
                ->descriptionIcon('heroicon-m-clock')
                ->color('success'),

            Stat::make('Events (7 days)', number_format($eventsWeek))
                ->description('Recently added events')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }
}
