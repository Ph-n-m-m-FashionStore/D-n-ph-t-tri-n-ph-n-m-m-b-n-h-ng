<?php
namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\BackfillOrderItemSnapshots;
use App\Console\Commands\InspectOrder;
use App\Console\Commands\ImportOrderDetails;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        BackfillOrderItemSnapshots::class,
    InspectOrder::class,
    ImportOrderDetails::class,
    ];

    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule)
    {
        //
    }

    protected function commands()
    {
        // load routes/console.php if exists
        if (file_exists(base_path('routes/console.php'))) {
            $this->load(base_path('routes/console.php'));
        }
    }
}
