<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\Warehouse;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class InventoryService
{
    protected InventoryRepositoryInterface $inventoryRepository;

    protected StockValuationService $valuationService;

    public function __construct(
        InventoryRepositoryInterface $inventoryRepository,
        StockValuationService $valuationService
    ) {
        $this->inventoryRepository = $inventoryRepository;
        $this->valuationService = $valuationService;
    }

    public function getLedgers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->inventoryRepository->getLedgerLogs($filters, $perPage);
    }

    public function getStockCard(string $warehouseId): array
    {
        return $this->inventoryRepository->getRealTimeStockCard($warehouseId);
    }

    public function createAdjustment(array $data): StockAdjustment
    {
        return $this->inventoryRepository->createStockAdjustment($data);
    }

    public function getValuationsComparison(string $warehouseId): array
    {
        $products = Product::all();
        $fifoTotal = 0.00;
        $lifoTotal = 0.00;
        $wacTotal = 0.00;

        foreach ($products as $p) {
            $fifoTotal += $this->valuationService->calculateFIFO($p->id, $warehouseId);
            $lifoTotal += $this->valuationService->calculateLIFO($p->id, $warehouseId);
            $wacTotal += $this->valuationService->calculateWeightedAverage($p->id, $warehouseId);
        }

        return [
            'fifo' => $fifoTotal,
            'lifo' => $lifoTotal,
            'wac' => $wacTotal,
        ];
    }

    public function verifyExciseCompliance(string $productId, string $warehouseId): array
    {
        $product = Product::find($productId);
        if (! $product) {
            return [
                'success' => false,
                'message' => 'Product not found.'
            ];
        }

        $warehouse = Warehouse::find($warehouseId);
        if (! $warehouse) {
            return [
                'success' => false,
                'message' => 'Warehouse not found.'
            ];
        }

        // Determine state
        $state = 'Maharashtra';
        $code = strtoupper($warehouse->code);
        if (str_contains($code, 'MUM') || str_contains($code, 'PUN')) {
            $state = 'Maharashtra';
        } elseif (str_contains($code, 'GOA')) {
            $state = 'Goa';
        } elseif (str_contains($code, 'BLR') || str_contains($code, 'KA')) {
            $state = 'Karnataka';
        } else {
            $setting = \App\Models\CompanySetting::first();
            if ($setting && $setting->state) {
                $state = $setting->state;
            }
        }

        // If product has no brand, skip compliance checks
        if (! $product->brand_id) {
            return [
                'success' => true,
                'message' => 'Product has no brand. Skipping brand registration check.'
            ];
        }

        // Check if brand has active registration in this state
        $registration = \App\Models\BrandRegistration::where('brand_id', $product->brand_id)
            ->where('state', $state)
            ->where('status', 'active')
            ->where('expiry_date', '>=', now()->format('Y-m-d'))
            ->first();

        if (! $registration) {
            return [
                'success' => false,
                'message' => "Excise Compliance Failure: The brand for product '{$product->name}' is not registered or registration has expired in state '{$state}'."
            ];
        }

        return [
            'success' => true,
            'message' => "Compliance verified. Brand registered in '{$state}' under excise code: '{$registration->excise_code}'."
        ];
    }
}
