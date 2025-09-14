<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\SendScheduledNotificationJob;
use App\Models\ScheduledNotification;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DateTimeZone;


use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('equipment:add-temperature')->dailyAt('09:00');
        $schedule->command('trasability:update-expire')->dailyAt('09:00');
        echo "new schdule is run \n";


        $schedule->call(function () {
    echo "new schedule run\n";

    // Tip: if you have many rows, switch to chunkById() for memory safety.
    $schedules = ScheduledNotification::query()
        ->where('status', 'active')
        ->where('is_recurring', true)
        ->get();

    foreach ($schedules as $sn) {
        // Current time in the schedule's timezone
        $nowTz = Carbon::now($sn->timezone);

        // ---- day_of_week parsing ----
        // Supports "7" (Every day) or comma list like "1,3,5"
        $days = array_map('intval', array_filter(array_map('trim', explode(',', (string) $sn->day_of_week))));
        if (empty($days)) {
            // If empty/missing, skip (or treat as everyday — your call)
            continue;
        }
        if (in_array(7, $days, true)) {
            $days = [0,1,2,3,4,5,6];
        }

        $isRightWeekday = in_array($nowTz->dayOfWeek, $days, true);

        // ---- time match (minute precision) ----
        // $sn->send_time is "HH:MM[:SS]" from DB; match "HH:MM"
        $scheduledHHmm = substr((string) $sn->send_time, 0, 5);
        $isRightMinute = $nowTz->format('H:i') === $scheduledHHmm;

        // ---- prevent duplicate sends in same local day ----
        $alreadySentToday = $sn->last_sent_at
            ? $sn->last_sent_at->copy()->setTimezone($sn->timezone)->isSameDay($nowTz)
            : false;

        if ($isRightWeekday && $isRightMinute && !$alreadySentToday) {
            SendScheduledNotificationJob::dispatch($sn->id);
            echo "Dispatched job for schedule #{$sn->id}\n";
        }
    }
})->everyMinute();
    //    $schedule->call(function () {
    //     echo "new schedule run\n";

    //     $schedules = ScheduledNotification::query()
    //         ->where('status', 'active')
    //         ->where('is_recurring', true)
    //         ->get();
    //     // foreach ($schedules as $sn) {
    //     //     SendScheduledNotificationJob::dispatch($sn->id);
    //     // }
    //     foreach ($schedules as $sn) {
    //         $nowTz = Carbon::now($sn->timezone);

    //         $isRightWeekday = (int)$sn->day_of_week === $nowTz->dayOfWeek; // 0..6
    //         $isRightMinute  = $nowTz->format('H:i') === Carbon::parse($sn->send_time)->format('H:i');
    //         $alreadySentToday = $sn->last_sent_at?->setTimezone($sn->timezone)?->isSameDay($nowTz);

    //         if ($isRightWeekday && $isRightMinute && !$alreadySentToday) {
    //             SendScheduledNotificationJob::dispatch($sn->id);
    //             echo "Dispatched job for schedule #{$sn->id}\n";
    //         }
    //     }
    // })->everyMinute();
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
