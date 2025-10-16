<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\ReportController;

$ctrl = new ReportController();
$resp = $ctrl->dashboard();

// The controller returns a view; extract the data from the view factory
if ($resp instanceof \Illuminate\View\View) {
    $data = $resp->getData();
    echo "Stats:\n";
    print_r($data['stats']);
    echo "Daily Revenue (last 7):\n";
    print_r($data['dailyRevenue']);
}
