<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$indicators = \App\Models\Indicator::all()->toArray();
file_put_contents('indicators_dump.json', json_encode($indicators));
