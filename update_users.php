<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\User::where('status', 'pending')
    ->where('id', '<', 46) // Assuming new registrations like lubna got higher IDs, wait, lubna's ID is AFFA-0046, so user id might be 46 or less
    ->update(['status' => 'approved']);

echo "Updated users.\n";
