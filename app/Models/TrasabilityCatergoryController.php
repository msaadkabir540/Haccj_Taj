<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;
use App\Models\TrasabilityCategory;
use App\Models\TrasabilityProdType;
use App\Models\TrasabilityProdName;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrasabilityCatergoryController extends Controller
{

    public function addTrasabilityData(Request $request){
            // dd($request->all());
        try
        {
            DB::beginTransaction();
            $addTrasability = new TrasabilityCategory();
            $addTrasability->created_by  = $request->employeecode;
            $addTrasability->trasability_name  = $request->trasability_name;
            $addTrasability->trasability_type = $request->trasability_type;
            $addTrasability->image_name = $request->file('image') ? $request->file('image')->getClientOriginalName() : "";
            $addTrasability->image = $request->file('image') ? $request->file('image')->move('uploads/', $addTrasability->image_name) : "";
            $addTrasability->created_at = Carbon::now()->toDateTimeString();
            $addTrasability->expire_at = $request->expire_at;
            // $addTrasability->expire_at = $request->expire_at;
            // $addTrasability->updated_at = Carbon::now()->toDateTimeString();
            $addTrasability->save();

            if(!$addTrasability) {
                DB::rollback();
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Trasability Added']);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e]);
        }
    
    }

    // public function getAllTrasabilityData(Request $request){
    //     // dd($request);
        
    //         // Get the JSON data from the request body
    //         // $jsonData = $request->getContent();
        
    //         // // Decode the JSON data into an associative array
    //         // $data = json_decode($jsonData, true);

    //         $data = $request->input();

        
        
        

    //     $employee = Employees::where('employeecode', isset($data['employeecode']))->first();
    
    //     // Check if the employee record exists and if the employee is an admin
    //     if ($employee && $employee->isadmin == 1) {
    //         // If the employee is an admin, fetch all temperature data
    //         $trasabilityData = TrasabilityCategory::select('id', 'trasability_name', 'trasability_type', 'image', 'image_name', 'expire_at', 'created_at', 'created_by');
    //     } else {
    //         // If the employee is not an admin, fetch only the employee's temperature data
    //         $trasabilityData = TrasabilityCategory::where('created_by', isset($data['employeecode']))
    //             ->select('id', 'trasability_name', 'trasability_type', 'image', 'image_name', 'expire_at', 'created_at', 'created_by');
    //     }
    
    //     // Apply date filters if provided
    //     if(isset($data['date'])){
    //         $date = date('Y-m-d', strtotime(isset($data['date'])));
    //         if(isset($data['edate'])) {
    //             $edate = date('Y-m-d', strtotime(isset($data['edate'])));
    //             $trasabilityData->whereRaw("DATE(created_at) BETWEEN '$date' AND '$edate'");
    //         } else {
    //             $trasabilityData->whereRaw("DATE(created_at) = '$date'");
    //         }
    //     } else {
    //         if(!isset($data['date']) && !isset($data['employee'])) {
    //             $trasabilityData->whereRaw("DATE(created_at) = curdate()");
    //             // $trasabilityData->whereRaw("DATE(created_at) BETWEEN CURDATE() - INTERVAL 4 DAY AND CURDATE()");

    //         }
    //     }

    
    //     // Apply employeecode filter if provided
    //     if(isset($data['employee'])){
    //         $trasabilityData->where('created_by', $data['employee']);
    //     }
    

    //     $trasabilityData = $trasabilityData->orderBy('created_at', 'DESC')->get();
    









    //     $fourDaysAgo = Carbon::today()->subDays(4);

    //     // $trasabilityData = TrasabilityCategory::whereDate('created_at', Carbon::today())->where('created_by', $employeecode)->get();
    //     // $trasabilityData = TrasabilityCategory::where('created_at', '>=', $fourDaysAgo)
    //     // ->where('created_by', $employeecode)
    //     // ->get();

    //     $trasabilityProdData = TrasabilityProdType::get();

    //     // if (count($trasabilityData)) {
    //         return response()->json(['status' => true, 'message' => 'All Trasability Data', 'trasabilityData' => $trasabilityData, 'trasabilityProdData' => $trasabilityProdData ]);
    //     // }
    //     // else {
    //     //     return response()->json(['status' => true, 'message' => 'No Trasability Found', 'data' => "N/A" ]);
    //     // }

    // }


    public function getAllTrasabilityData(Request $request) {
        // dd($request);
        // Retrieve the employee record based on the provided employeecode
        $employee = Employees::where('employeecode', $request->employeecode)->first();
    
        // Initialize the trasabilityData query
        $trasabilityData = TrasabilityCategory::query();
    
        // Check if the employee record exists and if the employee is an admin
        if ($employee && $employee->isadmin == 1) {
            // If the employee is an admin, fetch all trasability data
            $trasabilityData->select('id', 'trasability_name', 'trasability_type', 'image', 'image_name', 'expire_at', 'created_at', 'created_by');
        } else {
            // If the employee is not an admin, fetch only the employee's trasability data
            $trasabilityData->where('created_by', $request->employeecode)
                            ->select('id', 'trasability_name', 'trasability_type', 'image', 'image_name', 'expire_at', 'created_at', 'created_by');
        }
    
        // Apply date filters if provided
        if ($request->date) {
            $date = date('Y-m-d', strtotime($request->date));
            if ($request->edate) {
                $edate = date('Y-m-d', strtotime($request->edate));
                $trasabilityData->whereRaw("DATE(created_at) BETWEEN '$date' AND '$edate'");
            } else {
                $trasabilityData->whereRaw("DATE(created_at) = '$date'");
            }
        } else {
            if (!$request->date && !$request->employee) {
                $trasabilityData->whereDate('created_at', today());
            }
        }
    
        // Apply employeecode filter if provided
        if ($request->employee) {
            $trasabilityData->where('created_by', $request->employee);
        }
    
        // Continue building your query as needed
    
        // Finally, retrieve the data and return the response
        $trasabilityData = $trasabilityData->orderBy('created_at', 'DESC')->get();
    

        $fourDaysAgo = Carbon::today()->subDays(4);

        // $trasabilityData = TrasabilityCategory::whereDate('created_at', Carbon::today())->where('created_by', $employeecode)->get();
        // $trasabilityData = TrasabilityCategory::where('created_at', '>=', $fourDaysAgo)
        // ->where('created_by', $employeecode)
        // ->get();

        $trasabilityProdData = TrasabilityProdType::get();
        $trasabilityProdNameData = TrasabilityProdName::get();

        // if (count($trasabilityData)) {
            return response()->json(['status' => true, 'message' => 'All Trasability Data', 'trasabilityData' => $trasabilityData, 'trasabilityProdData' => $trasabilityProdData , 'trasabilityProdNameData' => $trasabilityProdNameData ]);
        // }
        // else {
        //     return response()->json(['status' => true, 'message' => 'No Trasability Found', 'data' => "N/A" ]);
        // }
        

    }
    

    public function getTrasabilityById(Request $request, $id){
        $reports = TrasabilityCategory::where('id', $id)->orderBy('created_at', 'desc')->first(); 

        if ($reports) {
            return response()->json(['status' => true, 'message' => 'data found', 'data'=> $reports]);
        }else {
            return response()->json(['status' => false, 'message' => 'no data found', 'data'=> 'N/A']);
        }

    }


    public function updateTrasabilityData(Request $request)
    {
        $id = $request->id;
        try {
            DB::beginTransaction();

            $updateTrasability = TrasabilityCategory::find($id);

            if (!$updateTrasability) {
                return response()->json(['status' => false, 'message' => 'Trasability data not found']);
            }

            // Update the attributes based on the request data
            $updateTrasability->updated_by = $request->employeecode;
            $updateTrasability->trasability_name = $request->trasability_name;
            $updateTrasability->trasability_type = $request->trasability_type;
            $updateTrasability->expire_at = $request->expire_at;
            $updateTrasability->updated_at = Carbon::now()->toDateTimeString();
            $updateTrasability->save();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Trasability data updated']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }


    public function deleteTrasability($id)
    {
        try {
            DB::beginTransaction();

            $trasability = TrasabilityCategory::find($id);

            if (!$trasability) {
                return response()->json(['status' => false, 'message' => 'Trasability data not found']);
            }

            $trasability->delete();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Trasability data deleted']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }




    public function addTrasabilityProdType(Request $request){
        
        try
        {
            DB::beginTransaction();
            $addProdType = new TrasabilityProdType();
            $addProdType->product_type  = $request->product_type;
            $addProdType->employeecode  = $request->employeecode;
            $addProdType->created_at = Carbon::now()->toDateTimeString();
            // $addProdType->updated_at = Carbon::now()->toDateTimeString();;
            $addProdType->save();
            

            if(!$addProdType) {
                DB::rollback();
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Trasability Product Type Added']);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e]);
        }
    

    }


    public function getAllTrasabilityProd(Request $request){

        $trasabilityProdData = TrasabilityProdType::get();
        if (count($trasabilityProdData)) {
            return response()->json(['status' => true, 'message' => 'All Trasability Product Type Data', 'data' => $trasabilityProdData ]);
        }
        else {
            return response()->json(['status' => true, 'message' => 'No Trasability Product Type Found', 'data' => "N/A" ]);
        }

    }


    public function getAllTrasabilityProdName(Request $request){

        $trasabilityProdData = TrasabilityProdName::get();
        if (count($trasabilityProdData)) {
            return response()->json(['status' => true, 'message' => 'All Trasability Product Name Data', 'data' => $trasabilityProdData ]);
        }
        else {
            return response()->json(['status' => true, 'message' => 'No Trasability Product Name Found', 'data' => "N/A" ]);
        }

    }

    public function deleteTrasabilityProd($id)
    {
        try {
            DB::beginTransaction();

            $trasabilityProd = TrasabilityProdType::find($id);

            if (!$trasabilityProd) {
                return response()->json(['status' => false, 'message' => 'Trasability Product data not found']);
            }

            $trasabilityProd->delete();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Trasability Product data deleted']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }


}
