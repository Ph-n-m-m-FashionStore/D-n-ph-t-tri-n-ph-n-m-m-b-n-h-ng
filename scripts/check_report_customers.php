<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

$admin = User::where('role','admin')->first();
if (!$admin) { echo "No admin user\n"; exit(1); }
Auth::loginUsingId($admin->id);

$ctrl = new ReportController();
$resp = $ctrl->customers(new \Illuminate\Http\Request());
if ($resp instanceof \Illuminate\View\View) {
    $data = $resp->getData();
    echo "Customer Stats:\n";
    print_r($data['customerStats']);
    echo "Top Customers (computed totals):\n";
    foreach ($data['topCustomers'] as $u) {
        echo "ID: {$u->id} | Name: {$u->name} | orders: {$u->computed_orders_count} | total: {$u->computed_report_total}\n";
    }
}
