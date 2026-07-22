<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryApiController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function stockDetails(string $sku): JsonResponse
    {
        $product = Product::where('sku', $sku)->first();
        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        $warehouse = Warehouse::first();
        $warehouseId = $warehouse ? $warehouse->id : '';

        $stockCards = $this->inventoryService->getStockCard($warehouseId);
        $card = collect($stockCards)->firstWhere('product_id', $product->id);

        return response()->json([
            'success' => true,
            'data' => $card ?? [
                'product_id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'available_qty' => 0.00,
                'reserved_qty' => 0.00,
                'batch_number' => null,
                'expiry_date' => null,
            ],
        ]);
    }

    public function adjustStock(Request $request): JsonResponse
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'reason' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.adjustment_type' => 'required|in:increment,decrement',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $payload = [
            'warehouse_id' => $request->warehouse_id,
            'reason' => $request->reason,
            'status' => 'completed',
            'created_by' => auth()->id() ?? User::first()->id ?? null,
            'remarks' => $request->get('remarks', 'API adjust log.'),
            'items' => $request->items,
        ];

        $adj = $this->inventoryService->createAdjustment($payload);

        return response()->json([
            'success' => true,
            'message' => 'Inventory API adjustment processed successfully.',
            'data' => $adj,
        ]);
    }
}
