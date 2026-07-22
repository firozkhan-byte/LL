<?php

namespace App\Services;

use App\Models\BinInventory;
use App\Models\StockTransfer;
use App\Models\WarehouseBin;
use App\Repositories\Contracts\WarehouseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class WarehouseService
{
    protected WarehouseRepositoryInterface $warehouseRepository;

    public function __construct(WarehouseRepositoryInterface $warehouseRepository)
    {
        $this->warehouseRepository = $warehouseRepository;
    }

    public function getRacks(string $warehouseId): array
    {
        return $this->warehouseRepository->getRacksForWarehouse($warehouseId);
    }

    public function getBin(string $binId): ?WarehouseBin
    {
        return $this->warehouseRepository->findBin($binId);
    }

    public function getBinByCode(string $code): ?WarehouseBin
    {
        return $this->warehouseRepository->findBinByCode($code);
    }

    public function adjustStock(string $binId, string $productId, float $quantity, ?string $batch = null, ?string $expiry = null): BinInventory
    {
        return $this->warehouseRepository->adjustBinStock($binId, $productId, $quantity, $batch, $expiry);
    }

    public function getTransfers(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->warehouseRepository->getStockTransfers($filters, $perPage);
    }

    public function createTransfer(array $data): StockTransfer
    {
        $data['status'] = 'pending';

        return $this->warehouseRepository->createStockTransfer($data);
    }

    public function executeTransfer(string $transferId): bool
    {
        return $this->warehouseRepository->executeStockTransfer($transferId);
    }
}
