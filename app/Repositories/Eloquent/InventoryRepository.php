<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockLedger;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class InventoryRepository implements InventoryRepositoryInterface
{
    public function getLedgerLogs(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = StockLedger::with(['product', 'warehouse']);

        if (! empty($filters['search'])) {
            $query->whereHas('product', function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('sku', 'like', "%{$filters['search']}%");
            });
        }

        if (! empty($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }

        if (! empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function recordLedgerEntry(array $data): StockLedger
    {
        // Compute running balance
        $latest = StockLedger::where('product_id', $data['product_id'])
            ->where('warehouse_id', $data['warehouse_id'])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $latestBalance = $latest ? $latest->balance_after : 0.00;
        $data['balance_after'] = $latestBalance + $data['quantity'];

        return StockLedger::create($data);
    }

    public function getRealTimeStockCard(string $warehouseId): array
    {
        $products = Product::all();
        $stockCard = [];

        foreach ($products as $p) {
            $latest = StockLedger::where('product_id', $p->id)
                ->where('warehouse_id', $warehouseId)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $qty = $latest ? $latest->balance_after : 0.00;

            $stockCard[] = [
                'product_id' => $p->id,
                'sku' => $p->sku,
                'name' => $p->name,
                'available_qty' => $qty,
                'reserved_qty' => 0.00, // placeholder hook for sales reserves
                'batch_number' => $latest ? $latest->batch_number : null,
                'expiry_date' => $latest ? $latest->expiry_date : null,
            ];
        }

        return $stockCard;
    }

    public function createStockAdjustment(array $data): StockAdjustment
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $adj = StockAdjustment::create($data);
        foreach ($items as $item) {
            $adj->items()->create($item);

            // Record Central Stock Ledger entry
            $qty = floatval($item['quantity']);
            $this->recordLedgerEntry([
                'product_id' => $item['product_id'],
                'warehouse_id' => $data['warehouse_id'],
                'transaction_type' => $item['adjustment_type'] === 'increment' ? 'adjustment_add' : 'adjustment_remove',
                'quantity' => $item['adjustment_type'] === 'increment' ? $qty : -$qty,
                'reference_type' => 'StockAdjustment',
                'reference_id' => $adj->id,
                'batch_number' => $item['batch_number'] ?? null,
                'unit_price' => Product::find($item['product_id'])->purchase_price ?? 0.00,
            ]);
        }

        return $adj;
    }
}
