<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    /**
     * Display a listing of competitions.
     */
    public function index(): View
    {
        $competitions = Competition::all();
        $featuredCompetition = Competition::where('status', 'active')
            ->orWhere('status', 'upcoming')
            ->first();

        return view('competitions.index', compact('competitions', 'featuredCompetition'));
    }

    /**
     * Display a specific competition.
     */
    public function show(string $id): View
    {
        $competition = Competition::findOrFail($id);
        return view('competitions.detail', compact('competition'));
    }

    /**
     * Return competitions as JSON for API.
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');

        $query = Competition::query();

        if ($status !== 'all' && $status !== 'leaderboard') {
            $query->where('status', $status);
        }

        if ($status === 'leaderboard') {
            $query->where('status', 'completed');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $competitions = $query->get()->map(function ($competition) {
            return [
                'id' => $competition->id,
                'title' => $competition->title,
                'slug' => $competition->slug,
                'status' => $competition->status,
                'category' => $this->getCategoryFromTitle($competition->title),
                'start_date' => $competition->start_date?->format('Y-m-d'),
                'end_date' => $competition->end_date?->format('Y-m-d'),
                'prize_pool' => $this->calculatePrizePool($competition->prizes),
                'participants' => $competition->participants_count,
                'difficulty' => 'Intermediate',
                'thumbnail' => $competition->thumbnail,
                'description' => $competition->description,
                'prizes' => $competition->prizes ?? [],
                'featured' => false,
            ];
        });

        return response()->json($competitions);
    }

    /**
     * Return a single competition as JSON.
     */
    public function apiShow(int $id): JsonResponse
    {
        $competition = Competition::findOrFail($id);

        return response()->json([
            'id' => $competition->id,
            'title' => $competition->title,
            'slug' => $competition->slug,
            'status' => $competition->status,
            'category' => $this->getCategoryFromTitle($competition->title),
            'start_date' => $competition->start_date?->format('Y-m-d'),
            'end_date' => $competition->end_date?->format('Y-m-d'),
            'prize_pool' => $this->calculatePrizePool($competition->prizes),
            'participants' => $competition->participants_count,
            'difficulty' => 'Intermediate',
            'thumbnail' => $competition->thumbnail,
            'description' => $competition->description,
            'prizes' => $competition->prizes ?? [],
            'requirements' => ['Programming Knowledge', 'Problem Solving'],
            'judges' => ['Industry Experts'],
        ]);
    }

    /**
     * Extract category from competition title.
     */
    private function getCategoryFromTitle(string $title): string
    {
        $categories = [
            'AI' => 'AI/ML',
            'Data' => 'Data Science',
            'Web' => 'Web Dev',
            'Design' => 'Design',
            'Security' => 'Security',
            'Game' => 'Game Dev',
            'Mobile' => 'Mobile',
            'Hackathon' => 'Hackathon',
            'Coding' => 'Programming',
        ];

        foreach ($categories as $keyword => $category) {
            if (stripos($title, $keyword) !== false) {
                return $category;
            }
        }

        return 'Programming';
    }

    /**
     * Calculate total prize pool from prizes array.
     */
    private function calculatePrizePool(?array $prizes): int
    {
        if (!$prizes) {
            return 0;
        }

        $total = 0;
        foreach ($prizes as $prize) {
            // Extract numeric value from string like "$500" or "1st" => "$500"
            if (is_string($prize)) {
                preg_match('/\$([0-9,]+)/', $prize, $matches);
                if (isset($matches[1])) {
                    $total += (int) str_replace(',', '', $matches[1]);
                }
            }
        }

        return $total;
    }
}
