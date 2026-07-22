<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\ExciseLicense;
use App\Services\ExciseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExciseApiController extends Controller
{
    protected ExciseService $exciseService;

    public function __construct(ExciseService $exciseService)
    {
        $this->exciseService = $exciseService;
    }

    /**
     * Fetch monthly GST reports (GSTR1 outward and GSTR3B offsets).
     */
    public function getGSTSummary(Request $request): JsonResponse
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->endOfMonth()->format('Y-m-d'));

        $summary = $this->exciseService->calculateGSTSummary($startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Check details and validity of a specific state excise license.
     */
    public function checkLicenseStatus(string $licenseId): JsonResponse
    {
        $license = ExciseLicense::find($licenseId);

        if (! $license) {
            return response()->json([
                'success' => false,
                'message' => 'Excise license not found.',
            ], 404);
        }

        $isExpiring = $license->expiry_date->isBefore(now()->addDays(30));

        return response()->json([
            'success' => true,
            'data' => [
                'license_number' => $license->license_number,
                'license_type' => $license->license_type,
                'state' => $license->state,
                'expiry_date' => $license->expiry_date->format('Y-m-d'),
                'status' => $license->status,
                'is_expiring_soon' => $isExpiring,
            ],
        ]);
    }
}
