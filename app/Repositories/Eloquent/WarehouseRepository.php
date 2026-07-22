<?php

namespace App\Repositories\Eloquent;

use App\Models\BinInventory;
use App\Models\StockTransfer;
use App\Models\WarehouseBin;
use App\Models\WarehouseRack;
use App\Models\WarehouseShelf;
use App\Repositories\Contracts\WarehouseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class WarehouseRepository implements WarehouseRepositoryInterface
{
    public function getRacksForWarehouse(string $warehouseId): array
    {
        return WarehouseRack::with('shelves.bins')->where('warehouse_id', $warehouseId)->get()->toArray();
    }

    public function getShelvesForRack(string $rackId): array
    {
        return WarehouseShelf::with('bins')->where('rack_id', $rackId)->get()->toArray();
    }

    public function getBinsForShelf(string $shelfId): array
    {
        return WarehouseBin::where('shelf_id', $shelfId)->get()->toArray();
    }

    public function findBin(string $binId): ?WarehouseBin
    {
        return WarehouseBin::with('inventories.product')->find($binId);
    }

    public function findBinByCode(string $code): ?WarehouseBin
    {
        return WarehouseBin::with('inventories.product')->where('code', $code)->first();
    }

    public function getBinInventory(string $binId): array
    {
        return BinInventory::with('product')->where('bin_id', $binId)->get()->toArray();
    }

    public function adjustBinStock(string $binId, string $productId, float $quantity, ?string $batch = null, ?string $expiry = null): BinInventory
    {
        $inv = BinInventory::where('bin_id', $binId)
            ->where('product_id', $productId)
            ->first();

        if ($inv) {
            $inv->quantity = $quantity;
            if ($batch) {
                $inv->batch_number = $batch;
            }
            if ($expiry) {
                $inv->expiry_date = $expiry;
            }
            $inv->save();
        } else {
            $inv = BinInventory::create([
                'bin_id' => $binId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'batch_number' => $batch,
                'expiry_date' => $expiry,
            ]);
        }

        return $inv;
    }

    public function getStockTransfers(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'items.product']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['search'])) {
            $query->where('code', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createStockTransfer(array $data): StockTransfer
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $transfer = StockTransfer::create($data);
        foreach ($items as $item) {
            $transfer->items()->create($item);
        }

        return $transfer;
    }

    public function executeStockTransfer(string $transferId): bool
    {
        $transfer = StockTransfer::with('items')->find($transferId);
        if (! $transfer || $transfer->status === 'completed') {
            return false;
        }

        // Complete the transfer
        $transfer->update(['status' => 'completed']);

        return true;
    }
}
