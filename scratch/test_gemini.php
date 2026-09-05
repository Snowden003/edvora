<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiKey = trim(env('GEMINI_API_KEY') ?: env('GENEMI_API_KEYS'));

// Test ListModels
echo "=== Listing Models ===\n";
$listUrl = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $apiKey;
$response = Illuminate\Support\Facades\Http::withoutVerifying()->get($listUrl);
echo "Status: " . $response->status() . "\n";
$json = $response->json();
if (isset($json['models'])) {
    foreach ($json['models'] as $m) {
        if (str_contains($m['name'], 'flash') || str_contains($m['name'], 'gemini')) {
            echo "- " . $m['name'] . " (" . implode(',', $m['supportedGenerationMethods'] ?? []) . ")\n";
        }
    }
} else {
    echo "Body: " . substr($response->body(), 0, 500) . "\n";
}

// Test gemini-3.6-flash
echo "\n=== Testing gemini-3.6-flash ===\n";
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key=" . $apiKey;
$res = Illuminate\Support\Facades\Http::withoutVerifying()->post($url, [
    'contents' => [
        ['parts' => [['text' => 'Hi']]]
    ]
]);
echo "Status: " . $res->status() . "\n";
echo "Body: " . substr($res->body(), 0, 300) . "\n";
