<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\ReportController;

$admin = User::where('role','admin')->first();
if (!$admin) { echo "No admin user found\n"; exit(1); }
Auth::loginUsingId($admin->id);

$ctrl = new ReportController();
$resp = $ctrl->dashboard();
if ($resp instanceof \Illuminate\View\View) {
    $html = $resp->render();
    $path = __DIR__ . '/../storage/app/dashboard_snapshot.html';
    file_put_contents($path, $html);
    echo "Wrote dashboard snapshot to: {$path}\n";
} else {
    echo "Controller did not return a view.\n";
}
