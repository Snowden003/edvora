<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('notifications:class-reminders')->everyMinute();
Schedule::command('notifications:prune --days=7')->weekly();
Schedule::command('courses:close-expired')->hourly();
