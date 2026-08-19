<?php

namespace App\Http\Controllers;

use App\Models\ScoringRule;

class ScoringHelpController extends Controller
{
    public function index()
    {
        if (ScoringRule::count() === 0) {
            \App\Services\ScoreService::seedDefaultRules();
        }

        $rules = ScoringRule::active()->orderBy('type')->get();
        return view('scoring-help', compact('rules'));
    }
}
