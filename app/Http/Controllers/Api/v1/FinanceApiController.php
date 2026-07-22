<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\FinanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FinanceApiController extends Controller
{
    protected FinanceService $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    /**
     * Get financial statement summaries.
     */
    public function getFinancialStatements(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'profit_and_loss' => $this->financeService->getProfitLoss(),
                'balance_sheet' => $this->financeService->getBalanceSheet(),
                'trial_balance' => $this->financeService->getTrialBalance(),
            ],
        ]);
    }

    /**
     * Post a manual journal entry remotely via API.
     */
    public function postExternalJournal(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'description' => 'required|string',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
        ]);

        try {
            $entry = $this->financeService->postJournal($validated);

            return response()->json([
                'success' => true,
                'message' => 'Journal entry posted successfully to GL.',
                'data' => [
                    'reference_number' => $entry->reference_number,
                ],
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
