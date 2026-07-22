<?php

namespace App\Repositories\Contracts;

use App\Models\Company;
use Illuminate\Support\Collection;

interface CompanyRepositoryInterface
{
    public function allCompanies(): Collection;

    public function findCompany(string $id): ?Company;

    public function createCompany(array $data): Company;

    public function updateCompany(string $id, array $data): ?Company;

    public function getTreeStructure(): Collection;
}
