<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryManager extends Component
{
    use WithPagination;

    public string $activeTab = 'stock'; // stock, ledger, adjustments, valuation

    public string $selectedWarehouseId = '';

    public string $search = '';

    public string $txFilter = '';

    // Adjustment Form Fields
    public bool $showingAdjModal = false;

    public string $adjReason = 'damaged';

    public string $adjRemarks = '';

    public array $adjItems = [];

    protected $queryString = [
        'activeTab' => ['except' => 'stock'],
        'selectedWarehouseId' => ['except' => ''],
    ];

    public function mount(): void
    {
        abort_if(Gate::denies('manage-company'), 403);

        $wh = Warehouse::first();
        if ($wh && empty($this->selectedWarehouseId)) {
            $this->selectedWarehouseId = $wh->id;
        }

        $this->adjItems = [['product_id' => '', 'adjustment_type' => 'decrement', 'quantity' => 1.00, 'batch_number' => '']];
    }

    public function render(InventoryService $inventoryService)
    {
        $warehouses = Warehouse::all();
        $products = Product::all();

        // 1. Stock Grid
        $stockCard = [];
        if (! empty($this->selectedWarehouseId)) {
            $stockCard = $inventoryService->getStockCard($this->selectedWarehouseId);
        }

        // 2. Ledgers
        $ledgerLogs = [];
        if ($this->activeTab === 'ledger') {
            $ledgerLogs = $inventoryService->getLedgers([
                'search' => $this->search,
                'transaction_type' => $this->txFilter,
                'warehouse_id' => $this->selectedWarehouseId,
            ], 15);
        }

        // 3. Costing Comparison
        $valuationTotals = [];
        if ($this->activeTab === 'valuation' && ! empty($this->selectedWarehouseId)) {
            $valuationTotals = $inventoryService->getValuationsComparison($this->selectedWarehouseId);
        }

        return view('livewire.admin.inventory-manager', [
            'warehouses' => $warehouses,
            'products' => $products,
            'stockCard' => $stockCard,
            'ledgerLogs' => $ledgerLogs,
            'valuationTotals' => $valuationTotals,
        ])->layout('layouts.app');
    }

    public function changeTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->search = '';
        $this->txFilter = '';
        $this->resetPage();
    }

    // Adjustment forms
    public function openAdjModal(): void
    {
        $this->adjReason = 'damaged';
        $this->adjRemarks = '';
        $this->adjItems = [['product_id' => '', 'adjustment_type' => 'decrement', 'quantity' => 1.00, 'batch_number' => '']];
        $this->showingAdjModal = true;
    }

    public function addAdjItem(): void
    {
        $this->adjItems[] = ['product_id' => '', 'adjustment_type' => 'decrement', 'quantity' => 1.00, 'batch_number' => ''];
    }

    public function removeAdjItem(int $index): void
    {
        unset($this->adjItems[$index]);
        $this->adjItems = array_values($this->adjItems);
    }

    public function saveAdjustment(InventoryService $inventoryService): void
    {
        $this->validate([
            'adjReason' => 'required|string',
            'adjItems.*.product_id' => 'required|exists:products,id',
            'adjItems.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $payload = [
            'warehouse_id' => $this->selectedWarehouseId,
            'reason' => $this->adjReason,
            'status' => 'completed',
            'created_by' => auth()->id(),
            'remarks' => $this->adjRemarks,
            'items' => $this->adjItems,
        ];

        $inventoryService->createAdjustment($payload);

        session()->flash('message', 'Inventory adjustment logged. Central ledger adjusted.');
        $this->showingAdjModal = false;
    }
}
