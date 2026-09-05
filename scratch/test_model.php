<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiKey = trim(env('GEMINI_API_KEY') ?: env('GENEMI_API_KEYS'));

$modelsToTry = [
    'gemini-flash-latest',
    'gemini-3.5-flash-lite',
    'gemini-3.1-flash-lite',
    'gemini-3.5-flash',
    'gemini-flash-lite-latest'
];

foreach ($modelsToTry as $model) {
    echo "Testing $model...\n";
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey;
    try {
        $res = Illuminate\Support\Facades\Http::withoutVerifying()->timeout(15)->post($url, [
            'contents' => [
                ['parts' => [['text' => 'Hi, reply with OK']]]
            ]
        ]);
        echo "Model {$model} status: " . $res->status() . "\n";
        if ($res->successful()) {
            echo "SUCCESS with {$model}: " . $res->json('candidates.0.content.parts.0.text') . "\n";
            break;
        } else {
            echo "FAILED {$model}: " . substr($res->body(), 0, 150) . "\n";
        }
    } catch (\Throwable $e) {
        echo "ERROR {$model}: " . $e->getMessage() . "\n";
    }
}
