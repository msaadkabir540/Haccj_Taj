<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;
use App\Models\ChecklistCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChecklistCatergoryController extends Controller
{

    public function addChecklistData(Request $request){
            // dd($request->all());
        try
        {
            DB::beginTransaction();
            $addChecklist = new ChecklistCategory();
            $addChecklist->created_by  = $request->employeecode;
            $addChecklist->task  = $request->task;
            $addChecklist->message = $request->message;
            $addChecklist->assign_to = $request->assign_to;
            $addChecklist->created_at = Carbon::now()->toDateTimeString();
            // $addChecklist->updated_at = Carbon::now()->toDateTimeString();
            $addChecklist->save();

            if(!$addChecklist) {
                DB::rollback();
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Checklist Added']);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e]);
        }
    
    }

    public function getAllChecklistData($employeecode){

        $checklistData = ChecklistCategory::whereDate('created_at', Carbon::today())->where('assign_to', $employeecode)->get();
        if (count($checklistData)) {
            return response()->json(['status' => true, 'message' => 'All Checklist Data', 'data' => $checklistData ]);
        }
        else {
            return response()->json(['status' => true, 'message' => 'No Checklist Found', 'data' => "N/A" ]);
        }

    }



}
