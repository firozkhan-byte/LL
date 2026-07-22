<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\WarehouseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarehouseApiController extends Controller
{
    protected WarehouseService $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function binDetails(string $code): JsonResponse
    {
        $bin = $this->warehouseService->getBinByCode($code);
        if (! $bin) {
            return response()->json([
                'success' => false,
                'message' => 'Storage bin not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $bin,
        ]);
    }

    public function adjustStock(Request $request): JsonResponse
    {
        $request->validate([
            'bin_id' => 'required|exists:warehouse_bins,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0',
        ]);

        $batch = $request->get('batch_number');
        $expiry = $request->get('expiry_date');

        $inventory = $this->warehouseService->adjustStock(
            $request->bin_id,
            $request->product_id,
            $request->quantity,
            $batch,
            $expiry
        );

        return response()->json([
            'success' => true,
            'message' => 'Bin inventory adjusted successfully.',
            'data' => $inventory,
        ]);
    }
}
