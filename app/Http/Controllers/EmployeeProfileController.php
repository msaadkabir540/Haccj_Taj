<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;

use App\Http\Requests;
use App\Http\Controllers\Controller;
// use DB;
use App\Http\Controllers\RuntimeException;
use Illuminate\Support\Facades\DB;

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
}
