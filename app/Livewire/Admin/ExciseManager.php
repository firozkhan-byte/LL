<?php

namespace App\Livewire\Admin;

use App\Models\ExciseLicense;
use App\Models\ExcisePermit;
use App\Models\ExciseRegister;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\ExciseService;
use Livewire\Component;
use Livewire\WithPagination;

class ExciseManager extends Component
{
    use WithPagination;

    public string $activeTab = 'dashboard';

    // GST report range
    public string $startDate = '';

    public string $endDate = '';

    // Register filters
    public ?string $filterProductId = null;

    public string $filterDate = '';

    // Add Permit Form
    public bool $showingPermitModal = false;

    public string $permitNumber = '';

    public ?string $permitLicenseId = null;

    public ?string $permitSupplierId = null;

    public string $permitIssueDate = '';

    public string $permitExpiryDate = '';

    // Generate register Form
    public bool $showingRegisterModal = false;

    public ?string $regProductId = null;

    public ?string $regLicenseId = null;

    public string $regDate = '';

    protected $queryString = [
        'activeTab' => ['except' => 'dashboard'],
    ];

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->filterDate = now()->format('Y-m-d');
        $this->regDate = now()->format('Y-m-d');
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // --- Permit Operations ---
    public function openPermitModal(): void
    {
        $this->permitNumber = '';
        $this->permitLicenseId = ExciseLicense::first()?->id;
        $this->permitSupplierId = Supplier::first()?->id;
        $this->permitIssueDate = now()->format('Y-m-d');
        $this->permitExpiryDate = now()->addDays(30)->format('Y-m-d');
        $this->showingPermitModal = true;
    }

    public function savePermit(): void
    {
        $this->validate([
            'permitNumber' => 'required|string|unique:excise_permits,permit_number',
            'permitLicenseId' => 'required|exists:excise_licenses,id',
            'permitSupplierId' => 'required|exists:suppliers,id',
            'permitIssueDate' => 'required|date',
            'permitExpiryDate' => 'required|date|after_or_equal:permitIssueDate',
        ]);

        ExcisePermit::create([
            'permit_number' => $this->permitNumber,
            'excise_license_id' => $this->permitLicenseId,
            'supplier_id' => $this->permitSupplierId,
            'issue_date' => $this->permitIssueDate,
            'expiry_date' => $this->permitExpiryDate,
            'status' => 'pending',
        ]);

        session()->flash('permit_success', 'Import/Transport permit logged.');
        $this->showingPermitModal = false;
    }

    public function utilizePermit(string $permitId, ExciseService $exciseService): void
    {
        $exciseService->utilizePermit($permitId);
        session()->flash('permit_success', 'Transport permit marked as utilized.');
    }

    // --- License Renewal ---
    public function renewLicense(string $licenseId, ExciseService $exciseService): void
    {
        // Add 1 year to expiry date
        $license = ExciseLicense::find($licenseId);
        if ($license) {
            $newExpiry = $license->expiry_date->addYear()->format('Y-m-d');
            $exciseService->renewLicense($licenseId, $newExpiry);
            session()->flash('license_success', 'Excise License fee paid. Validity renewed for 1 Year.');
        }
    }

    // --- Daily Register Generation ---
    public function openRegisterModal(): void
    {
        $this->regProductId = Product::first()?->id;
        $this->regLicenseId = ExciseLicense::first()?->id;
        $this->regDate = now()->format('Y-m-d');
        $this->showingRegisterModal = true;
    }

    public function triggerRegisterCalculation(ExciseService $exciseService): void
    {
        $this->validate([
            'regProductId' => 'required|exists:products,id',
            'regLicenseId' => 'required|exists:excise_licenses,id',
            'regDate' => 'required|date',
        ]);

        $exciseService->generateDailyExciseRegister($this->regDate, $this->regLicenseId, $this->regProductId);

        session()->flash('reg_success', 'Daily excise register balances calculated and written to books.');
        $this->showingRegisterModal = false;
    }

    public function render(ExciseService $exciseService)
    {
        $licensesList = ExciseLicense::orderBy('expiry_date')->get();
        $productsList = Product::orderBy('name')->get();
        $suppliersList = Supplier::orderBy('name')->get();

        $gstSummary = [];
        $permitsList = [];
        $registerLines = [];

        if ($this->activeTab === 'dashboard') {
            $gstSummary = $exciseService->calculateGSTSummary($this->startDate, $this->endDate);
        } elseif ($this->activeTab === 'gst') {
            $gstSummary = $exciseService->calculateGSTSummary($this->startDate, $this->endDate);
        } elseif ($this->activeTab === 'register') {
            $registerLines = ExciseRegister::with(['license', 'product'])
                ->when($this->filterProductId, function ($q) {
                    $q->where('product_id', $this->filterProductId);
                })
                ->when($this->filterDate, function ($q) {
                    $q->whereDate('transaction_date', $this->filterDate);
                })
                ->orderBy('transaction_date', 'desc')
                ->paginate(10);
        } elseif ($this->activeTab === 'permits') {
            $permitsList = ExcisePermit::with(['license', 'supplier'])->orderBy('created_at', 'desc')->paginate(10);
        }

        return view('livewire.admin.excise-manager', [
            'licensesList' => $licensesList,
            'productsList' => $productsList,
            'suppliersList' => $suppliersList,
            'gstSummary' => $gstSummary,
            'permitsList' => $permitsList,
            'registerLines' => $registerLines,
        ])->layout('layouts.app');
    }
}
