<?php
// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\SensipaySeedDummy;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<class-string>
     */
    protected $commands = [
        SensipaySeedDummy::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // ...
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
