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
        $schedule->command('node:sixprofis_links')->weeklyOn(6, '16:10');
        $schedule->command('node:sixprofis_data')->weeklyOn(6, '16:40');

        $schedule->command('node:ladies_links')->weeklyOn(2, '10:30');
        $schedule->command('node:ladies_data')->weeklyOn(2, '20:30');

        $schedule->command('node:erotik_links')->weeklyOn(3, '10:30');
        $schedule->command('node:erotik_data')->weeklyOn(3, '23:30');

        $schedule->command('node:erobella_links')->weeklyOn(4, '10:30');
        $schedule->command('node:erobella_data')->weeklyOn(5, '23:30');

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
