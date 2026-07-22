<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\CRMService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CRMApiController extends Controller
{
    protected CRMService $crmService;

    public function __construct(CRMService $crmService)
    {
        $this->crmService = $crmService;
    }

    /**
     * Get customer profile and wallet details.
     */
    public function getProfile(string $phone): JsonResponse
    {
        $customer = Customer::with(['profile', 'wallet'])
            ->where('phone', $phone)
            ->first();

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'membership_type' => $customer->membership_type,
                'loyalty_points' => $customer->loyalty_points,
                'wallet_balance' => $customer->wallet ? $customer->wallet->balance : 0.00,
                'birthday' => $customer->profile ? $customer->profile->birthday?->format('Y-m-d') : null,
                'anniversary' => $customer->profile ? $customer->profile->anniversary?->format('Y-m-d') : null,
                'preferences' => $customer->profile ? $customer->profile->preferences : null,
            ],
        ]);
    }

    /**
     * Create a support ticket from remote apps.
     */
    public function createTicket(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'type' => 'required|in:feedback,complaint,support',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        $ticket = $this->crmService->createTicket([
            'customer_id' => $validated['customer_id'],
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'open',
            'priority' => $validated['priority'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Support ticket submitted successfully.',
            'data' => [
                'ticket_id' => $ticket->id,
                'status' => $ticket->status,
            ],
        ], 201);
    }
}
