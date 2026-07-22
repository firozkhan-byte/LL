<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportApiController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Get compiled operational summary metrics.
     */
    public function getSummaryReport(Request $request): JsonResponse
    {
        $startDate = $request->query('start_date', date('Y-m-01'));
        $endDate = $request->query('end_date', date('Y-m-t'));

        $report = $this->reportService->generateEnterpriseReport($startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }
}
