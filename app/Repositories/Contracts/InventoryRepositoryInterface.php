<?php

namespace App\Repositories\Contracts;

use App\Models\StockAdjustment;
use App\Models\StockLedger;
use Illuminate\Pagination\LengthAwarePaginator;

interface InventoryRepositoryInterface
{
    public function getLedgerLogs(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function recordLedgerEntry(array $data): StockLedger;

    public function getRealTimeStockCard(string $warehouseId): array;

    public function createStockAdjustment(array $data): StockAdjustment;
}
