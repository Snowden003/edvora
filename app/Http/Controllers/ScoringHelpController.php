<?php

namespace App\Http\Controllers;

use App\Models\ScoringRule;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ScoringHelpController extends Controller
{
    public function index()
    {
        if (ScoringRule::count() === 0) {
            \App\Services\ScoreService::seedDefaultRules();
        }

        $rules = ScoringRule::active()->orderBy('default_score', 'desc')->get();

        $userData = null;
        if (Auth::check()) {
            $u = Auth::user();
            $levelInfo = method_exists($u, 'level') ? $u->level() : ['level' => 1, 'title' => 'Beginner', 'progress' => 0, 'next_threshold' => 100];
            $userData = [
                'id' => $u->id,
                'name' => $u->name,
                'avatar' => method_exists($u, 'publicAvatarUrl') ? $u->publicAvatarUrl() : null,
                'role' => $u->role,
                'total_score' => method_exists($u, 'totalScore') ? $u->totalScore() : ($u->xp ?? 0),
                'level' => $levelInfo['level'] ?? 1,
                'level_title' => $levelInfo['title'] ?? 'Beginner',
                'progress' => $levelInfo['progress'] ?? 0,
                'next_threshold' => $levelInfo['next_threshold'] ?? 100,
            ];
        }

        return Inertia::render('ScoringHelp', [
            'rules' => $rules,
            'auth' => [
                'user' => $userData,
            ],
        ]);
    }
}

