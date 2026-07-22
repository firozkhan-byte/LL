<?php

namespace App\Livewire\Admin;

use App\Models\GoodsReceiptNote;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseManager extends Component
{
    use WithPagination;

    public string $activeTab = 'requisitions'; // requisitions, orders, receipts, invoices

    public string $search = '';

    public string $statusFilter = '';

    // Requisition Creation form fields
    public bool $showingReqModal = false;

    public string $reqNeededDate = '';

    public string $reqRemarks = '';

    public array $reqItems = [];

    // PO Creation form fields
    public bool $showingPoModal = false;

    public string $poSupplierId = '';

    public ?string $poReqId = null;

    public string $poDate = '';

    public string $poPaymentTerms = '30 Days Credit';

    public array $poItems = [];

    // GRN Creation form fields
    public bool $showingGrnModal = false;

    public string $grnPoId = '';

    public string $grnReceivedDate = '';

    public string $grnRemarks = '';

    public array $grnItems = [];

    // Invoice Creation form fields
    public bool $showingInvModal = false;

    public string $invSupplierId = '';

    public ?string $invGrnId = null;

    public string $invNumber = '';

    public string $invDate = '';

    public string $invDueDate = '';

    public array $invItems = [];

    protected $queryString = [
        'activeTab' => ['except' => 'requisitions'],
        'search' => ['except' => ''],
    ];

    public function mount(): void
    {
        abort_if(Gate::denies('manage-company'), 403);
        $this->reqNeededDate = now()->addDays(7)->toDateString();
        $this->poDate = now()->toDateString();
        $this->grnReceivedDate = now()->toDateString();
        $this->invDate = now()->toDateString();
        $this->invDueDate = now()->addDays(30)->toDateString();
    }

    public function render(PurchaseService $purchaseService)
    {
        $filters = [
            'search' => $this->search,
            'status' => $this->statusFilter,
        ];

        $products = Product::all();
        $suppliers = Supplier::all();
        $approvedPOs = PurchaseOrder::where('status', 'approved')->get();
        $completedGRNs = GoodsReceiptNote::all();

        // Query active tab lists
        $requisitionsList = [];
        $purchaseOrdersList = [];
        $grnList = [];
        $invoiceList = [];

        if ($this->activeTab === 'requisitions') {
            $requisitionsList = $purchaseService->getRequisitions($filters, 10);
        } elseif ($this->activeTab === 'orders') {
            $purchaseOrdersList = $purchaseService->getPurchaseOrders($filters, 10);
        } elseif ($this->activeTab === 'receipts') {
            $grnList = $purchaseService->getGRNs($filters, 10);
        } elseif ($this->activeTab === 'invoices') {
            $invoiceList = $purchaseService->getInvoices($filters, 10);
        }

        $metrics = $purchaseService->getStats();

        return view('livewire.admin.purchase-manager', [
            'requisitionsList' => $requisitionsList,
            'purchaseOrdersList' => $purchaseOrdersList,
            'grnList' => $grnList,
            'invoiceList' => $invoiceList,
            'products' => $products,
            'suppliers' => $suppliers,
            'approvedPOs' => $approvedPOs,
            'completedGRNs' => $completedGRNs,
            'metrics' => $metrics,
        ])->layout('layouts.app');
    }

    public function changeTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->search = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    // Requisition Line Manipulation
    public function openReqModal(): void
    {
        $this->reqNeededDate = now()->addDays(7)->toDateString();
        $this->reqRemarks = '';
        $this->reqItems = [['product_id' => '', 'quantity' => 1, 'estimated_cost' => 0.00]];
        $this->showingReqModal = true;
    }

    public function addReqItem(): void
    {
        $this->reqItems[] = ['product_id' => '', 'quantity' => 1, 'estimated_cost' => 0.00];
    }

    public function removeReqItem(int $index): void
    {
        unset($this->reqItems[$index]);
        $this->reqItems = array_values($this->reqItems);
    }

    public function saveRequisition(PurchaseService $purchaseService): void
    {
        $this->validate([
            'reqNeededDate' => 'required|date',
            'reqItems.*.product_id' => 'required|exists:products,id',
            'reqItems.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $payload = [
            'requested_by' => auth()->id(),
            'needed_by_date' => $this->reqNeededDate,
            'remarks' => $this->reqRemarks,
            'status' => 'approved', // Auto approved for testing/seeding flow ease
            'items' => $this->reqItems,
        ];

        $purchaseService->createRequisition($payload);
        session()->flash('message', 'Requisition created successfully.');
        $this->showingReqModal = false;
    }

    // PO Line Manipulation
    public function openPoModal(): void
    {
        $this->poSupplierId = '';
        $this->poReqId = null;
        $this->poDate = now()->toDateString();
        $this->poPaymentTerms = '30 Days Credit';
        $this->poItems = [['product_id' => '', 'quantity' => 1, 'unit_price' => 0.00, 'tax_percent' => 18.00]];
        $this->showingPoModal = true;
    }

    public function addPoItem(): void
    {
        $this->poItems[] = ['product_id' => '', 'quantity' => 1, 'unit_price' => 0.00, 'tax_percent' => 18.00];
    }

    public function removePoItem(int $index): void
    {
        unset($this->poItems[$index]);
        $this->poItems = array_values($this->poItems);
    }

    public function savePurchaseOrder(PurchaseService $purchaseService): void
    {
        $this->validate([
            'poSupplierId' => 'required|exists:suppliers,id',
            'poDate' => 'required|date',
            'poItems.*.product_id' => 'required|exists:products,id',
            'poItems.*.quantity' => 'required|numeric|min:0.01',
            'poItems.*.unit_price' => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        $tax = 0;
        $itemsData = [];

        foreach ($this->poItems as $item) {
            $prod = Product::find($item['product_id']);
            $price = floatval($item['unit_price'] ?: ($prod ? $prod->purchase_price : 0));
            $qty = floatval($item['quantity']);
            $totalItem = $price * $qty;
            $taxItem = $totalItem * (floatval($item['tax_percent']) / 100);

            $subtotal += $totalItem;
            $tax += $taxItem;

            $itemsData[] = [
                'product_id' => $item['product_id'],
                'quantity' => $qty,
                'unit_price' => $price,
                'tax_percent' => $item['tax_percent'],
                'tax_amount' => $taxItem,
                'total_amount' => $totalItem + $taxItem,
            ];
        }

        $payload = [
            'supplier_id' => $this->poSupplierId,
            'purchase_requisition_id' => $this->poReqId ?: null,
            'po_date' => $this->poDate,
            'payment_terms' => $this->poPaymentTerms,
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $subtotal + $tax,
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'items' => $itemsData,
        ];

        $purchaseService->createPurchaseOrder($payload);
        session()->flash('message', 'Purchase Order generated and approved.');
        $this->showingPoModal = false;
    }

    // GRN Line Manipulation
    public function openGrnModal(): void
    {
        $this->grnPoId = '';
        $this->grnReceivedDate = now()->toDateString();
        $this->grnRemarks = '';
        $this->grnItems = [];
        $this->showingGrnModal = true;
    }

    public function loadPoItems(): void
    {
        $this->grnItems = [];
        $po = PurchaseOrder::with('items.product')->find($this->grnPoId);
        if ($po) {
            foreach ($po->items as $item) {
                $this->grnItems[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity_ordered' => $item->quantity,
                    'quantity_received' => $item->quantity,
                    'quantity_accepted' => $item->quantity,
                    'quantity_rejected' => 0.00,
                    'batch_number' => 'BATCH-'.strtoupper(Str::random(6)),
                    'expiry_date' => '',
                ];
            }
        }
    }

    public function saveGRN(PurchaseService $purchaseService): void
    {
        $this->validate([
            'grnPoId' => 'required|exists:purchase_orders,id',
            'grnReceivedDate' => 'required|date',
            'grnItems.*.quantity_received' => 'required|numeric|min:0',
        ]);

        $payload = [
            'purchase_order_id' => $this->grnPoId,
            'received_date' => $this->grnReceivedDate,
            'received_by' => auth()->id(),
            'status' => 'completed',
            'remarks' => $this->grnRemarks,
            'items' => $this->grnItems,
        ];

        $purchaseService->createGRN($payload);
        session()->flash('message', 'Goods Receipt Note logged and stock adjusted.');
        $this->showingGrnModal = false;
    }

    // Invoice matches
    public function openInvModal(): void
    {
        $this->invSupplierId = '';
        $this->invGrnId = null;
        $this->invNumber = '';
        $this->invDate = now()->toDateString();
        $this->invDueDate = now()->addDays(30)->toDateString();
        $this->invItems = [['product_id' => '', 'quantity' => 1, 'unit_price' => 0.00]];
        $this->showingInvModal = true;
    }

    public function addInvItem(): void
    {
        $this->invItems[] = ['product_id' => '', 'quantity' => 1, 'unit_price' => 0.00];
    }

    public function removeInvItem(int $index): void
    {
        unset($this->invItems[$index]);
        $this->invItems = array_values($this->invItems);
    }

    public function saveInvoice(PurchaseService $purchaseService): void
    {
        $this->validate([
            'invSupplierId' => 'required|exists:suppliers,id',
            'invNumber' => 'required|string',
            'invDate' => 'required|date',
            'invDueDate' => 'required|date',
            'invItems.*.product_id' => 'required|exists:products,id',
            'invItems.*.quantity' => 'required|numeric|min:0.01',
            'invItems.*.unit_price' => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        $tax = 0;
        $itemsData = [];

        foreach ($this->invItems as $item) {
            $qty = floatval($item['quantity']);
            $price = floatval($item['unit_price']);
            $totalItem = $price * $qty;
            $taxItem = $totalItem * 0.18; // standard 18% GST estimate

            $subtotal += $totalItem;
            $tax += $taxItem;

            $itemsData[] = [
                'product_id' => $item['product_id'],
                'quantity' => $qty,
                'unit_price' => $price,
                'tax_amount' => $taxItem,
                'total_amount' => $totalItem + $taxItem,
            ];
        }

        $payload = [
            'supplier_id' => $this->invSupplierId,
            'goods_receipt_note_id' => $this->invGrnId ?: null,
            'invoice_number' => $this->invNumber,
            'invoice_date' => $this->invDate,
            'due_date' => $this->invDueDate,
            'status' => 'unpaid',
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $subtotal + $tax,
            'items' => $itemsData,
        ];

        $purchaseService->createInvoice($payload);
        session()->flash('message', 'Purchase Invoice recorded. Supplier ledger updated.');
        $this->showingInvModal = false;
    }
}
