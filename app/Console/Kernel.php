<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DateTimeZone;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('equipment:add-temperature')->dailyAt('09:00');
        $schedule->command('trasability:update-expire')->dailyAt('09:00');
    }

    /**
     * Force all scheduled tasks to use the France timezone.
     */
    protected function scheduleTimezone(): DateTimeZone|string|null
    {
        return 'Europe/Paris'; // France timezone
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
