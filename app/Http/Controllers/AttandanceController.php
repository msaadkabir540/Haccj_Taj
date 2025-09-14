<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attandance;
use App\Models\Employees;

use Illuminate\Validation\ValidationException;

use Carbon\Carbon;

class AttandanceController extends Controller
{
    // Add attendance



//     public function checkAttendance(Request $request)
// {
//     $request->validate([
//         'employeecode' => 'required|string',
//         'location' => 'required|string|regex:/^-?\d{1,3}\.\d+,-?\d{1,3}\.\d+$/', // "lat,long"
//         'time' => 'required|date_format:Y-m-d H:i:s',
//         'checkStatus' => 'required|in:in,out',
//     ]);

//     try {
//         // Split location into lat/long
//         [$latitude, $longitude] = explode(',', $request->location);
//         $attendanceDate = Carbon::parse($request->time)->toDateString();

//         if ($request->checkStatus === 'in') {
//             // Check if employee already checked in today (whether checked out or not)
//             $alreadyCheckedInToday = Attandance::where('employeecode', $request->employeecode)
//                 ->whereDate('check_in', $attendanceDate)
//                 ->exists();

//             if ($alreadyCheckedInToday) {
//                 $lastCheckIn = Attandance::where('employeecode', $request->employeecode)
//                     ->whereDate('check_in', $attendanceDate)
//                     ->latest('check_in')
//                     ->first();

//                 return response()->json([
//                     'status' => 'error',
//                     'message' => 'You have already checked in today at ' . Carbon::parse($lastCheckIn->check_in)->format('Y-m-d H:i:s'),
//                 ], 409);
//             }

//             // Insert a new check-in record
//             $attendance = Attandance::create([
//                 'employeecode' => $request->employeecode,
//                 'check_in_latitude' => $latitude,
//                 'check_in_longitude' => $longitude,
//                 'check_in' => $request->time,
//             ]);

//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'Check-in recorded successfully',
//                 'data' => $attendance,
//                 'status' => 200
//             ], 200);
//         }

//         if ($request->checkStatus === 'out') {
//             // Find the latest check-in without checkout
//             $attendance = Attandance::where('employeecode', $request->employeecode)
//                 ->whereNull('check_out')
//                 ->latest('check_in')
//                 ->first();

//             if (!$attendance) {
//                 return response()->json([
//                     'status' => 'error',
//                     'message' => 'No active check-in found for this employee.',
//                 ], 404);
//             }

//             // Update checkout
//             $attendance->update([
//                 'check_out_latitude' => $latitude,
//                 'check_out_longitude' => $longitude,
//                 'check_out' => $request->time,
//             ]);

//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'Check-out recorded successfully',
//                 'data' => $attendance,
//                 'status' => 200
//             ], 200);
//         }
//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'Something went wrong',
//             'error' => $e->getMessage(),
//         ], 500);
//     }
// }



public function checkAttendance(Request $request)
{
    $request->validate([
        'employeecode' => 'required|employeecode',
        'location'     => 'required|string|regex:/^-?\d{1,3}\.\d+,-?\d{1,3}\.\d+$/', // "lat,long"
        'time'         => 'required|date_format:Y-m-d H:i:s',
        'checkStatus'  => 'required|in:in,out',
    ]);

    try {
        [$latitude, $longitude] = explode(',', $request->location);
        $requestTime = Carbon::parse($request->time)->format('Y-m-d H:i:s');

        if ($request->checkStatus === 'in') {
            // Always create a new check-in, regardless of existing open sessions
            $attendance = Attandance::create([
                'employeecode'       => $request->employeecode,
                'check_in_latitude'  => $latitude,
                'check_in_longitude' => $longitude,
                'check_in'           => $requestTime,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Check-in recorded successfully.',
                'data'    => $attendance,
            ], 200);
        }

        // checkStatus === 'out' -> close the latest open session (most recent null check_out)
        $latestOpen = Attandance::where('employeecode', $request->employeecode)
            ->whereNull('check_out')
            ->latest('check_in')
            ->first();

        if (!$latestOpen) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No open check-in found to check out.',
            ], 404);
        }

        // Ensure checkout time is not before its check-in
        if (Carbon::parse($requestTime)->lt(Carbon::parse($latestOpen->check_in))) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Check-out time cannot be earlier than the latest open check-in ('
                    . Carbon::parse($latestOpen->check_in)->format('Y-m-d H:i:s') . ').',
            ], 422);
        }

        $latestOpen->update([
            'check_out_latitude'  => $latitude,
            'check_out_longitude' => $longitude,
            'check_out'           => $requestTime,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Check-out recorded successfully.',
            'data'    => $latestOpen,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Something went wrong',
            'error'   => $e->getMessage(),
        ], 500);
    }
}


