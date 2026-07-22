<?php

namespace App\Livewire\Admin;

use App\Models\BinInventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseBin;
use App\Services\WarehouseService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class WarehouseManager extends Component
{
    use WithPagination;

    public string $activeTab = 'map'; // map, transfers, scanner, counting

    public string $selectedWarehouseId = '';

    public string $search = '';

    // Putaway fields
    public bool $showingPutawayModal = false;

    public string $putawayBinId = '';

    public string $putawayProductId = '';

    public float $putawayQuantity = 1.00;

    public string $putawayBatch = '';

    // Transfer fields
    public bool $showingTransferModal = false;

    public string $txFromWarehouseId = '';

    public string $txToWarehouseId = '';

    public array $txItems = [];

    // Scanner console fields
    public string $scannedBarcode = '';

    public string $scannedBinCode = '';

    public ?array $scanLookupResult = null;

    // Cycle counting fields
    public string $countBinId = '';

    public string $countProductId = '';

    public float $countRecordedQty = 0.00;

    public float $countDiscrepancyQty = 0.00;

    protected $queryString = [
        'activeTab' => ['except' => 'map'],
        'selectedWarehouseId' => ['except' => ''],
    ];

    public function mount(): void
    {
        abort_if(Gate::denies('manage-company'), 403);

        $wh = Warehouse::first();
        if ($wh && empty($this->selectedWarehouseId)) {
            $this->selectedWarehouseId = $wh->id;
        }

        $this->txItems = [['product_id' => '', 'quantity' => 1, 'batch' => '']];
    }

    public function render(WarehouseService $warehouseService)
    {
        $warehouses = Warehouse::all();
        $products = Product::all();

        // 1. Map Hierarchy
        $racks = [];
        if (! empty($this->selectedWarehouseId)) {
            $racks = $warehouseService->getRacks($this->selectedWarehouseId);
        }

        // 2. Transfers
        $transfersList = [];
        if ($this->activeTab === 'transfers') {
            $transfersList = $warehouseService->getTransfers(['search' => $this->search], 10);
        }

        // 3. Counting List
        $binInventoriesList = [];
        if ($this->activeTab === 'counting') {
            $binInventoriesList = BinInventory::with(['bin.shelf.rack.warehouse', 'product'])
                ->whereHas('bin.shelf.rack', function ($q) {
                    $q->where('warehouse_id', $this->selectedWarehouseId);
                })->paginate(15);
        }

        // Fetch all bins in this warehouse
        $warehouseBins = WarehouseBin::whereHas('shelf.rack', function ($q) {
            $q->where('warehouse_id', $this->selectedWarehouseId);
        })->get();

        return view('livewire.admin.warehouse-manager', [
            'warehouses' => $warehouses,
            'products' => $products,
            'racks' => $racks,
            'transfersList' => $transfersList,
            'binInventoriesList' => $binInventoriesList,
            'warehouseBins' => $warehouseBins,
        ])->layout('layouts.app');
    }

    public function changeTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->search = '';
        $this->resetPage();
    }

    // Putaways
    public function openPutawayModal(string $binId): void
    {
        $this->putawayBinId = $binId;
        $this->putawayProductId = '';
        $this->putawayQuantity = 1.00;
        $this->putawayBatch = 'BATCH-'.strtoupper(Str::random(6));
        $this->showingPutawayModal = true;
    }

    public function executePutaway(WarehouseService $warehouseService): void
    {
        $this->validate([
            'putawayBinId' => 'required|exists:warehouse_bins,id',
            'putawayProductId' => 'required|exists:products,id',
            'putawayQuantity' => 'required|numeric|min:0.01',
        ]);

        $binInv = BinInventory::where('bin_id', $this->putawayBinId)
            ->where('product_id', $this->putawayProductId)
            ->first();

        $existingQty = $binInv ? $binInv->quantity : 0;
        $newQty = $existingQty + $this->putawayQuantity;

        $warehouseService->adjustStock($this->putawayBinId, $this->putawayProductId, $newQty, $this->putawayBatch);
        session()->flash('message', 'Putaway completed successfully.');
        $this->showingPutawayModal = false;
    }

    // Transfers
    public function openTransferModal(): void
    {
        $this->txFromWarehouseId = $this->selectedWarehouseId;
        $this->txToWarehouseId = '';
        $this->txItems = [['product_id' => '', 'quantity' => 1, 'batch' => '']];
        $this->showingTransferModal = true;
    }

    public function addTxItem(): void
    {
        $this->txItems[] = ['product_id' => '', 'quantity' => 1, 'batch' => ''];
    }

    public function removeTxItem(int $index): void
    {
        unset($this->txItems[$index]);
        $this->txItems = array_values($this->txItems);
    }

    public function saveTransfer(WarehouseService $warehouseService): void
    {
        $this->validate([
            'txFromWarehouseId' => 'required|different:txToWarehouseId',
            'txToWarehouseId' => 'required|exists:warehouses,id',
            'txItems.*.product_id' => 'required|exists:products,id',
            'txItems.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $payload = [
            'from_warehouse_id' => $this->txFromWarehouseId,
            'to_warehouse_id' => $this->txToWarehouseId,
            'remarks' => 'Inter-warehouse transfer logged.',
            'items' => $this->txItems,
        ];

        $transfer = $warehouseService->createTransfer($payload);
        $warehouseService->executeTransfer($transfer->id);

        session()->flash('message', 'Stock transfer completed and quantities adjusted.');
        $this->showingTransferModal = false;
    }

    // Barcode QR scanner lookups
    public function runBarcodeLookup(WarehouseService $warehouseService): void
    {
        $this->scanLookupResult = null;

        // Search product by barcode/sku
        $product = Product::where('sku', $this->scannedBarcode)
            ->orWhere('barcode', $this->scannedBarcode)
            ->orWhere('name', 'like', "%{$this->scannedBarcode}%")
            ->first();

        // Search bin by code
        $bin = $warehouseService->getBinByCode($this->scannedBinCode);

        if ($product && $bin) {
            $this->scanLookupResult = [
                'type' => 'match',
                'product_id' => $product->id,
                'product_name' => $product->name,
                'bin_id' => $bin->id,
                'bin_code' => $bin->code,
                'bin_name' => $bin->name,
            ];
        } elseif ($product) {
            $this->scanLookupResult = [
                'type' => 'product_only',
                'product_id' => $product->id,
                'product_name' => $product->name,
            ];
        } elseif ($bin) {
            $this->scanLookupResult = [
                'type' => 'bin_only',
                'bin_id' => $bin->id,
                'bin_code' => $bin->code,
                'bin_name' => $bin->name,
            ];
        } else {
            session()->flash('scanner_error', 'No matches found in ERP for scanned codes.');
        }
    }

    public function executeScanPutaway(WarehouseService $warehouseService): void
    {
        if ($this->scanLookupResult && isset($this->scanLookupResult['product_id'], $this->scanLookupResult['bin_id'])) {
            $pId = $this->scanLookupResult['product_id'];
            $bId = $this->scanLookupResult['bin_id'];

            $binInv = BinInventory::where('bin_id', $bId)->where('product_id', $pId)->first();
            $qty = ($binInv ? $binInv->quantity : 0) + 1; // Putaway 1 unit

            $warehouseService->adjustStock($bId, $pId, $qty, 'SCAN-PUTAWAY');
            session()->flash('message', 'Scanner Putaway completed: 1 unit allocated to Bin.');

            $this->scannedBarcode = '';
            $this->scannedBinCode = '';
            $this->scanLookupResult = null;
        }
    }

    // Cycle Counting adjustments
    public function adjustCycleDiscrepancy(string $inventoryId, float $recordedQty): void
    {
        $inv = BinInventory::find($inventoryId);
        if ($inv) {
            $inv->update(['quantity' => $recordedQty]);
            session()->flash('message', 'Physical count discrepancy updated in system logs.');
        }
    }
}
