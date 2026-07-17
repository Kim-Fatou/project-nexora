<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Connection;

$connections = Connection::all();
foreach ($connections as $conn) {
    echo "ID: {$conn->id}, User ID: {$conn->user_id}, Friend ID: {$conn->friend_id}, Status: {$conn->status}\n";
}
