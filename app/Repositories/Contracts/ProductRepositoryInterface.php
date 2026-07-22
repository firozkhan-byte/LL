<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function searchAndFilter(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function find(string $id): ?Product;

    public function create(array $data): Product;

    public function update(string $id, array $data): ?Product;

    public function delete(string $id): bool;

    public function restore(string $id): bool;

    public function getMetrics(): array;
}
