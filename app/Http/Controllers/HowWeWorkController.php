<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Http\Request;

class HowWeWorkController extends Controller
{
    public function index()
    {
        $stats = $this->getCalculatedStats();

        // Get hero stats (first 3)
        $heroStats = $stats->take(3);

        // Get impact stats (all)
        $impactStats = $stats;

        $content = \App\Models\PageContent::get('page_how_we_work', []);

        return view('how-we-work', compact('heroStats', 'impactStats', 'content'));
    }

    private function getCalculatedStats()
    {
        // Calculate real stats from database
        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')
            ->where('status', 'active')
            ->whereHas('teacher', fn ($q) => $q->where('is_verified', true))
            ->count();

        // Count distinct provinces from student_profiles as a proxy for regions/countries
        $distinctProvinces = StudentProfile::distinct('province')->count('province');
        // If we have provinces, use that count, otherwise default to a reasonable number
        $countriesCount = $distinctProvinces > 0 ? $distinctProvinces : 1;

        return collect([
            (object)[
                'key' => 'youth_empowered',
                'value' => number_format($totalStudents),
                'label' => 'Youth Empowered',
                'suffix' => '+',
            ],
            (object)[
                'key' => 'volunteer_educators',
                'value' => number_format($totalTeachers),
                'label' => 'Volunteer Educators',
                'suffix' => '+',
            ],
            (object)[
                'key' => 'countries_reached',
                'value' => number_format($countriesCount),
                'label' => 'Provinces Reached',
                'suffix' => '+',
            ],
            (object)[
                'key' => 'success_rate',
                'value' => '95',
                'label' => 'Success Rate',
                'suffix' => '%',
            ],
        ]);
    }
}
