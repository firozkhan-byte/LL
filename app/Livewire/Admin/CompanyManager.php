<?php

namespace App\Livewire\Admin;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\RegionalOffice;
use App\Models\Store;
use App\Models\Warehouse;
use App\Services\ApprovalService;
use App\Services\CompanyService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class CompanyManager extends Component
{
    // Active tabs
    public string $activeTab = 'company'; // company, branches, structures

    // Company Settings Form State
    public ?string $companyId = null;

    public ?string $companyName = '';

    public ?string $registrationNumber = '';

    public ?string $taxNumber = '';

    public ?string $email = '';

    public ?string $phone = '';

    public ?string $website = '';

    public ?string $currency = 'INR';

    public ?string $timezone = 'Asia/Kolkata';

    public ?string $addressLine1 = '';

    public ?string $addressLine2 = '';

    public ?string $city = '';

    public ?string $state = '';

    public ?string $postalCode = '';

    public ?string $country = 'India';

    // Branch Form State
    public bool $showingBranchModal = false;

    public string $branchName = '';

    public string $branchCode = '';

    public ?string $branchRoId = null;

    // Store Form State
    public bool $showingStoreModal = false;

    public string $storeName = '';

    public string $storeCode = '';

    public string $storeLicense = '';

    public ?string $storeBranchId = null;

    // Warehouse Form State
    public bool $showingWarehouseModal = false;

    public string $warehouseName = '';

    public string $warehouseCode = '';

    public ?string $warehouseBranchId = null;

    // Structure Form State (Dept, BU, Cost Center)
    public string $structureType = 'department'; // department, business_unit, cost_center

    public string $structName = '';

    public string $structCode = '';

    public ?string $structParentId = null; // business unit id (for cost center)

    public function mount(CompanyService $companyService): void
    {
        abort_if(Gate::denies('manage-company'), 403);

        $company = $companyService->getCompanies()->first();
        if ($company) {
            $this->loadCompany($company);
        }
    }

    public function render(CompanyService $companyService)
    {
        $companies = $companyService->getCompanies();
        $selectedCompany = $this->companyId ? $companyService->findCompany($this->companyId) : null;

        $branches = Branch::with(['regionalOffice', 'stores', 'warehouses'])->get();
        $roList = RegionalOffice::all();

        $departments = $this->companyId ? $companyService->getDepartments($this->companyId) : collect();
        $businessUnits = $this->companyId ? $companyService->getBusinessUnits($this->companyId) : collect();
        $costCenters = $this->companyId ? $companyService->getCostCenters($this->companyId) : collect();

        return view('livewire.admin.company-manager', [
            'companies' => $companies,
            'selectedCompany' => $selectedCompany,
            'branches' => $branches,
            'roList' => $roList,
            'departments' => $departments,
            'businessUnits' => $businessUnits,
            'costCenters' => $costCenters,
        ])->layout('layouts.app');
    }

    public function loadCompany(Company $company): void
    {
        $this->companyId = $company->id;
        $this->companyName = $company->name;
        $this->registrationNumber = $company->registration_number;
        $this->taxNumber = $company->tax_number;
        $this->email = $company->email;
        $this->phone = $company->phone;
        $this->website = $company->website;

        if ($company->settings) {
            $this->currency = $company->settings->currency;
            $this->timezone = $company->settings->timezone;
            $this->addressLine1 = $company->settings->address_line1;
            $this->addressLine2 = $company->settings->address_line2;
            $this->city = $company->settings->city;
            $this->state = $company->settings->state;
            $this->postalCode = $company->settings->postal_code;
            $this->country = $company->settings->country;
        }
    }

    public function updateCompany(CompanyService $companyService): void
    {
        $this->validate([
            'companyName' => 'required|string|max:255',
            'email' => 'nullable|email',
            'currency' => 'required|string|size:3',
            'timezone' => 'required|string',
        ]);

        $companyService->updateCompany($this->companyId, [
            'name' => $this->companyName,
            'registration_number' => $this->registrationNumber,
            'tax_number' => $this->taxNumber,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'settings_data' => [
                'currency' => $this->currency,
                'timezone' => $this->timezone,
                'address_line1' => $this->addressLine1,
                'address_line2' => $this->addressLine2,
                'city' => $this->city,
                'state' => $this->state,
                'postal_code' => $this->postalCode,
                'country' => $this->country,
            ],
        ]);

        session()->flash('message', 'Company settings updated successfully.');
    }

    // Propose Branch Creation
    public function proposeBranch(ApprovalService $approvalService): void
    {
        $this->validate([
            'branchName' => 'required|string|max:255',
            'branchCode' => 'required|string|max:20|unique:branches,code',
            'branchRoId' => 'required|exists:regional_offices,id',
        ]);

        $approvalService->proposeChange(
            Branch::class,
            'create',
            [
                'company_id' => $this->companyId,
                'regional_office_id' => $this->branchRoId,
                'name' => $this->branchName,
                'code' => $this->branchCode,
            ],
            auth()->id()
        );

        $this->showingBranchModal = false;
        $this->resetBranchForm();
        session()->flash('message', 'Branch creation request submitted and is pending approval.');
    }

    // Propose Store Creation
    public function proposeStore(ApprovalService $approvalService): void
    {
        $this->validate([
            'storeName' => 'required|string|max:255',
            'storeCode' => 'required|string|max:20|unique:stores,code',
            'storeBranchId' => 'required|exists:branches,id',
            'storeLicense' => 'nullable|string',
        ]);

        $approvalService->proposeChange(
            Store::class,
            'create',
            [
                'branch_id' => $this->storeBranchId,
                'name' => $this->storeName,
                'code' => $this->storeCode,
                'license_number' => $this->storeLicense,
            ],
            auth()->id()
        );

        $this->showingStoreModal = false;
        $this->resetStoreForm();
        session()->flash('message', 'Store creation request submitted and is pending approval.');
    }

    // Propose Warehouse Creation
    public function proposeWarehouse(ApprovalService $approvalService): void
    {
        $this->validate([
            'warehouseName' => 'required|string|max:255',
            'warehouseCode' => 'required|string|max:20|unique:warehouses,code',
            'warehouseBranchId' => 'required|exists:branches,id',
        ]);

        $approvalService->proposeChange(
            Warehouse::class,
            'create',
            [
                'branch_id' => $this->warehouseBranchId,
                'name' => $this->warehouseName,
                'code' => $this->warehouseCode,
            ],
            auth()->id()
        );

        $this->showingWarehouseModal = false;
        $this->resetWarehouseForm();
        session()->flash('message', 'Warehouse creation request submitted and is pending approval.');
    }

    // Immediate creation for internal metadata (Dept, BU, CC)
    public function addStructure(CompanyService $companyService): void
    {
        $this->validate([
            'structName' => 'required|string|max:255',
            'structCode' => 'required|string|max:20',
        ]);

        if ($this->structureType === 'department') {
            $companyService->createDepartment([
                'company_id' => $this->companyId,
                'name' => $this->structName,
                'code' => $this->structCode,
            ]);
        } elseif ($this->structureType === 'business_unit') {
            $companyService->createBusinessUnit([
                'company_id' => $this->companyId,
                'name' => $this->structName,
                'code' => $this->structCode,
            ]);
        } elseif ($this->structureType === 'cost_center') {
            $this->validate(['structParentId' => 'required|exists:business_units,id']);
            $companyService->createCostCenter([
                'company_id' => $this->companyId,
                'business_unit_id' => $this->structParentId,
                'name' => $this->structName,
                'code' => $this->structCode,
            ]);
        }

        $this->resetStructureForm();
        session()->flash('message', 'Structure item created successfully.');
    }

    private function resetBranchForm(): void
    {
        $this->branchName = '';
        $this->branchCode = '';
        $this->branchRoId = null;
    }

    private function resetStoreForm(): void
    {
        $this->storeName = '';
        $this->storeCode = '';
        $this->storeLicense = '';
        $this->storeBranchId = null;
    }

    private function resetWarehouseForm(): void
    {
        $this->warehouseName = '';
        $this->warehouseCode = '';
        $this->warehouseBranchId = null;
    }

    private function resetStructureForm(): void
    {
        $this->structName = '';
        $this->structCode = '';
        $this->structParentId = null;
    }
}
