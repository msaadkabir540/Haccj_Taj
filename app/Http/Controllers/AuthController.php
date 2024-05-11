<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Employees;
// use Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;


use Laravel\Sanctum\HasApiTokens;
use DB;


use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;




class AuthController extends Controller
{
    public function register(Request $request)
    {
        $employeecode = $request->get('employeecode');
        $password = $request->get('password');
        
        $isEmployeeExist = Employees::where('employeecode', $employeecode)->where('quit', 0)->distinct()->count();

        
        if($isEmployeeExist == 0){
            return response()->json([
                'status' => false,
                'data' => 'N/A',
                'message' => 'employee_not_exists' 
            ]);
        }
        
        
        $isAccountAlreadyExists = User::where('employeecode', $employeecode)->distinct()->count();
        
        if($isAccountAlreadyExists > 0){
            return response()->json([
                'status' => false,
                'data' => 'N/A',
                'message' => 'account_already_exists'
            ]);
        }
        
        $request->validate([
            // 'name' => 'required|string',
            'employeecode' => 'required|employeecode|unique:users',
            'password' => 'required|string',
        ]);
        
        $user = User::create([
            // 'name' => $request->name,
            'employeecode' => $employeecode,
            'password' => bcrypt($password),
        ]);
        
        // dd('$isAccountAlreadyExists');
        // return response()->json(['user' => $user]);
        return response()->json([
            'status' => true,
            'user' => $user,
            'message' => 'SignUp Successfully'
        ]);
    }


    public function login(Request $request)
    {
        $request->validate([
            'employeecode' => 'required|employeecode',
            'password' => 'required|string',
        ]);

        $user = User::where('employeecode', $request->employeecode)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'token' => null,
                'message' => 'Unauthorized'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $employeeData = Employees::where('employeecode', $user->employeecode)->first();

        return response()->json([
            'status' => true,
            'message' => 'login_successfully',
            'access_token' => $token,
            'expires_in' => Config::get('sanctum.expiration') * 900,
            'token_type' => 'bearer',
            'employee' => $employeeData,
        ]);
    }
    


    // public function logout()
    // {
    //     auth()->guard('web')->logout();

    //     return response()->json(['message' => 'Successfully logged out']);
    // }





    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['status' => true ,'message' => 'Logout Successfully']);
        
    }
}
