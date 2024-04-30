<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;
use App\Models\TemperatureCategory;
use App\Models\Equipments;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TemperatureCatergoryController extends Controller
{

    public function addTemperatureData(Request $request){
        
        try
        {
            DB::beginTransaction();
            $addTemperature = new TemperatureCategory();
            $addTemperature->created_by  = $request->employeecode;
            $addTemperature->equipment_name  = $request->equipment_name;
            $addTemperature->temperature_value = $request->temperature_value;
            $addTemperature->created_at = Carbon::now()->toDateTimeString();
            // $addTemperature->updated_at = Carbon::now()->toDateTimeString();;
            $addTemperature->save();

            if(!$addTemperature) {
                DB::rollback();
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Temperature Added']);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e]);
        }
    
    }

    public function getAllTemperatureData(Request $request){

        $temperatureData = TemperatureCategory::whereDate('created_at', Carbon::today())->get();


        $equipmentsData = Equipments::get();

        return response()->json(['status' => true, 'message' => 'Today Temperature Data', 'temperatureData' => $temperatureData, "equipmentsData" => $equipmentsData ]);
        // if (count($temperatureData)) {
        // }
        // else {
        //     return response()->json(['status' => true, 'message' => 'No Data Found', 'data' => "N/A" ]);
        // }
    }

    public function addEquipmentData(Request $request){
        
        try
        {
            DB::beginTransaction();
            $addEquipment = new Equipments();
            $addEquipment->equipment_name  = $request->equipment_name;
            $addEquipment->created_by  = $request->employeecode;
            $addEquipment->created_at = Carbon::now()->toDateTimeString();
            // $addEquipment->updated_at = Carbon::now()->toDateTimeString();;
            $addEquipment->save();
            

            if(!$addEquipment) {
                DB::rollback();
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Equipment Added']);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e]);
        }
    







    }


    public function getAllEquipmentData(Request $request){

        $equipmentsData = Equipments::get();
        if (count($equipmentsData)) {
            return response()->json(['status' => true, 'message' => 'All Equipment Data', 'data' => $equipmentsData ]);
        }
        else {
            return response()->json(['status' => true, 'message' => 'No Equipment Found', 'data' => "N/A" ]);
        }

    }



}
