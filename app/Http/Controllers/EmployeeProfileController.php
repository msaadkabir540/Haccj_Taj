<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;

use App\Http\Requests;
use App\Http\Controllers\Controller;
// use DB;
use App\Http\Controllers\RuntimeException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeProfileController extends Controller
{

    public function validateEmployee($employeecode){
        $response = [
            'status' => false,
            'message' => 'employee_not_found',
        ];

        if(!$employeecode){
            return response()->json($response);
        }

        $employee = Employees::where('employeecode', $employeecode)->get();
        
        if(count($employee) > 0){
            $response['status'] = true;
            $response['employee'] = $employee;
            $response['message'] = 'employee_found';
        }

        return response()->json($response);
    }

    public function createEmployee(Request $request){
        
        // $data = json_decode($request->getContent());
        
        $employee = Employees::where('employeecode', $request->employeecode)->get();
        if (count($employee) > 0) {
            return response()->json(['status' => true, 'message' => 'employee already added', 'data' => null]);
        }

        try
        {
            DB::beginTransaction();
            $addEmployee = new Employees();
            $addEmployee->employeecode  = $request->employeecode;
            $addEmployee->name  = $request->name;
            $addEmployee->email  = $request->email;
            $addEmployee->dob  = $request->dob;
            $addEmployee->contact_no = $request->contact_no;
            $addEmployee->address = $request->address;
            $addEmployee->department = $request->department;
            $addEmployee->isadmin = $request->isadmin;
            $addEmployee->created_at = Carbon::now()->toDateTimeString();
            // $addEmployee->updated_at = Carbon::now()->toDateTimeString();
            $addEmployee->save();
            

            if(!$addEmployee) {
                DB::rollback();
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'employee_added', 'data' => null]);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return response()->json(['status' => false, 'message' => 'Something Went Wrong', 'data' => null]);
        }
    







    }

    public function updateEmployeeData(Request $request){
        $id = $request->id;
        try {
            DB::beginTransaction();

            $employeeData = Employees::find($id);

            if (!$employeeData) {
                return response()->json(['status' => false, 'message' => 'Employee data not found']);
            }

            $employeeData->updated_by  = $request->employeecode;
            $employeeData->name  = $request->name;
            $employeeData->email  = $request->email;
            $employeeData->dob  = $request->dob;
            $employeeData->contact_no = $request->contact_no;
            $employeeData->address = $request->address;
            $employeeData->department = $request->department;
            $employeeData->isadmin = $request->isadmin;
            $employeeData->updated_at = Carbon::now()->toDateTimeString();
            $employeeData->save();
    
            DB::commit();
    
            return response()->json(['status' => true, 'message' => 'Employee Updated']);
        } catch(\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function deleteEmployee($id)
    {
        try {
            DB::beginTransaction();

            $employeeData = Employees::find($id);

            if (!$employeeData) {
                return response()->json(['status' => false, 'message' => 'employeeData data not found']);
            }

            $employeeData->delete();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'employeeData data deleted']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }


    public function getAllEmployeeData(Request $request){

        $employeeData = Employees::get();
        if (count($employeeData)) {
            return response()->json(['status' => true, 'message' => 'All Employee Data', 'data' => $employeeData ]);
        }
        else {
            return response()->json(['status' => true, 'message' => 'No Employee Found', 'data' => "N/A" ]);
        }

    }


}
