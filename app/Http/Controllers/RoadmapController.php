<?php

namespace App\Http\Controllers;

use App\Models\RoadmapStage;

class RoadmapController extends Controller
{
    public function index()
    {
        $stages = RoadmapStage::where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('roadmap', compact('stages'));
    }
}
