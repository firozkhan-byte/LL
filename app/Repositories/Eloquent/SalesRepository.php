<?php

namespace App\Repositories\Eloquent;

use App\Models\CreditNote;
use App\Models\SalesInvoice;
use App\Models\SalesOrder;
use App\Models\SalesReturn;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use App\Repositories\Contracts\SalesRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SalesRepository implements SalesRepositoryInterface
{
    public function getSalesOrders(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = SalesOrder::with(['customer', 'warehouse', 'items.product']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['order_type'])) {
            $query->where('order_type', $filters['order_type']);
        }
        if (! empty($filters['search'])) {
            $query->where('order_number', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getSalesInvoices(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = SalesInvoice::with(['order.customer']);

        if (! empty($filters['search'])) {
            $query->where('invoice_number', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getSalesReturns(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = SalesReturn::with(['order.customer', 'items.product']);

        if (! empty($filters['search'])) {
            $query->where('return_number', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createSalesOrder(array $data): SalesOrder
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $order = SalesOrder::create($data);
        foreach ($items as $item) {
            $order->items()->create($item);

            // Deduct stock levels by creating a 'sale' transaction in Stock Ledger
            $inventoryRepo = app(InventoryRepositoryInterface::class);
            if ($inventoryRepo) {
                $inventoryRepo->recordLedgerEntry([
                    'product_id' => $item['product_id'],
                    'warehouse_id' => $data['warehouse_id'],
                    'transaction_type' => 'sale',
                    'quantity' => -floatval($item['quantity']),
                    'reference_type' => 'SalesOrder',
                    'reference_id' => $order->id,
                    'unit_price' => $item['unit_price'],
                ]);
            }
        }

        return $order;
    }

    public function transitionOrderStatus(string $orderId, string $status): bool
    {
        $order = SalesOrder::find($orderId);
        if (! $order) {
            return false;
        }

        $order->update(['status' => $status]);

        // Auto-generate Invoice when delivered
        if ($status === 'delivered') {
            $order->update(['payment_status' => 'paid']);
            SalesInvoice::create([
                'sales_order_id' => $order->id,
                'total_amount' => $order->total_amount,
                'status' => 'completed',
            ]);
        }

        return true;
    }

    public function processSalesReturn(array $data): SalesReturn
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $return = SalesReturn::create($data);
        foreach ($items as $item) {
            $return->items()->create($item);

            // Return stock back into the inventory ledger
            $inventoryRepo = app(InventoryRepositoryInterface::class);
            if ($inventoryRepo) {
                $order = SalesOrder::find($data['sales_order_id']);
                $warehouseId = $order ? $order->warehouse_id : null;

                $inventoryRepo->recordLedgerEntry([
                    'product_id' => $item['product_id'],
                    'warehouse_id' => $warehouseId,
                    'transaction_type' => 'return',
                    'quantity' => floatval($item['quantity']),
                    'reference_type' => 'SalesReturn',
                    'reference_id' => $return->id,
                    'unit_price' => $item['refund_unit_price'],
                ]);
            }
        }

        // Issue Credit Note if order has customer
        $order = SalesOrder::find($data['sales_order_id']);
        if ($order && ! empty($order->customer_id)) {
            CreditNote::create([
                'customer_id' => $order->customer_id,
                'sales_return_id' => $return->id,
                'amount' => $data['refund_amount'],
                'status' => 'active',
            ]);
        }

        return $return;
    }
}
