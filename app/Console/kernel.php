<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan setiap akhir bulan jam 23:50
        $schedule->command('some:command')->monthlyOn(1);

        // Atau jalankan setiap hari untuk backup harian (opsional)
        $schedule->command('snapshot:monthly --date=' . now()->format('Y-m-d'))
                 ->dailyAt('23:00')
                 ->timezone('Asia/Jakarta');
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