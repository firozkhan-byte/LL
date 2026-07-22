<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\BusinessUnit;
use App\Models\Company;
use App\Models\CostCenter;
use App\Models\Department;
use App\Models\Store;
use App\Models\Warehouse;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Support\Collection;

class CompanyService
{
    protected CompanyRepositoryInterface $companyRepository;

    public function __construct(CompanyRepositoryInterface $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function getCompanies(): Collection
    {
        return $this->companyRepository->allCompanies();
    }

    public function findCompany(string $id): ?Company
    {
        return $this->companyRepository->findCompany($id);
    }

    public function createCompany(array $data): Company
    {
        return $this->companyRepository->createCompany($data);
    }

    public function updateCompany(string $id, array $data): ?Company
    {
        return $this->companyRepository->updateCompany($id, $data);
    }

    public function getTreeHierarchy(): Collection
    {
        return $this->companyRepository->getTreeStructure();
    }

    // Branch management
    public function createBranch(array $data): Branch
    {
        return Branch::create($data);
    }

    public function updateBranch(string $id, array $data): ?Branch
    {
        $branch = Branch::find($id);
        if ($branch) {
            $branch->update($data);

            return $branch;
        }

        return null;
    }

    // Store mapping
    public function createStore(array $data): Store
    {
        return Store::create($data);
    }

    public function updateStore(string $id, array $data): ?Store
    {
        $store = Store::find($id);
        if ($store) {
            $store->update($data);

            return $store;
        }

        return null;
    }

    // Warehouse mapping
    public function createWarehouse(array $data): Warehouse
    {
        return Warehouse::create($data);
    }

    public function updateWarehouse(string $id, array $data): ?Warehouse
    {
        $warehouse = Warehouse::find($id);
        if ($warehouse) {
            $warehouse->update($data);

            return $warehouse;
        }

        return null;
    }

    // Other settings and metadata CRUD helpers...
    public function getDepartments(string $companyId): Collection
    {
        return Department::where('company_id', $companyId)->get();
    }

    public function createDepartment(array $data): Department
    {
        return Department::create($data);
    }

    public function getBusinessUnits(string $companyId): Collection
    {
        return BusinessUnit::where('company_id', $companyId)->get();
    }

    public function createBusinessUnit(array $data): BusinessUnit
    {
        return BusinessUnit::create($data);
    }

    public function getCostCenters(string $companyId): Collection
    {
        return CostCenter::where('company_id', $companyId)->get();
    }

    public function createCostCenter(array $data): CostCenter
    {
        return CostCenter::create($data);
    }
}
