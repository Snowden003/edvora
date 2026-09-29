<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (App\Models\TeamMember::all() as $m) {
    echo $m->id . ": " . $m->name . " | " . $m->role_title . " | " . $m->department . "\n";
    echo "Bio: " . $m->bio . "\n---\n";
}
