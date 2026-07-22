<?php

namespace App\Services;

use App\Models\StockLedger;

class StockValuationService
{
    /**
     * Compute total inventory value using FIFO (First In First Out).
     */
    public function calculateFIFO(string $productId, string $warehouseId): float
    {
        $transactions = StockLedger::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->orderBy('created_at', 'asc')
            ->get();

        $batches = [];
        $totalOut = 0.00;

        foreach ($transactions as $t) {
            $qty = floatval($t->quantity);
            if ($qty > 0) {
                $batches[] = [
                    'qty' => $qty,
                    'price' => floatval($t->unit_price),
                ];
            } else {
                $totalOut += abs($qty);
            }
        }

        // Apply outflows chronologically (FIFO)
        $valuation = 0.00;
        foreach ($batches as &$b) {
            if ($totalOut > 0) {
                if ($totalOut >= $b['qty']) {
                    $totalOut -= $b['qty'];
                    $b['qty'] = 0;
                } else {
                    $b['qty'] -= $totalOut;
                    $totalOut = 0;
                }
            }
            $valuation += $b['qty'] * $b['price'];
        }

        return $valuation;
    }

    /**
     * Compute total inventory value using LIFO (Last In First Out).
     */
    public function calculateLIFO(string $productId, string $warehouseId): float
    {
        $transactions = StockLedger::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->orderBy('created_at', 'asc')
            ->get();

        $batches = [];
        $totalOut = 0.00;

        foreach ($transactions as $t) {
            $qty = floatval($t->quantity);
            if ($qty > 0) {
                $batches[] = [
                    'qty' => $qty,
                    'price' => floatval($t->unit_price),
                ];
            } else {
                $totalOut += abs($qty);
            }
        }

        // Apply outflows backward (LIFO)
        for ($i = count($batches) - 1; $i >= 0; $i--) {
            if ($totalOut > 0) {
                if ($totalOut >= $batches[$i]['qty']) {
                    $totalOut -= $batches[$i]['qty'];
                    $batches[$i]['qty'] = 0;
                } else {
                    $batches[$i]['qty'] -= $totalOut;
                    $totalOut = 0;
                }
            }
        }

        $valuation = 0.00;
        foreach ($batches as $b) {
            $valuation += $b['qty'] * $b['price'];
        }

        return $valuation;
    }

    /**
     * Compute total inventory value using Weighted Average Costing.
     */
    public function calculateWeightedAverage(string $productId, string $warehouseId): float
    {
        $transactions = StockLedger::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->get();

        $totalQtyIn = 0.00;
        $totalCostIn = 0.00;
        $currentBalance = 0.00;

        foreach ($transactions as $t) {
            $qty = floatval($t->quantity);
            $currentBalance += $qty;

            if ($qty > 0) {
                $totalQtyIn += $qty;
                $totalCostIn += ($qty * floatval($t->unit_price));
            }
        }

        if ($totalQtyIn == 0) {
            return 0.00;
        }

        $averagePrice = $totalCostIn / $totalQtyIn;

        return max(0.00, $currentBalance * $averagePrice);
    }
}
