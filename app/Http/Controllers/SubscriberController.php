<?php
namespace App\Http\Controllers;
use App\Mail\Subscribe;
use App\Models\Subscriber;
use App\Models\Employees;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


use Illuminate\Support\Str;
use Carbon\Carbon;


class SubscriberController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $data = json_decode($request->getContent());
        $employeecode = $data->employeecode;

        // $validator = Validator::make($request->all(), [
        //     'employeecode' => 'required|int',
        // ]);
        

        if ($employeecode == '' || $$employeecode = null) {
            return response()->json($validator->errors(), 422);
        }

        $employee = Employees::where('employeecode', $employeecode)->first();
        // dd($employee);
        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employeecode not exist'
            ], 401);
        }

        $users = User::where('employeecode', $employeecode)->distinct()->count();
        
        if ($users == 0) {
            return response()->json([
                'status' => false,
                'message' => 'SignUp first'
            ], 401);
        }

        $otp = Str::random(6); // Generate a random 6-digit OTP
        $otp = mt_rand(100000, 999999); // Generate a random 6-digit OTP


        // Create a new OTP record or update existing one for the employee
        // $otpRecord = $employee->otps()->create([
        //     'otp' => $otp,
        //     'expires_at' => Carbon::now()->addMinutes(1), // Set expiration time (e.g., 30 minutes)
        // ]);
        // $otpRecord = User::create([
        //     'remember_token' => $otp,
        //     'expires_at' => Carbon::now()->addMinutes(1), // Set expiration time (e.g., 30 minutes)
        // ]);


        $emailData = [
            'name' => $employee->name,
            'employeecode' => $employee->employeecode,
            'otp' => $otp,
        ];

        Mail::to($employee->email)->send(new Subscribe($otp));

        // return response()->json(['message' => 'OTP sent to your email address']);

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to your email address',
            'data' => $emailData
        ]);
    }

    public function resetPassword(Request $request)
    {
        // Validate the request data
        // $request->validate([
        //     'employee_code' => 'required',
        //     'password' => 'required|min:8', // Define your validation rules here
        // ]);

        // Retrieve the employee based on the provided code
        $employee = User::where('employeecode', $request->employeecode)->first();

        if (!$employee) {
        return response()->json([
                'status' => false,
                'message' => 'Account not found'
            ]);
        }

        // Update the password for the employee
        $employee->password = Hash::make($request->password);
        $employee->save();

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully'
        ]);
    }




}