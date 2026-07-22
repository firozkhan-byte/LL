<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierApiController extends Controller
{
    protected SupplierService $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'search',
            'rating',
            'credit_days',
            'status',
        ]);

        // Default to active status for API unless requested otherwise
        if (! isset($filters['status'])) {
            $filters['status'] = 'active';
        }

        $perPage = intval($request->get('per_page', 15));
        $suppliers = $this->supplierService->getSuppliers($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $suppliers->items(),
            'meta' => [
                'current_page' => $suppliers->currentPage(),
                'last_page' => $suppliers->lastPage(),
                'per_page' => $suppliers->perPage(),
                'total' => $suppliers->total(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $supplier = $this->supplierService->getSupplier($id);
        if (! $supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $supplier,
        ]);
    }
}
