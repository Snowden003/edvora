<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.partials.footer', function ($view) {
            $footerStats = Cache::remember('footer:stats', now()->addMinutes(10), function () {
                return [
                    'footerStudents' => User::where('role', 'student')->count(),
                    'footerCourses' => Course::count(),
                    'footerTeachers' => User::where('role', 'teacher')->count(),
                ];
            });

            $view->with($footerStats);
        });
    }
}
