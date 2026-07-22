<?php

namespace App\Livewire\Admin;

use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ProductCatalog extends Component
{
    use WithFileUploads, WithPagination;

    // Filters
    public string $search = '';

    public ?string $selectedCategory = null;

    public ?string $selectedBrand = null;

    public ?string $selectedLiquorType = null;

    public ?int $selectedVolume = null;

    public ?float $minPrice = null;

    public ?float $maxPrice = null;

    public string $filterStatus = 'active'; // active, inactive, deleted

    // Form modal state
    public bool $showingProductModal = false;

    public ?string $productId = null;

    // Product fields
    public ?string $categoryId = null;

    public ?string $brandId = null;

    public ?string $manufacturerId = null;

    public string $name = '';

    public string $sku = '';

    public ?string $barcode = '';

    public ?string $hsnCode = '';

    public float $gstRate = 18.00;

    public string $liquorType = 'Spirit'; // Spirit, Beer, Wine, Liqueur, Cider, Brandy

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

    // File attachments state
    public $productImages = [];

    public $importFile = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => null],
        'selectedBrand' => ['except' => null],
        'selectedLiquorType' => ['except' => null],
        'filterStatus' => ['except' => 'active'],
    ];

    public function mount(): void
    {
        abort_if(Gate::denies('manage-company'), 403);
    }

    public function render(ProductService $productService)
    {
        $filters = [
            'search' => $this->search,
            'category_id' => $this->selectedCategory,
            'brand_id' => $this->selectedBrand,
            'liquor_type' => $this->selectedLiquorType,
            'volume_ml' => $this->selectedVolume,
            'min_price' => $this->minPrice,
            'max_price' => $this->maxPrice,
            'status' => $this->filterStatus,
        ];

        $products = $productService->getProducts($filters, 10);
        $metrics = $productService->getStats();

        $categories = Category::all();
        $brands = Brand::all();
        $manufacturers = Manufacturer::all();

        return view('livewire.admin.product-catalog', [
            'products' => $products,
            'metrics' => $metrics,
            'categories' => $categories,
            'brands' => $brands,
            'manufacturers' => $manufacturers,
        ])->layout('layouts.app');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetProductForm();
        $this->productId = null;
        $this->showingProductModal = true;
    }

    public function openEditModal(string $id, ProductService $productService): void
    {
        $this->resetProductForm();
        $product = $productService->getProduct($id);
        if ($product) {
            $this->productId = $product->id;
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

            $this->showingProductModal = true;
        }
    }

    public function saveProduct(ProductService $productService): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'mrp' => 'required|numeric|min:0',
            'purchasePrice' => 'required|numeric|min:0',
            'sellingPrice' => 'required|numeric|min:0',
            'volumeMl' => 'required|integer|min:0',
            'alcoholPercentage' => 'required|numeric|min:0|max:100',
            'liquorType' => 'required|string',
            'sku' => $this->productId ? 'required|string|unique:products,sku,'.$this->productId : 'nullable|string|unique:products,sku',
        ];

        $this->validate($rules);

        $payload = [
            'category_id' => $this->categoryId ?: null,
            'brand_id' => $this->brandId ?: null,
            'manufacturer_id' => $this->manufacturerId ?: null,
            'name' => $this->name,
            'sku' => $this->sku ?: null,
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

        // Process image uploads if available
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

        if ($this->productId) {
            $productService->updateProduct($this->productId, $payload);
            session()->flash('message', 'Product updated successfully.');
        } else {
            $productService->createProduct($payload);
            session()->flash('message', 'Product created successfully.');
        }

        $this->showingProductModal = false;
        $this->resetProductForm();
    }

    public function deleteProduct(string $id, ProductService $productService): void
    {
        $productService->deleteProduct($id);
        session()->flash('message', 'Product soft-deleted successfully.');
    }

    public function restoreProduct(string $id, ProductService $productService): void
    {
        $productService->restoreProduct($id);
        session()->flash('message', 'Product restored successfully.');
    }

    public function exportExcel()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }

    public function importExcel(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new ProductsImport, $this->importFile->getRealPath());

        $this->importFile = null;
        session()->flash('message', 'Excel products imported successfully.');
    }

    private function resetProductForm(): void
    {
        $this->productId = null;
        $this->categoryId = null;
        $this->brandId = null;
        $this->manufacturerId = null;
        $this->name = '';
        $this->sku = '';
        $this->barcode = '';
        $this->hsnCode = '';
        $this->gstRate = 18.00;
        $this->liquorType = 'Spirit';
        $this->volumeMl = 750;
        $this->alcoholPercentage = 42.80;
        $this->mrp = 0.00;
        $this->purchasePrice = 0.00;
        $this->sellingPrice = 0.00;
        $this->originCountry = '';
        $this->originRegion = '';
        $this->expiryTracking = false;
        $this->batchTracking = false;
        $this->serialTracking = false;
        $this->description = '';
        $this->status = 'active';
        $this->productImages = [];
    }
}
