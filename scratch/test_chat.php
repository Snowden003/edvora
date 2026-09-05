<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing GeminiService Chat ===\n";
$service = app(\App\Services\GeminiService::class);

$result = $service->chat("چند دوره در سایت ادورا وجود دارد؟");

echo "Success: " . ($result['success'] ? 'true' : 'false') . "\n";
echo "Message:\n" . $result['message'] . "\n";
echo "Sources: " . implode(', ', $result['sources']) . "\n";
