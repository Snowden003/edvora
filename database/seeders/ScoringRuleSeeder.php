<?php

namespace Database\Seeders;

use App\Services\ScoreService;
use Illuminate\Database\Seeder;

class ScoringRuleSeeder extends Seeder
{
    public function run(): void
    {
        ScoreService::seedDefaultRules();
    }
}
