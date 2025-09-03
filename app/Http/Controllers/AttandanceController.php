<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attandance;
use App\Models\Employees;

class AttandanceController extends Controller
{
    // Add attendance
    public function store(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'time' => 'required|date',
            'employeecode' => 'required|exists:employees,employeecode',
        ]);

        $attendance = Attandance::create([
            'location' => $request->location,
            'time' => $request->time,
            'employeecode' => $request->employeecode,
        ]);

        return response()->json([
            'message' => 'Attendance added successfully',
            'data' => $attendance
        ], 201);
    }

  // Get all attendance with employee details
public function getAllAttendance(Request $request)
{
    $employee = Employees::where('employeecode', $request->employeeCode)->first();

    // Check if employee exists and is admin
    if (!$employee || $employee->isadmin != 1) {
        return response()->json([
            'message' => 'Not allowed to get the attendance',
            'data' => [],
            "status" => 403
        ], 403); // 403 = Forbidden
    }

    $query = Attandance::with('employee');

    $from = $request->input('from_date');
    $to   = $request->input('to_date');

    if (!empty($from) && !empty($to)) {
        // Filter between from_date and to_date
        $query->whereBetween('time', [
            $from . " 00:00:00",
            $to . " 23:59:59"
        ]);
    } else {
        // Default → today’s attendance
        $query->whereDate('time', now()->toDateString());
    }

    $attendance = $query->get();

    return response()->json([
        'message' => 'Attendance fetched successfully',
        'data' => $attendance,
        "status" => 200
    ], 200);
}


}
