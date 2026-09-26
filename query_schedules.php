<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$schedules = \App\Models\Schedule::where('day', 'Sabtu')->with('coach')->get();
foreach($schedules as $s) {
    echo "ID: $s->id | Coach: {$s->coach->name} | Day: $s->day | Start: $s->start_time | IsMakeup: $s->is_makeup\n";
    $reports = \App\Models\TrainingReport::where('schedule_id', $s->id)->count();
    echo "  Reports count: $reports\n";
}
