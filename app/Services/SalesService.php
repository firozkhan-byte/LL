<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Repositories\Contracts\SalesRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SalesService
{
    protected SalesRepositoryInterface $salesRepo;

    public function __construct(SalesRepositoryInterface $salesRepo)
    {
        $this->salesRepo = $salesRepo;
    }

    /**
     * Process checkout/creation of a sales order.
     */
    public function createOrder(array $data): SalesOrder
    {
        return DB::transaction(function () use ($data) {
            return $this->salesRepo->createSalesOrder($data);
        });
    }

    /**
     * Transition order status (e.g., pending -> processing -> shipped -> delivered).
     */
    public function transitionStatus(string $orderId, string $status): bool
    {
        return DB::transaction(function () use ($orderId, $status) {
            return $this->salesRepo->transitionOrderStatus($orderId, $status);
        });
    }

    /**
     * Process return of items for a delivered/completed order.
     */
    public function processReturn(array $data): SalesReturn
    {
        return DB::transaction(function () use ($data) {
            return $this->salesRepo->processSalesReturn($data);
        });
    }

    /**
     * Fetch general sales metrics & margins.
     */
    public function getAnalyticsSummary(): array
    {
        $grossSales = SalesOrder::where('status', '!=', 'cancelled')->sum('total_amount');
        $netReturns = SalesReturn::where('status', 'completed')->sum('refund_amount');

        // Let's calculate total cost of goods sold (COGS) based on product purchase prices
        $cogs = 0.00;
        $orderItems = SalesOrderItem::whereHas('order', function ($q) {
            $q->where('status', '!=', 'cancelled');
        })->get();

        foreach ($orderItems as $item) {
            $product = $item->product;
            if ($product) {
                $cogs += ($item->quantity * $product->purchase_price);
            }
        }

        // Adjust COGS for returned products (reduce cost of goods sold)
        $returnedItems = SalesReturnItem::whereHas('salesReturn', function ($q) {
            $q->where('status', 'completed');
        })->get();

        foreach ($returnedItems as $retItem) {
            $product = $retItem->product;
            if ($product) {
                $cogs -= ($retItem->quantity * $product->purchase_price);
            }
        }

        $netSales = max(0.00, $grossSales - $netReturns);
        $grossMarginAmount = max(0.00, $netSales - $cogs);
        $grossMarginPercentage = $netSales > 0 ? ($grossMarginAmount / $netSales) * 100 : 0.00;

        return [
            'gross_sales' => $grossSales,
            'net_returns' => $netReturns,
            'net_sales' => $netSales,
            'cogs' => $cogs,
            'gross_margin_amount' => $grossMarginAmount,
            'gross_margin_percentage' => $grossMarginPercentage,
        ];
    }
}