// ✅ Check-In API
 public function checkIn(Request $request)
    {
        try {
            $request->validate([
                'employeecode' => 'required|string|exists:employees,employeecode',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'time' => 'required|date_format:Y-m-d H:i:s', // e.g. "2025-09-06 15:40:54"
            ]);

            // Always create a new record
            $attendance = Attandance::create([
                'employeecode' => $request->employeecode,
                'check_in_latitude' => $request->latitude,
                'check_in_longitude' => $request->longitude,
                'check_in' => $request->time,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Checked in successfully',
                'data' => $attendance,
                 'status' => 200
            ], 200); // Created
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong during check-in',
                'error' => $e->getMessage()
            ], 500);
        }
    }





// ✅ Check-Out API (always updates latest record)
public function checkOut(Request $request)
{
    try {
        $request->validate([
            'employeecode' => 'required|string|exists:employees,employeecode',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'time' => 'required|date_format:Y-m-d H:i:s', // e.g. "2025-09-06 18:10:00"
        ]);

        // Always get the latest attendance record
        $attendance = Attandance::where('employeecode', $request->employeecode)
            ->latest()
            ->first();

        if (!$attendance) {
            return response()->json([
                'status' => 'error',
                'message' => 'No check-in record found for this employee.'
            ], 404); 
        }

        $attendance->update([
            'check_out_latitude' => $request->latitude,
            'check_out_longitude' => $request->longitude,
            'check_out' => $request->time,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Checked out successfully',
            'data' => $attendance
        ], 200); 
    } catch (ValidationException $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422); 
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong during check-out',
            'error' => $e->getMessage()
        ], 500); 
    }
}




public function getAllAttendance(Request $request)
{
    try {
        // 1. Verify the logged-in employee
        $loggedInEmployee = Employees::where('employeecode', $request->employeeCode)->first();

        if (!$loggedInEmployee || $loggedInEmployee->isadmin != 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not allowed to get the attendance',
                'data' => []
            ], 403); // 403 Forbidden
        }

        // 2. Build attendance query
        $query = Attandance::with('employee');

        // ✅ Filter attendance by employeeId if provided
        if ($request->filled('employeeId')) {
            $query->where('employeecode', $request->employeeId);
        }

        $from = $request->input('from_date');
        $to   = $request->input('to_date');

        if (!empty($from) && !empty($to)) {
            $query->whereBetween('check_in', [
                $from . " 00:00:00",
                $to . " 23:59:59"
            ]);
        } else {
            $query->whereDate('check_in', now()->toDateString());
        }

        $attendance = $query->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Attendance fetched successfully',
            'data' => $attendance,
            'status' => 200
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $e->errors(),
            'status' => 422
        ], 422);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong while fetching attendance',
            'error' => $e->getMessage(),
            'status' => 500
        ], 500);
    }
}



public function isEmployeeCheckedIn(Request $request)
{
    try {
        $request->validate([
            'employeecode' => 'required|employeecode|exists:employees,employeecode',
            'date' => 'required|date_format:Y-m-d', // e.g. "2025-09-07"
        ]);

        $date = Carbon::parse($request->date);

        // Check if employee has a check-in record on that date
        $attendance = Attandance::where('employeecode', $request->employeecode)
            ->whereDate('check_in', $date->toDateString()) // match only the date part
            ->first();

        if ($attendance) {
            return response()->json([
                'status' => 200,
                'exist' => true,
                'message' => "Employee checked in on {$date->toDateString()}.",
                'data' => $attendance,
            ], 200);
        }

        return response()->json([
            'status' => 200,
            'exist' => false,
            'message' => "Employee did not check in on {$date->toDateString()}.",
        ], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'status' => 422,
            'exist' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'exist' => false,
            'message' => 'Something went wrong while checking attendance',
            'error' => $e->getMessage()
        ], 500);
    }
}



}
