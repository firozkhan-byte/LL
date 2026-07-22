<?php

namespace App\Repositories\Contracts;

use App\Models\Supplier;
use Illuminate\Pagination\LengthAwarePaginator;

interface SupplierRepositoryInterface
{
    public function searchAndFilter(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function find(string $id): ?Supplier;

    public function create(array $data): Supplier;

    public function update(string $id, array $data): ?Supplier;

    public function delete(string $id): bool;

    public function restore(string $id): bool;

    public function getMetrics(): array;
}
