<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\POSService;
use Illuminate\Http\JsonResponse;

class POSApiController extends Controller
{
    protected POSService $posService;

    public function __construct(POSService $posService)
    {
        $this->posService = $posService;
    }

    public function customerDetails(string $phone): JsonResponse
    {
        $customer = $this->posService->getCustomer($phone);
        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $customer,
        ]);
    }

    public function giftCardDetails(string $cardNumber): JsonResponse
    {
        $gc = $this->posService->getGiftCard($cardNumber);
        if (! $gc) {
            return response()->json([
                'success' => false,
                'message' => 'Gift card is invalid or expired.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $gc,
        ]);
    }
}
