<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;
use App\Models\OilMachines;
use App\Models\OilTemperatureCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OilTemperatureCatergoryController extends Controller
{

    public function addOilTemperatureData(Request $request){
            // dd($request->all());
        try
        {
            DB::beginTransaction();
            $addOilTemperature = new OilTemperatureCategory();
            $addOilTemperature->created_by  = $request->employeecode;
            $addOilTemperature->machine_name  = $request->machine_name;
            $addOilTemperature->machine_type = $request->machine_type;
            $addOilTemperature->oil_temperature = $request->oil_temperature;
            $addOilTemperature->image_name = $request->file('image') ? $request->file('image')->getClientOriginalName() : "";
            $addOilTemperature->image = $request->file('image') ? $request->file('image')->move('uploads/', $addOilTemperature->image_name) : "";
            $addOilTemperature->created_at = Carbon::now()->toDateTimeString();
            // $addOilTemperature->updated_at = Carbon::now()->toDateTimeString();
            $addOilTemperature->save();

            if(!$addOilTemperature) {
                DB::rollback();
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Oil Temperature Added']);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e]);
        }
    
    }


    public function updateOilTemperatureData(Request $request){
        $id = $request->id;
        try {
            DB::beginTransaction();

            $oilTemperature = OilTemperatureCategory::find($id);

            if (!$oilTemperature) {
                return response()->json(['status' => false, 'message' => 'Oil Temperature data not found']);
            }

            $oilTemperature->updated_by = $request->employeecode;
            $oilTemperature->machine_name = $request->machine_name;
            $oilTemperature->machine_type = $request->machine_type;
            $oilTemperature->oil_temperature = $request->oil_temperature;            
            $oilTemperature->updated_at = Carbon::now()->toDateTimeString();
            $oilTemperature->save();
    
            DB::commit();
    
            return response()->json(['status' => true, 'message' => 'Oil Temperature Updated']);
        } catch(\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function deleteOilTemperature($id)
    {
        try {
            DB::beginTransaction();

            $OilTemperature = OilTemperatureCategory::find($id);

            if (!$OilTemperature) {
                return response()->json(['status' => false, 'message' => 'OilTemperature data not found']);
            }

            $OilTemperature->delete();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'OilTemperature data deleted']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }



    public function getAllOilTemperatureData(Request $request, $employeecode){

        $oilTemperatureData = OilTemperatureCategory::whereDate('created_at', Carbon::today())->where('created_by', $employeecode)->get();

        $oilMachineData = OilMachines::get();

        // if (count($trasabilityData)) {
            return response()->json(['status' => true, 'message' => 'All Oil Temperature Data', 'oilTemperatureData' => $oilTemperatureData, 'oilMachineData' => $oilMachineData ]);
        // }
        // else {
        //     return response()->json(['status' => true, 'message' => 'No Trasability Found', 'data' => "N/A" ]);
        // }

    }


    public function addOilTempMachines(Request $request){
        
        try
        {
            DB::beginTransaction();
            $addOilMachines = new OilMachines();
            $addOilMachines->machine_name  = $request->machine_name;
            $addOilMachines->employeecode  = $request->employeecode;
            $addOilMachines->created_at = Carbon::now()->toDateTimeString();
            // $addOilMachines->updated_at = Carbon::now()->toDateTimeString();;
            $addOilMachines->save();
            

            if(!$addOilMachines) {
                DB::rollback();
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Oil Machine Added']);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e]);
        }
    

    }


    // public function getAllTrasabilityProd(Request $request){

    //     $trasabilityProdData = TrasabilityProdType::get();
    //     if (count($trasabilityProdData)) {
    //         return response()->json(['status' => true, 'message' => 'All Trasability Product Type Data', 'data' => $trasabilityProdData ]);
    //     }
    //     else {
    //         return response()->json(['status' => true, 'message' => 'No Trasability Product Type Found', 'data' => "N/A" ]);
    //     }

    // }




}
