<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define your application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Tes scheduler — akan nulis log setiap menit
        $schedule->call(function () {
            Log::info("SCHEDULER RUNNING at " . now()->toDateTimeString());
        })->everyMinute();

        // ==============================
        // Contoh untuk Reminder Jatuh Tempo
        // ==============================
        $schedule->command('sensipay:reminder')->dailyAt('09:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
