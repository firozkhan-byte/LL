<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Services\SalesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalesApiController extends Controller
{
    protected SalesService $salesService;

    public function __construct(SalesService $salesService)
    {
        $this->salesService = $salesService;
    }

    /**
     * Get order details and tracking status.
     */
    public function trackOrder(string $orderNumber): JsonResponse
    {
        $order = SalesOrder::with(['items.product', 'warehouse'])
            ->where('order_number', $orderNumber)
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Sales order not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_number' => $order->order_number,
                'order_type' => $order->order_type,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'total_amount' => $order->total_amount,
                'warehouse' => $order->warehouse->name,
                'delivery_address' => $order->delivery_address,
                'items' => $order->items->map(fn ($item) => [
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                ]),
            ],
        ]);
    }

    /**
     * Post/Create a wholesale/corporate order via REST API.
     */
    public function createCorporateOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_id' => 'nullable|exists:customers,id',
            'order_type' => 'required|in:corporate,wholesale',
            'delivery_address' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.1',
            'items.*.unit_price' => 'required|numeric|min:0.0',
        ]);

        $subtotal = 0.00;
        $itemsPayload = [];

        foreach ($validated['items'] as $item) {
            $qty = floatval($item['quantity']);
            $price = floatval($item['unit_price']);
            $totalPrice = $qty * $price;
            $subtotal += $totalPrice;

            $itemsPayload[] = [
                'product_id' => $item['product_id'],
                'quantity' => $qty,
                'unit_price' => $price,
                'total_price' => $totalPrice,
            ];
        }

        $taxAmount = $subtotal * 0.18; // 18% GST
        $totalAmount = $subtotal + $taxAmount;

        $order = $this->salesService->createOrder([
            'warehouse_id' => $validated['warehouse_id'],
            'customer_id' => $validated['customer_id'] ?? null,
            'order_type' => $validated['order_type'],
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'delivery_address' => $validated['delivery_address'],
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'items' => $itemsPayload,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Corporate/wholesale order registered successfully.',
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
            ],
        ], 201); // Created status
    }
}
