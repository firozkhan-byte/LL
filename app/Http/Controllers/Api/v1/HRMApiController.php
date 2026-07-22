<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Services\HRMService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HRMApiController extends Controller
{
    protected HRMService $hrmService;

    public function __construct(HRMService $hrmService)
    {
        $this->hrmService = $hrmService;
    }

    /**
     * Get employee active roster list.
     */
    public function getEmployeeRoster(): JsonResponse
    {
        $employees = Employee::where('status', 'active')->get();

        return response()->json([
            'success' => true,
            'data' => $employees,
        ]);
    }

    /**
     * Submit biometric clock-in attendance registry punch.
     */
    public function biometricClockIn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'check_in' => 'required',
            'biometric_device_id' => 'required|string',
        ]);

        $validated['date'] = now()->format('Y-m-d');

        $punch = $this->hrmService->punchAttendance($validated);

        return response()->json([
            'success' => true,
            'message' => 'Attendance punched logged successfully.',
            'data' => [
                'punch_id' => $punch->id,
                'status' => $punch->status,
            ],
        ], 201);
    }
}
