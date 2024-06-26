<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;
use App\Models\ChecklistCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;

class ChecklistCatergoryController extends Controller
{

    public function addChecklistData(Request $request){

        $dateStart = $request->assign_start;
        $dateEnd = $request->assign_end;
    
        // Convert string dates to DateTime objects
        $datetime1 = new DateTime($dateStart);
        $datetime2 = new DateTime($dateEnd);
    
        // Calculate the difference in days between the two dates
        $interval = $datetime1->diff($datetime2);
        $days = (int)$interval->format('%a');

        $timePart = $datetime1->format('H:i:s');


        DB::beginTransaction();

            // dd($request->all());
        try
        {






            for ($j = 0; $j <= $days; $j++) {
                        // Clone the start date and add $j days to it
                        $currentDate = clone $datetime1;
                        $currentDate->modify("+$j day");
                        $currentDateTime = $currentDate->format('Y-m-d') . " $timePart";





            $addChecklist = new ChecklistCategory();
            $addChecklist->created_by  = $request->employeecode;
            $addChecklist->task  = $request->task;
            $addChecklist->message = $request->message;
            $addChecklist->assign_to = $request->assign_to;
            $addChecklist->decision = $request->decision;
            $addChecklist->assign_start = $request->assign_start;
            $addChecklist->assign_end = $request->assign_end;
            $addChecklist->created_at = $currentDateTime;
            // $addChecklist->created_at = Carbon::now()->toDateTimeString();
            $addChecklist->save();
            }
            if(!$addChecklist) {
                DB::rollback();
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Checklist Added']);
        }
        catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollback();
            // Optionally, handle the exception (e.g., log the error, return a response)
            return response()->json(['status' => false, 'message' => $e]);
        }

        // catch(\Exception $e)
        // {
        //     DB::rollback();
        //     return response()->json(['status' => false, 'message' => $e]);
        // }
    
    }


    public function updateChecklistData(Request $request){
        try {
            $id = $request->id;
            DB::beginTransaction();

            $checklist = ChecklistCategory::find($id);

            if (!$checklist) {
                return response()->json(['status' => false, 'message' => 'Checklist data not found']);
            }
            
            // $checklist->updated_by = $request->employeecode;
            // $checklist->task = $request->task;
            // $checklist->message = $request->message;
            // $checklist->assign_to = $request->assign_to;
            // $checklist->assign_start = $request->assign_start;
            // $checklist->assign_end = $request->assign_end;
            // $checklist->decision = $request->decision;
            // $checklist->updated_at = Carbon::now()->toDateTimeString();
            // $checklist->save();
            
        
            
            
            if ($request->decision === 'D') {
            // Update only the decision, updated_at, and handle image upload
            $checklist->decision = $request->decision;
            $checklist->updated_at = Carbon::now()->toDateTimeString();

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $image->move(public_path('uploads'), $imageName);

                $checklist->image_name = $imageName;
                $checklist->image = 'uploads/' . $imageName;
            } else {
                $checklist->image_name = "";
                $checklist->image = "";
            }

        } else {
            // Update all fields
            $checklist->updated_by = $request->employeecode;
            $checklist->task = $request->task;
            $checklist->message = $request->message;
            $checklist->assign_to = $request->assign_to;
            $checklist->assign_start = $request->assign_start;
            $checklist->assign_end = $request->assign_end;
            $checklist->decision = $request->decision;
            $checklist->updated_at = Carbon::now()->toDateTimeString();

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $image->move(public_path('uploads'), $imageName);

                $checklist->image_name = $imageName;
                $checklist->image = 'uploads/' . $imageName;
            } else {
                $checklist->image_name = "";
                $checklist->image = "";
            }
        }

        $checklist->save();

            
            
            
            
            
            
            
            
            
            
            
    
            DB::commit();
    
            return response()->json(['status' => true, 'message' => 'Checklist Updated']);
        } catch(\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deleteChecklist($id)
    {
        try {
            DB::beginTransaction();

            $checklist = ChecklistCategory::find($id);

            if (!$checklist) {
                return response()->json(['status' => false, 'message' => 'checklist data not found']);
            }

            $checklist->delete();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'checklist data deleted']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }



    public function getAllChecklistData(Request $request){

        $employee = Employees::where('employeecode', $request->employeecode)->first();
    
        // Check if the employee record exists and if the employee is an admin
        // dd($employee->isadmin == 1);
        if ($employee && $employee->isadmin == 1) {
            // If the employee is an admin, fetch all temperature data
            // $checklistData = ChecklistCategory::select('id', 'task', 'message', 'assign_to','assign_start', 'assign_end', 'decision', 'image', 'image_name', 'created_at', 'created_by');
            $checklistData = ChecklistCategory::select('id', 'task', 'message', 'assign_to','assign_start', 'assign_end', 'decision', 'image', 'image_name', DB::raw('DATE_ADD(created_at, INTERVAL 2 HOUR) as created_at'), 'created_by');
        } else {
            // If the employee is not an admin, fetch only the employee's temperature data
            // $checklistData = ChecklistCategory::where('assign_to', $request->employeecode)
            //     ->select('id', 'task', 'message', 'assign_to','assign_start', 'assign_end', 'decision', 'image', 'image_name', 'created_at', 'created_by');
            $checklistData = ChecklistCategory::where('assign_to', $request->employeecode)
                ->select('id', 'task', 'message', 'assign_to','assign_start', 'assign_end', 'decision', 'image', 'image_name', DB::raw('DATE_ADD(created_at, INTERVAL 2 HOUR) as created_at'), 'created_by');
        }
    
        // Apply date filters if provided
        if($request->date){
            $date = date('Y-m-d', strtotime($request->date));
            if($request->edate) {
                $edate = date('Y-m-d', strtotime($request->edate));
                $checklistData->whereRaw("DATE(created_at) BETWEEN '$date' AND '$edate'");
            } else {
                $checklistData->whereRaw("DATE(created_at) = '$date'");
            }
        } else {
            if(!$request->date && !$request->employee ) {
                $checklistData->whereRaw("DATE(created_at) = curdate()");
            }
        }
    
        // Apply employeecode filter if provided
        if($request->employee){
            $checklistData->where('created_by', trim($request->employee));
        }
        if($request->assign_to){
            $checklistData->where('assign_to', trim($request->assign_to));
        }
    

        $checklistData = $checklistData->orderBy('id', 'DESC')->get();
    

        // $checklistData = ChecklistCategory::whereDate('created_at', Carbon::today())->where('assign_to', $employeecode)->get();
        if (count($checklistData)) {
            return response()->json(['status' => true, 'message' => 'All Checklist Data', 'data' => $checklistData ]);
        }
        else {
            return response()->json(['status' => true, 'message' => 'No Checklist Found', 'data' => "N/A" ]);
        }

    }



}
