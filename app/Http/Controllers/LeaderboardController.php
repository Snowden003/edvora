<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Point;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $now = now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // Filters
        $courseId = $request->input('course_id');
        $range = $request->input('range', 'monthly'); // weekly, monthly, all_time

        $from = null;
        $to = null;

        if ($range === 'weekly') {
            $from = $now->copy()->startOfWeek();
            $to = $now->copy()->endOfWeek();
            $monthName = 'This Week';
        } elseif ($range === 'monthly') {
            $from = $now->copy()->startOfMonth();
            $to = $now->copy()->endOfMonth();
            $monthName = $now->format('F');
        } else {
            $monthName = 'All Time';
        }

        $courses = Course::orderBy('title')->pluck('title', 'id');

        // Build leaderboard query
        $query = Point::query()
            ->selectRaw('user_id, SUM(amount) as xp')
            ->with('user')
            ->groupBy('user_id');

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        $entries = $query
            ->orderByDesc('xp')
            ->take(20)
            ->get()
            ->map(function ($entry, $index) {
                $entry->rank = $index + 1;
                return $entry;
            });

        if ($entries->count() > 0 && $entries->count() < 3) {
            $top3 = collect();
            $rest = $entries->values();
        } else {
            $top3 = $entries->take(3)->values();
            $rest = $entries->slice(3)->values();
        }

        // All-time top 3 used as fallback if filtered results are empty
        $allTimeTop = Point::query()
            ->selectRaw('user_id, SUM(amount) as xp')
            ->with('user')
            ->groupBy('user_id')
            ->orderByDesc('xp')
            ->take(3)
            ->get()
            ->map(function ($entry, $index) {
                $entry->rank = $index + 1;
                return $entry;
            });

        if ($top3->isEmpty() && $allTimeTop->isNotEmpty()) {
            $top3 = $allTimeTop;
            $rest = collect();
            $entries = $top3;
        }

        return view('leaderboard', compact(
            'entries', 'top3', 'rest', 'monthName', 'currentYear', 'allTimeTop',
            'courses', 'courseId', 'range'
        ));
    }
}
