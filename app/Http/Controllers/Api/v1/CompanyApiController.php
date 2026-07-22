<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Store;
use App\Models\Warehouse;
use App\Services\CompanyService;
use Illuminate\Support\Facades\Gate;

class CompanyApiController extends Controller
{
    protected CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * List all companies.
     */
    public function index()
    {
        Gate::authorize('manage-company');

        $companies = $this->companyService->getCompanies();

        return response()->json([
            'data' => $companies,
        ]);
    }

    /**
     * Get the full tree structure for a company.
     */
    public function tree(string $id)
    {
        Gate::authorize('manage-company');

        $company = Company::with([
            'regionalOffices.branches.stores',
            'regionalOffices.branches.warehouses',
        ])->find($id);

        if (! $company) {
            return response()->json(['message' => 'Company not found.'], 404);
        }

        return response()->json([
            'data' => $company,
        ]);
    }

    /**
     * Get all branches for a company.
     */
    public function branches(string $id)
    {
        Gate::authorize('manage-company');

        $branches = Branch::where('company_id', $id)->get();

        return response()->json([
            'data' => $branches,
        ]);
    }

    /**
     * Get all stores mapped under a company's branches.
     */
    public function stores(string $id)
    {
        Gate::authorize('manage-company');

        $stores = Store::whereHas('branch', function ($q) use ($id) {
            $q->where('company_id', $id);
        })->get();

        return response()->json([
            'data' => $stores,
        ]);
    }

    /**
     * Get all warehouses mapped under a company's branches.
     */
    public function warehouses(string $id)
    {
        Gate::authorize('manage-company');

        $warehouses = Warehouse::whereHas('branch', function ($q) use ($id) {
            $q->where('company_id', $id);
        })->get();

        return response()->json([
            'data' => $warehouses,
        ]);
    }
}
