<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TrasabilityCategory;
use Carbon\Carbon;

class UpdateTrasabilityExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Run with: php artisan trasability:update-expire
     */
    protected $signature = 'trasability:update-expire';

    /**
     * The console command description.
     */
    protected $description = 'Update expire_at in TrasabilityCategory when only 1 day is left, extend by 3 days';

    /**
     * Execute the console command.
     */
   public function handle()
{
    $today = Carbon::today(); // 2025-09-02 00:00:00
    $endOfToday = Carbon::today()->endOfDay(); // 2025-09-02 23:59:59

    // Get all records expiring today
    $records = TrasabilityCategory::whereBetween('expire_at', [$today, $endOfToday])->get();

    foreach ($records as $record) {
        $oldExpire = $record->expire_at;
        $newExpire = Carbon::parse($record->expire_at)->addDays(3);

        $record->expire_at = $newExpire->format('Y-m-d H:i:s');
        $record->save();

        $this->info("Updated ID {$record->id} from {$oldExpire} to {$record->expire_at}");
    }

    $this->info('All Trasability records expiring today extended by +3 days.');
}
}
