<?php

namespace App\Livewire\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockLedger;
use App\Models\PosSaleItem;
use App\Models\SalesOrderItem;
use App\Models\PurchaseOrderItem;
use App\Services\ProductService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class ProductDetail extends Component
{
    use WithFileUploads, WithPagination;

    public string $productId;
    public string $activeTab = 'stock'; // stock, pricing, ledger, analytics, logs

    // Form modal state (for editing product directly from detail page)
    public bool $showingProductModal = false;
    public ?string $categoryId = null;
    public ?string $brandId = null;
    public ?string $manufacturerId = null;
    public string $name = '';
    public string $sku = '';
    public ?string $barcode = '';
    public ?string $hsnCode = '';
    public float $gstRate = 18.00;
    public string $liquorType = 'Spirit';
    public int $volumeMl = 750;
    public float $alcoholPercentage = 42.80;
    public float $mrp = 0.00;
    public float $purchasePrice = 0.00;
    public float $sellingPrice = 0.00;
    public ?string $originCountry = '';
    public ?string $originRegion = '';
    public bool $expiryTracking = false;
    public bool $batchTracking = false;
    public bool $serialTracking = false;
    public ?string $description = '';
    public string $status = 'active';
    public $productImages = [];

    // Add Variant form state
    public bool $showingVariantModal = false;
    public int $varVolumeMl = 750;
    public string $varSku = '';
    public ?string $varBarcode = '';
    public float $varMrp = 0.00;
    public float $varPurchasePrice = 0.00;
    public float $varSellingPrice = 0.00;
    public string $varStatus = 'active';
    public bool $varExpiryTracking = false;
    public bool $varBatchTracking = false;
    public bool $varSerialTracking = false;
    public float $varAlcoholPercentage = 42.80;

    protected $queryString = [
        'activeTab' => ['except' => 'stock'],
    ];

    public function mount(string $id): void
    {
        abort_if(Gate::denies('manage-company'), 403);
        $this->productId = $id;
        
        // Confirm the product exists
        Product::withTrashed()->findOrFail($id);
    }

    public function getProductProperty(): Product
    {
        return Product::withTrashed()->with(['category', 'brand', 'manufacturer', 'images', 'variants'])->findOrFail($this->productId);
    }

    public function getWarehouseStocksProperty(): array
    {
        $warehouses = Warehouse::all();
        $stocks = [];
        
        foreach ($warehouses as $wh) {
            $latest = StockLedger::where('product_id', $this->productId)
                ->where('warehouse_id', $wh->id)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();
                
            $stocks[] = [
                'warehouse' => $wh,
                'available_qty' => $latest ? $latest->balance_after : 0.00,
                'batch_number' => $latest ? $latest->batch_number : null,
                'expiry_date' => $latest && $latest->expiry_date ? $latest->expiry_date->format('Y-m-d') : null,
            ];
        }
        
        return $stocks;
    }

    public function getFinancialsProperty(): array
    {
        $product = $this->product;
        $profit = $product->selling_price - $product->purchase_price;
        
        // Tax calculations (GST included in Selling Price)
        $gstAmount = $product->selling_price * ($product->gst_rate / (100 + $product->gst_rate));
        $netProfit = $profit - $gstAmount;
        
        $marginPercent = $product->selling_price > 0 ? ($profit / $product->selling_price) * 100 : 0;
        $markupPercent = $product->purchase_price > 0 ? ($profit / $product->purchase_price) * 100 : 0;
        
        return [
            'profit' => $profit,
            'net_profit' => $netProfit,
            'gst_amount' => $gstAmount,
            'margin_percent' => $marginPercent,
            'markup_percent' => $markupPercent,
        ];
    }

    public function getAnalyticsProperty(): array
    {
        $posQty = PosSaleItem::where('product_id', $this->productId)->sum('quantity');
        $posRevenue = PosSaleItem::where('product_id', $this->productId)->sum('total_price');
        
        $wholesaleQty = SalesOrderItem::where('product_id', $this->productId)->sum('quantity');
        $wholesaleRevenue = SalesOrderItem::where('product_id', $this->productId)->sum('total_price');
        
        $purchaseQty = PurchaseOrderItem::where('product_id', $this->productId)->sum('quantity');
        $purchaseCost = PurchaseOrderItem::where('product_id', $this->productId)->sum('total_amount');
        
        // 6 months sales trend
        $salesTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M Y');
            $monthStart = $date->startOfMonth()->toDateTimeString();
            $monthEnd = $date->endOfMonth()->toDateTimeString();
            
            $posSalesVal = PosSaleItem::where('product_id', $this->productId)
                ->whereHas('sale', function ($query) use ($monthStart, $monthEnd) {
                    $query->whereBetween('created_at', [$monthStart, $monthEnd]);
                })->sum('total_price');
                
            $orderSalesVal = SalesOrderItem::where('product_id', $this->productId)
                ->whereHas('order', function ($query) use ($monthStart, $monthEnd) {
                    $query->whereBetween('created_at', [$monthStart, $monthEnd]);
                })->sum('total_price');
                
            $salesTrend[] = [
                'month' => $monthName,
                'amount' => $posSalesVal + $orderSalesVal,
            ];
        }
        
        return [
            'total_sales_qty' => $posQty + $wholesaleQty,
            'total_sales_revenue' => $posRevenue + $wholesaleRevenue,
            'total_purchased_qty' => $purchaseQty,
            'total_purchased_cost' => $purchaseCost,
            'sales_trend' => $salesTrend,
        ];
    }

    public function getAuditLogsProperty(): array
    {
        $product = Product::withTrashed()->findOrFail($this->productId);
        
        return Activity::forSubject($product)
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get()
            ->map(fn ($log) => [
                'description' => $log->description,
                'event' => $log->event,
                'properties' => $log->properties ? json_encode($log->properties, JSON_PRETTY_PRINT) : null,
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                'user_name' => $log->causer ? $log->causer->name : 'System',
            ])
            ->toArray();
    }

    public function changeTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function toggleStatus(): void
    {
        $product = $this->product;
        $product->status = $product->status === 'active' ? 'inactive' : 'active';
        $product->save();
        
        session()->flash('message', 'Product status updated to ' . $product->status . '.');
    }

    public function deleteProduct(ProductService $productService): void
    {
        $productService->deleteProduct($this->productId);
        session()->flash('message', 'Product soft-deleted successfully.');
    }

    public function restoreProduct(ProductService $productService): void
    {
        $productService->restoreProduct($this->productId);
        session()->flash('message', 'Product restored successfully.');
    }

    // Modal Edit product logic
    public function openEditModal(): void
    {
        $product = $this->product;
        $this->categoryId = $product->category_id;
        $this->brandId = $product->brand_id;
        $this->manufacturerId = $product->manufacturer_id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->barcode = $product->barcode;
        $this->hsnCode = $product->hsn_code;
        $this->gstRate = $product->gst_rate;
        $this->liquorType = $product->liquor_type;
        $this->volumeMl = $product->volume_ml;
        $this->alcoholPercentage = $product->alcohol_percentage;
        $this->mrp = $product->mrp;
        $this->purchasePrice = $product->purchase_price;
        $this->sellingPrice = $product->selling_price;
        $this->originCountry = $product->origin_country;
        $this->originRegion = $product->origin_region;
        $this->expiryTracking = $product->expiry_tracking;
        $this->batchTracking = $product->batch_tracking;
        $this->serialTracking = $product->serial_tracking;
        $this->description = $product->description;
        $this->status = $product->status;
        $this->productImages = [];
        
        $this->showingProductModal = true;
    }

    public function saveProduct(ProductService $productService): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'mrp' => 'required|numeric|min:0',
            'purchasePrice' => 'required|numeric|min:0',
            'sellingPrice' => 'required|numeric|min:0',
            'volumeMl' => 'required|integer|min:0',
            'alcoholPercentage' => 'required|numeric|min:0|max:100',
            'liquorType' => 'required|string',
            'sku' => 'required|string|unique:products,sku,'.$this->productId,
        ]);

        $payload = [
            'category_id' => $this->categoryId ?: null,
            'brand_id' => $this->brandId ?: null,
            'manufacturer_id' => $this->manufacturerId ?: null,
            'name' => $this->name,
            'sku' => $this->sku,
            'barcode' => $this->barcode ?: null,
            'hsn_code' => $this->hsnCode ?: null,
            'gst_rate' => $this->gstRate,
            'liquor_type' => $this->liquorType,
            'volume_ml' => $this->volumeMl,
            'alcohol_percentage' => $this->alcoholPercentage,
            'mrp' => $this->mrp,
            'purchase_price' => $this->purchasePrice,
            'selling_price' => $this->sellingPrice,
            'origin_country' => $this->originCountry ?: null,
            'origin_region' => $this->originRegion ?: null,
            'expiry_tracking' => $this->expiryTracking,
            'batch_tracking' => $this->batchTracking,
            'serial_tracking' => $this->serialTracking,
            'description' => $this->description ?: null,
            'status' => $this->status,
        ];

        if (! empty($this->productImages)) {
            $imagesData = [];
            foreach ($this->productImages as $index => $img) {
                $path = $img->store('products', 'public');
                $imagesData[] = [
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                ];
            }
            $payload['images'] = $imagesData;
        }

        $productService->updateProduct($this->productId, $payload);
        session()->flash('message', 'Product updated successfully.');

        $this->showingProductModal = false;
    }

    public function openVariantModal(): void
    {
        $parent = $this->product;
        $this->varVolumeMl = 750;
        $this->varSku = '';
        $this->varBarcode = '';
        $this->varMrp = $parent->mrp;
        $this->varPurchasePrice = $parent->purchase_price;
        $this->varSellingPrice = $parent->selling_price;
        $this->varStatus = 'active';
        $this->varExpiryTracking = $parent->expiry_tracking;
        $this->varBatchTracking = $parent->batch_tracking;
        $this->varSerialTracking = $parent->serial_tracking;
        $this->varAlcoholPercentage = $parent->alcohol_percentage;

        $this->showingVariantModal = true;
    }

    public function saveVariant(ProductService $productService): void
    {
        $this->validate([
            'varVolumeMl' => 'required|integer|min:1',
            'varAlcoholPercentage' => 'required|numeric|min:0|max:100',
            'varMrp' => 'required|numeric|min:0',
            'varPurchasePrice' => 'required|numeric|min:0',
            'varSellingPrice' => 'required|numeric|min:0',
            'varSku' => 'nullable|string|unique:products,sku',
        ]);

        $parent = $this->product;
        $sku = $this->varSku ?: 'SKU-' . strtoupper(\Illuminate\Support\Str::random(8));

        $payload = [
            'parent_id' => $parent->id,
            'category_id' => $parent->category_id,
            'brand_id' => $parent->brand_id,
            'manufacturer_id' => $parent->manufacturer_id,
            'name' => $parent->name . ' ' . $this->varVolumeMl . 'ml',
            'sku' => $sku,
            'barcode' => $this->varBarcode ?: null,
            'gst_rate' => $parent->gst_rate,
            'liquor_type' => $parent->liquor_type,
            'volume_ml' => $this->varVolumeMl,
            'alcohol_percentage' => $this->varAlcoholPercentage,
            'mrp' => $this->varMrp,
            'purchase_price' => $this->varPurchasePrice,
            'selling_price' => $this->varSellingPrice,
            'origin_country' => $parent->origin_country,
            'origin_region' => $parent->origin_region,
            'expiry_tracking' => $this->varExpiryTracking,
            'batch_tracking' => $this->varBatchTracking,
            'serial_tracking' => $this->varSerialTracking,
            'status' => $this->varStatus,
        ];

        $productService->createProduct($payload);
        $this->showingVariantModal = false;
        session()->flash('message', 'Product variant added successfully.');
    }

    public function render()
    {
        $product = $this->product;
        
        $ledgerHistory = StockLedger::where('product_id', $this->productId)
            ->with('warehouse')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $categories = Category::all();
        $brands = Brand::all();
        $manufacturers = Manufacturer::all();

        return view('livewire.admin.product-detail', [
            'product' => $product,
            'ledgerHistory' => $ledgerHistory,
            'categories' => $categories,
            'brands' => $brands,
            'manufacturers' => $manufacturers,
        ])->layout('layouts.app');
    }
}
