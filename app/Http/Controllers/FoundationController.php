<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class FoundationController extends Controller
{
    public function index()
    {
        // Get donation statistics
        $totalDonations = Donation::where('status', 'completed')->sum('amount');
        $totalSupporters = Donation::where('status', 'completed')->distinct('email')->count();
        $totalStudents = User::where('role', 'student')->count();

        // Get top donor
        $topDonor = Donation::where('status', 'completed')
            ->where('show_name', true)
            ->orderByDesc('amount')
            ->first();

        // Get monthly donations for chart (last 6 months)
        $monthlyDonations = Donation::where('status', 'completed')
            ->where('donated_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(donated_at, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Format chart data
        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $chartLabels[] = $month->format('M Y');
            
            $monthTotal = $monthlyDonations->firstWhere('month', $monthKey);
            $chartData[] = $monthTotal ? (int) $monthTotal->total : 0;
        }

        return view('foundation', compact(
            'totalDonations',
            'totalSupporters',
            'totalStudents',
            'topDonor',
            'chartLabels',
            'chartData'
        ));
    }
}
