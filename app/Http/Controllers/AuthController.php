<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Employees;
// use Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;


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
    try {
        $request->validate([
            'employeecode' => 'required|employeecode',
            'password' => 'required|string',
            'fcm_token' => 'nullable|string', // ✅ optional FCM token
        ]);

        $user = User::where('employeecode', $request->employeecode)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid employeecode or password',
                'access_token' => null,
            ], 401);
        }

        // ✅ Update FCM token if provided
       if ($request->filled('fcm_token')) {
            $user->fem_token_mobile = $request->fcm_token; // direct assignment
            $user->save();
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $employeeData = Employees::where('employeecode', $user->employeecode)->first();

        $currentTime = Carbon::now();
        $expiryTime = $currentTime->addDays(3); // token expiry
        $formattedTime = $expiryTime->format('Y-m-d H:i:s');

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'access_token' => $token,
            'expires_at' => $formattedTime,
            'token_type' => 'bearer',
            'employee' => $employeeData,
        ], 200)->cookie('expiration', $formattedTime);

    } catch (ValidationException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong while logging in',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    // public function login(Request $request)
    // {

      
    //     $request->validate([
    //         'employeecode' => 'required|employeecode',
    //         'password' => 'required|string',
    //     ]);

    //     $user = User::where('employeecode', $request->employeecode)->first();

    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json([
    //             'status' => false,
    //             'token' => null,
    //             'message' => 'Unauthorized'
    //         ], 401);
    //     }

    //     $token = $user->createToken('auth_token')->plainTextToken;
    //     $employeeData = Employees::where('employeecode', $user->employeecode)->first();

    //     // $expiryDateTime = now()->addDays(3); // Calculate expiration date and time
    //     $currentTime = Carbon::now();

    //     // Add 15 minutes to the current time
    //     // $fifteenMinutesLater = $currentTime->addMinutes(20);
    //     $fifteenMinutesLater = $currentTime->addDays(3);
        
    //     // Choose your desired format for output
    //     $formattedTime = $fifteenMinutesLater->format('Y-m-d H:i:s'); // Example format (change as needed)
        
        
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'login_successfully',
    //         'access_token' => $token,
    //         'expires_at' => $formattedTime, // Format the expiry date and time
    //         'token_type' => 'bearer',
    //         'employee' => $employeeData,
    //     ])->cookie('expiration', $formattedTime);
        
    // }
    
    public function getAllUsers()
{
    try {
        $users = User::all(); // fetch all records from users table

        return response()->json([
            'status' => true,
            'message' => 'Users retrieved successfully',
            'data' => $users
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Failed to retrieve users',
            'error' => $e->getMessage()
        ], 500);
    }
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
