<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Services\DeliveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryApiController extends Controller
{
    protected DeliveryService $deliveryService;

    public function __construct(DeliveryService $deliveryService)
    {
        $this->deliveryService = $deliveryService;
    }

    /**
     * Get active deliveries assigned to a driver.
     */
    public function getAssignedDeliveries(Request $request): JsonResponse
    {
        $agentId = $request->query('agent_id');

        if (! $agentId) {
            return response()->json([
                'success' => false,
                'message' => 'agent_id query parameter is required.',
            ], 422);
        }

        $deliveries = Delivery::with('salesOrder.customer')
            ->where('delivery_agent_id', $agentId)
            ->whereIn('status', ['assigned', 'in_transit'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $deliveries,
        ]);
    }

    /**
     * Update transit coordinates of delivery agent.
     */
    public function updateGPSLocation(Request $request, string $deliveryId): JsonResponse
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $success = $this->deliveryService->updateGPS($deliveryId, $validated['lat'], $validated['lng']);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'GPS coordinates updated.' : 'Failed to update GPS coordinates.',
        ]);
    }

    /**
     * OTP-secured signature checkout verification.
     */
    public function confirmOTPCheckout(Request $request, string $deliveryId): JsonResponse
    {
        $validated = $request->validate([
            'otp' => 'required|string',
            'signature' => 'required|string',
            'photo_url' => 'nullable|string',
        ]);

        $success = $this->deliveryService->confirmDeliveryWithOTP(
            $deliveryId,
            $validated['otp'],
            $validated['signature'],
            $validated['photo_url'] ?? null
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Delivery successfully validated and checked out.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP verification code. Delivery checkout rejected.',
        ], 422);
    }
}
