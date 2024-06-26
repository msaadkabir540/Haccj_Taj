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

    // public function getAllTemperatureData(Request $request){

    //     $employee = Employees::where('employeecode', $request->employeecode)->first();

    //     // Check if the employee record exists and if the employee is an admin
        
    //     if ($employee && $employee->isAdmin == 1) {
    //     }        
    //     // $temperatureData = TemperatureCategory::whereDate('created_at', Carbon::today())->where('created_by', $employeecode)->get();
    //     $temperatureData = TemperatureCategory::select('id', 'equipment_name', 'temperature_value', 'created_at', 'created_by');
    //     // dd($request->date);
    //     if($request->date){
    //         $date = date('Y-m-d', strtotime($request->date));
    //         if($request->edate) {
    //             $edate = date('Y-m-d', strtotime($request->edate));
    //             $temperatureData = $temperatureData->whereRaw("DATE(created_at) BETWEEN '$date' AND '$edate'");
    //         }
    //         else {
    //             $temperatureData = $temperatureData->whereRaw("DATE(created_at) = '$date'");
    //         }
    //     }
    //     else {
    //         if(!$request->date && !$request->employeecode ) {
    //             $temperatureData = $temperatureData->whereRaw("DATE(created_at) = curdate()");
    //         }
    //     }

    //     if($request->employeecode){
    //         $temperatureData = $temperatureData->where('created_by' , trim($request->employeecode));
    //     }
    //     $temperatureData = $temperatureData->orderBy('created_at', 'DESC')->get();

    //     $equipmentsData = Equipments::get();

    //     return response()->json(['status' => true, 'message' => 'Today Temperature Data', 'temperatureData' => $temperatureData, "equipmentsData" => $equipmentsData ]);
    //     // if (count($temperatureData)) {
    //     // }
    //     // else {
    //     //     return response()->json(['status' => true, 'message' => 'No Data Found', 'data' => "N/A" ]);
    //     // }
    // }



    public function getAllTemperatureData(Request $request){

        // dd($request->employeecode);
        $employee = Employees::where('employeecode', $request->employeecode)->first();
    
        // Check if the employee record exists and if the employee is an admin
        // dd($employee);        
        if ($employee && $employee->isadmin == 1) {
            // If the employee is an admin, fetch all temperature data
            // $temperatureData = TemperatureCategory::select('id', 'equipment_name', 'temperature_value', 'created_at', 'created_by');
            $temperatureData = TemperatureCategory::select('id', 'equipment_name', 'temperature_value', DB::raw('DATE_ADD(created_at, INTERVAL 2 HOUR) as created_at'), 'created_by');
        } else {
            // If the employee is not an admin, fetch only the employee's temperature data
            // $temperatureData = TemperatureCategory::where('created_by', $request->employeecode)
            //     ->select('id', 'equipment_name', 'temperature_value', 'created_at', 'created_by');
            $temperatureData = TemperatureCategory::where('created_by', $request->employeecode)
                ->select('id', 'equipment_name', 'temperature_value', DB::raw('DATE_ADD(created_at, INTERVAL 2 HOUR) as created_at'), 'created_by');
        }
    
        // Apply date filters if provided
        if($request->date){
            $date = date('Y-m-d', strtotime($request->date));
            if($request->edate) {
                $edate = date('Y-m-d', strtotime($request->edate));
                $temperatureData->whereRaw("DATE(created_at) BETWEEN '$date' AND '$edate'");
            } else {
                $temperatureData->whereRaw("DATE(created_at) = '$date'");
            }
        } else {
            if(!$request->date && !$request->employee ) {
                $temperatureData->whereRaw("DATE(created_at) = curdate()");
            }
        }
    
        // Apply employeecode filter if provided
        if($request->employee){
            $temperatureData->where('created_by', trim($request->employee));
        }
    

        $temperatureData = $temperatureData->orderBy('created_at', 'DESC')->get();
    
        $equipmentsData = Equipments::get();
    
        return response()->json(['status' => true, 'message' => 'Today Temperature Data', 'temperatureData' => $temperatureData, "equipmentsData" => $equipmentsData ]);
    }
    




    public function getTempById(Request $request, $id){
        $reports = TemperatureCategory::where('id', $id)->orderBy('created_at', 'desc')->first(); 

        if ($reports) {
            return response()->json(['status' => true, 'message' => 'data found', 'data'=> $reports]);
        }else {
            return response()->json(['status' => false, 'message' => 'no data found', 'data'=> 'N/A']);
        }

    }

    public function updateTemperatureData(Request $request){
        $id = $request->id;
        try {
            DB::beginTransaction();
            // dd($id);
            
            $updateTemperature = TemperatureCategory::find($id);
            
            if(!$updateTemperature) {
                return response()->json(['status' => false, 'message' => 'Temperature data not found']);
            }
            
            $updateTemperature->updated_by = $request->employeecode;
            $updateTemperature->equipment_name = $request->equipment_name;
            $updateTemperature->temperature_value = $request->temperature_value;
            $updateTemperature->updated_at = Carbon::now()->toDateTimeString();
            $updateTemperature->save();
    
            DB::commit();
    
            return response()->json(['status' => true, 'message' => 'Temperature data updated']);
        } catch(\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deleteTemperature($id)
    {
        try {
            DB::beginTransaction();

            $temperature = TemperatureCategory::find($id);

            if (!$temperature) {
                return response()->json(['status' => false, 'message' => 'Temperature data not found']);
            }

            $temperature->delete();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Temperature data soft deleted']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
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

    public function deleteEquipment($id)
    {
        try {
            DB::beginTransaction();

            $trasabilityProd = Equipments::find($id);

            if (!$trasabilityProd) {
                return response()->json(['status' => false, 'message' => 'Equipment data not found']);
            }

            $trasabilityProd->delete();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Equipment data deleted']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }



}
