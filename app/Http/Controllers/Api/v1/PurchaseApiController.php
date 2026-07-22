<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseApiController extends Controller
{
    protected PurchaseService $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'status']);
        $perPage = intval($request->get('per_page', 15));

        $orders = $this->purchaseService->getPurchaseOrders($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function storeGRN(Request $request): JsonResponse
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'received_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_received' => 'required|numeric|min:0',
        ]);

        $data = [
            'purchase_order_id' => $request->purchase_order_id,
            'received_date' => $request->received_date,
            'received_by' => auth()->id() ?: User::first()->id,
            'status' => 'completed',
            'remarks' => $request->remarks ?: 'Logged via Barcode Scanner API',
            'items' => collect($request->items)->map(function ($item) {
                return [
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity_ordered'] ?? $item['quantity_received'],
                    'quantity_received' => $item['quantity_received'],
                    'quantity_accepted' => $item['quantity_received'],
                    'quantity_rejected' => 0.00,
                    'batch_number' => $item['batch_number'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                ];
            })->all(),
        ];

        $grn = $this->purchaseService->createGRN($data);

        return response()->json([
            'success' => true,
            'message' => 'Goods Receipt Note logged successfully via API.',
            'data' => $grn,
        ], 201);
    }
}
