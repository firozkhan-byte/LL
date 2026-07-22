<?php

namespace App\Repositories\Eloquent;

use App\Models\Company;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Support\Collection;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function allCompanies(): Collection
    {
        return Company::with('settings')->get();
    }

    public function findCompany(string $id): ?Company
    {
        return Company::with('settings')->find($id);
    }

    public function createCompany(array $data): Company
    {
        $settingsData = $data['settings_data'] ?? [];
        unset($data['settings_data']);

        $company = Company::create($data);

        $company->settings()->create($settingsData);

        return $company;
    }

    public function updateCompany(string $id, array $data): ?Company
    {
        $company = Company::find($id);
        if (! $company) {
            return null;
        }

        $settingsData = $data['settings_data'] ?? null;
        if (isset($data['settings_data'])) {
            unset($data['settings_data']);
        }

        $company->update($data);

        if ($settingsData !== null) {
            $company->settings()->updateOrCreate([], $settingsData);
        }

        return $company;
    }

    public function getTreeStructure(): Collection
    {
        return Company::with([
            'regionalOffices.branches.stores',
            'regionalOffices.branches.warehouses',
        ])->get();
    }
}
