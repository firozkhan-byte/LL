<?php

namespace App\Repositories\Contracts;

use App\Models\BinInventory;
use App\Models\StockTransfer;
use App\Models\WarehouseBin;
use Illuminate\Pagination\LengthAwarePaginator;

interface WarehouseRepositoryInterface
{
    // Hierarchical query
    public function getRacksForWarehouse(string $warehouseId): array;

    public function getShelvesForRack(string $rackId): array;

    public function getBinsForShelf(string $shelfId): array;

    // Bin & BinInventory operations
    public function findBin(string $binId): ?WarehouseBin;

    public function findBinByCode(string $code): ?WarehouseBin;

    public function getBinInventory(string $binId): array;

    public function adjustBinStock(string $binId, string $productId, float $quantity, ?string $batch = null, ?string $expiry = null): BinInventory;

    // Transfer operations
    public function getStockTransfers(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function createStockTransfer(array $data): StockTransfer;

    public function executeStockTransfer(string $transferId): bool;
}
