<?php

namespace App\Http\Controllers;

use App\Models\ScheduledNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ScheduledNotificationController extends Controller
{
    /**
     * GET /scheduled-notifications
     * List schedules with optional filters & pagination.
     * Query params: employeecode, status, day_of_week, per_page
     */
    public function getNotification(Request $request): JsonResponse
    {
        $query = ScheduledNotification::query();

        if ($request->filled('employeecode')) {
            $query->where('employeecode', $request->string('employeecode'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

    if ($request->filled('day_of_week')) {
    $val = (int) $request->input('day_of_week');
    if ($val === 7) {
        $query->where('day_of_week', '7'); // only everyday schedules
    } else {
        $query->whereRaw('FIND_IN_SET(?, day_of_week)', [$val]);
    }
}

        $perPage = (int) $request->input('per_page', 15);

        return response()->json(
            $query->latest()->paginate($perPage)
        );
    }

    /**
     * POST /scheduled-notifications
     * Create a new scheduled notification.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'employeecode'  => ['required', 'string'],
            'title'         => ['required', 'string', 'max:255'],
            'body'          => ['required', 'string'],
            'day_of_week'   => ['required', 'string'], // 0=Sun..6=Sat
            'send_time'     => ['required', 'date_format:H:i'],        // HH:MM (24h)
            'timezone'      => ['nullable', 'timezone'],
            'is_recurring'  => ['nullable', 'boolean'],
            'status'        => ['nullable', Rule::in(['active','paused'])],
        ]);

        $data['timezone']     = $data['timezone']     ?? config('app.timezone', 'UTC');
        $data['is_recurring'] = $data['is_recurring'] ?? true;
        $data['status']       = $data['status']       ?? 'active';

        $sn = ScheduledNotification::create($data);

        return response()->json([
            'message' => 'Scheduled notification created.',
            'data'    => $sn,
        ], 201);
    }

    /**
     * GET /scheduled-notifications/{id}
     * Show a single schedule.
     */
    public function show($id): JsonResponse
    {
        $sn = ScheduledNotification::findOrFail($id);
        return response()->json($sn);
    }

    /**
     * PUT/PATCH /scheduled-notifications/{id}
     * Update a schedule (partial updates allowed).
     */
    public function update(Request $request, $id): JsonResponse
    {
        $sn = ScheduledNotification::findOrFail($id);

        $data = $request->validate([
            'employeecode'  => ['sometimes', 'required', 'string'],
            'title'         => ['sometimes', 'required', 'string', 'max:255'],
            'body'          => ['sometimes', 'required', 'string'],
            'day_of_week'   => ['required', 'string'],
            'send_time'     => ['sometimes', 'required', 'date_format:H:i'],
            'timezone'      => ['sometimes', 'required', 'timezone'],
            'is_recurring'  => ['sometimes', 'required', 'boolean'],
            'status'        => ['sometimes', 'required', Rule::in(['active','paused'])],
        ]);

        $sn->update($data);

        return response()->json([
            'message' => 'Scheduled notification updated.',
            'data'    => $sn,
        ]);
    }

    /**
     * DELETE /scheduled-notifications/{id}
     * Delete a schedule.
     */
    public function destroy($id): JsonResponse
    {
        $sn = ScheduledNotification::findOrFail($id);
        $sn->delete();

        return response()->json([
            'message' => 'Scheduled notification deleted.',
        ]);
    }

    /**
     * PATCH /scheduled-notifications/{id}/pause
     * Pause a schedule.
     */
    public function pause($id): JsonResponse
    {
        $sn = ScheduledNotification::findOrFail($id);
        $sn->update(['status' => 'paused']);

        return response()->json([
            'message' => 'Schedule paused.',
            'data'    => $sn,
        ]);
    }

    /**
     * PATCH /scheduled-notifications/{id}/resume
     * Resume a schedule.
     */
    public function resume($id): JsonResponse
    {
        $sn = ScheduledNotification::findOrFail($id);
        $sn->update(['status' => 'active']);

        return response()->json([
            'message' => 'Schedule resumed.',
            'data'    => $sn,
        ]);
    }
}
