<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesInvoice;
use App\Models\SalesOrder;
use App\Models\SalesReturn;
use App\Models\Warehouse;
use App\Services\SalesService;
use Livewire\Component;
use Livewire\WithPagination;

class SalesManager extends Component
{
    use WithPagination;

    public string $activeTab = 'pipeline';

    // Filters
    public string $search = '';

    public string $orderType = '';

    public string $status = '';

    // Create Order Form
    public bool $showingCreateModal = false;

    public string $newOrderType = 'walk_in';

    public ?string $newCustomerId = null;

    public ?string $newWarehouseId = null;

    public string $newDeliveryAddress = '';

    public array $newOrderItems = []; // [['product_id' => '', 'quantity' => 1, 'unit_price' => 0.00]]

    public string $customerSearch = '';

    // Return Form
    public bool $showingReturnModal = false;

    public ?string $selectedOrderId = null;

    public ?SalesOrder $selectedOrderForReturn = null;

    public string $returnReason = 'wrong_item';

    public array $returnItems = []; // [['product_id' => '', 'quantity_to_return' => 0, 'refund_unit_price' => 0.00, 'purchased_qty' => 0]]

    public float $refundAmount = 0.00;

    protected $queryString = [
        'search' => ['except' => ''],
        'orderType' => ['except' => ''],
        'status' => ['except' => ''],
        'activeTab' => ['except' => 'pipeline'],
    ];

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingOrderType(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    // --- Order Pipeline Transitions ---
    public function updateOrderStatus(string $orderId, string $newStatus, SalesService $salesService): void
    {
        $salesService->transitionStatus($orderId, $newStatus);
        session()->flash('success', "Order state transitioned to {$newStatus} successfully.");
    }

    // --- Order Creation ---
    public function openCreateModal(): void
    {
        $this->resetCreateForm();
        $this->newWarehouseId = Warehouse::first()?->id;
        $this->showingCreateModal = true;
    }

    public function addOrderItem(): void
    {
        $products = Product::where('status', 'active')->get();
        if ($products->isEmpty()) {
            return;
        }

        $this->newOrderItems[] = [
            'product_id' => $products->first()->id,
            'quantity' => 1,
            'unit_price' => $products->first()->selling_price,
        ];
    }

    public function removeOrderItem(int $index): void
    {
        unset($this->newOrderItems[$index]);
        $this->newOrderItems = array_values($this->newOrderItems);
    }

    public function updateItemPrice(int $index): void
    {
        $productId = $this->newOrderItems[$index]['product_id'] ?? null;
        if ($productId) {
            $product = Product::find($productId);
            if ($product) {
                $this->newOrderItems[$index]['unit_price'] = $product->selling_price;
            }
        }
    }

    public function saveOrder(SalesService $salesService): void
    {
        $this->validate([
            'newWarehouseId' => 'required|exists:warehouses,id',
            'newOrderType' => 'required|in:online,corporate,wholesale,walk_in',
            'newOrderItems' => 'required|array|min:1',
            'newOrderItems.*.product_id' => 'required|exists:products,id',
            'newOrderItems.*.quantity' => 'required|numeric|min:0.01',
            'newOrderItems.*.unit_price' => 'required|numeric|min:0.00',
        ]);

        $subtotal = 0.00;
        $itemsPayload = [];

        foreach ($this->newOrderItems as $item) {
            $qty = floatval($item['quantity']);
            $price = floatval($item['unit_price']);
            $totalPrice = $qty * $price;
            $subtotal += $totalPrice;

            $itemsPayload[] = [
                'product_id' => $item['product_id'],
                'quantity' => $qty,
                'unit_price' => $price,
                'total_price' => $totalPrice,
            ];
        }

        $taxAmount = $subtotal * 0.18; // 18% GST standard
        $totalAmount = $subtotal + $taxAmount;

        $salesService->createOrder([
            'warehouse_id' => $this->newWarehouseId,
            'customer_id' => $this->newCustomerId,
            'order_type' => $this->newOrderType,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'delivery_address' => $this->newDeliveryAddress,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'items' => $itemsPayload,
        ]);

        session()->flash('success', 'Sales order created and stock reservations recorded.');
        $this->showingCreateModal = false;
        $this->resetCreateForm();
    }

    private function resetCreateForm(): void
    {
        $this->newOrderType = 'walk_in';
        $this->newCustomerId = null;
        $this->newDeliveryAddress = '';
        $this->newOrderItems = [];
        $this->customerSearch = '';
    }

    // --- Customer Returns ---
    public function openReturnModal(string $orderId): void
    {
        $this->selectedOrderId = $orderId;
        $this->selectedOrderForReturn = SalesOrder::with('items.product')->find($orderId);
        $this->returnReason = 'wrong_item';
        $this->returnItems = [];
        $this->refundAmount = 0.00;

        if ($this->selectedOrderForReturn) {
            foreach ($this->selectedOrderForReturn->items as $item) {
                $this->returnItems[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'purchased_qty' => $item->quantity,
                    'quantity_to_return' => 0,
                    'refund_unit_price' => $item->unit_price,
                ];
            }
        }

        $this->showingReturnModal = true;
    }

    public function calculateRefundAmount(): void
    {
        $sub = 0.00;
        foreach ($this->returnItems as $item) {
            $qty = floatval($item['quantity_to_return']);
            $price = floatval($item['refund_unit_price']);
            $sub += ($qty * $price);
        }
        $this->refundAmount = $sub * 1.18; // Apply 18% tax refund standard
    }

    public function saveReturn(SalesService $salesService): void
    {
        $this->validate([
            'returnItems' => 'required|array',
            'returnItems.*.quantity_to_return' => 'required|numeric|min:0',
        ]);

        $itemsPayload = [];
        $totalQtyReturned = 0;

        foreach ($this->returnItems as $item) {
            $qty = floatval($item['quantity_to_return']);
            if ($qty > $item['purchased_qty']) {
                $this->addError('returnItems', "Cannot return more than purchased quantity ({$item['purchased_qty']}) for product: {$item['product_name']}");

                return;
            }

            if ($qty > 0) {
                $totalQtyReturned += $qty;
                $itemsPayload[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $qty,
                    'refund_unit_price' => $item['refund_unit_price'],
                ];
            }
        }

        if ($totalQtyReturned <= 0) {
            $this->addError('returnItems', 'Please select at least one item and quantity to return.');

            return;
        }

        $this->calculateRefundAmount();

        $salesService->processReturn([
            'sales_order_id' => $this->selectedOrderId,
            'reason' => $this->returnReason,
            'refund_amount' => $this->refundAmount,
            'status' => 'completed',
            'items' => $itemsPayload,
        ]);

        session()->flash('success', 'Customer sales return successfully recorded and stock restored.');
        $this->showingReturnModal = false;
    }

    public function render(SalesService $salesService)
    {
        $filters = [
            'search' => $this->search,
            'order_type' => $this->orderType,
            'status' => $this->status,
        ];

        $orders = [];
        $invoices = [];
        $returns = [];
        $analytics = [];

        if ($this->activeTab === 'pipeline') {
            $orders = SalesOrder::with(['customer', 'warehouse', 'items.product'])
                ->when($this->search, fn ($q) => $q->where('order_number', 'like', "%{$this->search}%"))
                ->when($this->orderType, fn ($q) => $q->where('order_type', $this->orderType))
                ->when($this->status, fn ($q) => $q->where('status', $this->status))
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($this->activeTab === 'invoices') {
            $invoices = SalesInvoice::with(['order.customer'])
                ->when($this->search, fn ($q) => $q->where('invoice_number', 'like', "%{$this->search}%"))
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($this->activeTab === 'returns') {
            $returns = SalesReturn::with(['order.customer', 'items.product'])
                ->when($this->search, fn ($q) => $q->where('return_number', 'like', "%{$this->search}%"))
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($this->activeTab === 'analytics') {
            $analytics = $salesService->getAnalyticsSummary();
        }

        $products = Product::where('status', 'active')->orderBy('name')->get();
        $warehouses = Warehouse::where('status', 'active')->get();

        // Dynamic search for loyalty customer dropdowns
        $customers = Customer::when($this->customerSearch, function ($q) {
            $q->where('name', 'like', "%{$this->customerSearch}%")
                ->orWhere('phone', 'like', "%{$this->customerSearch}%");
        })->take(5)->get();

        return view('livewire.admin.sales-manager', [
            'orders' => $orders,
            'invoices' => $invoices,
            'returns' => $returns,
            'analytics' => $analytics,
            'products' => $products,
            'warehouses' => $warehouses,
            'customers' => $customers,
        ])->layout('layouts.app');
    }
}
