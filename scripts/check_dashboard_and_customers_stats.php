<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CustomerController;

$admin = User::where('role','admin')->first();
if (!$admin) { echo "No admin user\n"; exit(1); }
Auth::loginUsingId($admin->id);

$report = new ReportController();
$dashView = $report->dashboard();
$dashData = $dashView instanceof \Illuminate\View\View ? $dashView->getData() : null;

$customersCtrl = new CustomerController();
$customersView = $customersCtrl->index(new \Illuminate\Http\Request());
$customersData = $customersView instanceof \Illuminate\View\View ? $customersView->getData() : null;

echo "--- Dashboard stats (server) ---\n";
if ($dashData && isset($dashData['stats'])) print_r($dashData['stats']); else echo "No stats\n";

echo "--- Customers stats (server) ---\n";
if ($customersData && isset($customersData['stats'])) print_r($customersData['stats']); else echo "No stats\n";

// Print simple totals from customers view table
if ($customersData && isset($customersData['customers'])) {
    $customers = $customersData['customers'];
    echo "Customers count (page): " . $customers->total() . "\n";
}
