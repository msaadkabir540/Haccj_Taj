<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;
use App\Models\CleaningCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleaningCatergoryController extends Controller
{

    public function addCleaningData(Request $request){
            // dd($request->all());
        try
        {
            DB::beginTransaction();
            $addCleaning = new CleaningCategory();
            $addCleaning->created_by  = $request->employeecode;
            $addCleaning->cleaning_area  = $request->cleaning_area;
            $addCleaning->image_name = $request->file('image') ? $request->file('image')->getClientOriginalName() : "";
            $addCleaning->image = $request->file('image') ? $request->file('image')->move('uploads/', $addCleaning->image_name) : "";
            $addCleaning->created_at = Carbon::now()->toDateTimeString();
            // $addCleaning->updated_at = Carbon::now()->toDateTimeString();
            $addCleaning->save();

            if(!$addCleaning) {
                DB::rollback();
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Cleaning Added']);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e]);
        }
    
    }


    public function updateCleaningData(Request $request){
        try {
            $id = $request->id;
            DB::beginTransaction();

            $cleaning = CleaningCategory::find($id);

            if (!$cleaning) {
                return response()->json(['status' => false, 'message' => 'cleaning data not found']);
            }
            
            $cleaning->updated_by = $request->employeecode;
            $cleaning->cleaning_area = $request->cleaning_area;
            $cleaning->updated_at = Carbon::now()->toDateTimeString();
            $cleaning->save();
    
            DB::commit();
    
            return response()->json(['status' => true, 'message' => 'cleaning Updated']);
        } catch(\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deleteCleaning($id)
    {
        try {
            DB::beginTransaction();

            $cleaning = CleaningCategory::find($id);

            if (!$cleaning) {
                return response()->json(['status' => false, 'message' => 'cleaning data not found']);
            }

            $cleaning->delete();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'cleaning data deleted']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }



    public function getAllCleaningData(Request $request, $employeecode, $area){

        $cleaningData = CleaningCategory::whereDate('created_at', Carbon::today())
        ->where('created_by', $employeecode)
        ->where('cleaning_area', $area)
        ->get();


        if (count($cleaningData)) {
            return response()->json(['status' => true, 'message' => 'All Cleaning Data', 'cleaningData' => $cleaningData ]);
        }
        else {
            return response()->json(['status' => true, 'message' => 'No Cleaning Found', 'data' => "N/A" ]);
        }

    }



}
