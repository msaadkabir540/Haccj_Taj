<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Equipments;
use App\Models\TemperatureCategory;
use Carbon\Carbon;

class AddEquipmentTemperature extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Run with: php artisan equipment:add-temperature
     */
    protected $signature = 'equipment:add-temperature';

    /**
     * The console command description.
     */
    protected $description = 'Check equipments and add temperature category records based on category value';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $equipments = Equipments::all();

        foreach ($equipments as $equipment) {
            $temperatureValue = 0;

            if ($equipment->category == 0 || $equipment->category === "0") {
                // subtract 6
                $temperatureValue = -6;
            } elseif ($equipment->category == 1 || $equipment->category === "1") {
                // add 2
               $temperatureValue = '+2';
            } else {
                continue; // skip if category not 0 or 1
            }

            $addTemperature = new TemperatureCategory();
            $addTemperature->created_by  = $equipment->created_by;
            $addTemperature->equipment_name  = $equipment->equipment_name;
            $addTemperature->temperature_value = $temperatureValue;
            $addTemperature->created_at = Carbon::now()->toDateTimeString();
            $addTemperature->save();
        }

        $this->info('Temperature categories added successfully!');
    }
}
