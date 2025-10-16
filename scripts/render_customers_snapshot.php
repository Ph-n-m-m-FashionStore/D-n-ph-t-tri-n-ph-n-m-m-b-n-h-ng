<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\CustomerController;

$admin = User::where('role','admin')->first();
if (!$admin) { echo "No admin user\n"; exit(1); }
Auth::loginUsingId($admin->id);

$ctrl = new CustomerController();
$resp = $ctrl->index(new \Illuminate\Http\Request());
if ($resp instanceof \Illuminate\View\View) {
    $html = $resp->render();
    $path = __DIR__ . '/../storage/app/customers_snapshot.html';
    file_put_contents($path, $html);
    echo "Wrote customers snapshot to: {$path}\n";
} else {
    echo "CustomerController@index did not return a view.\n";
}
