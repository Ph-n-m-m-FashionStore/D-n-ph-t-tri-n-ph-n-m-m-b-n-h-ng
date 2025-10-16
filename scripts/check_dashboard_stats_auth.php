<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\ReportController;

$admin = User::where('role','admin')->first();
if (!$admin) {
    echo "No admin user found.\n";
    exit(1);
}
// Log in as admin
Auth::loginUsingId($admin->id);

$ctrl = new ReportController();
$resp = $ctrl->dashboard();
if ($resp instanceof \Illuminate\View\View) {
    $data = $resp->getData();
    echo "Stats:\n";
    print_r($data['stats']);
    echo "Daily Revenue (last 7):\n";
    print_r($data['dailyRevenue']);
}
