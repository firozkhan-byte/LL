<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    protected ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getProducts(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->productRepository->searchAndFilter($filters, $perPage);
    }

    public function getProduct(string $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    public function createProduct(array $data): Product
    {
        return $this->productRepository->create($data);
    }

    public function updateProduct(string $id, array $data): ?Product
    {
        return $this->productRepository->update($id, $data);
    }

    public function deleteProduct(string $id): bool
    {
        return $this->productRepository->delete($id);
    }

    public function restoreProduct(string $id): bool
    {
        return $this->productRepository->restore($id);
    }

    public function getStats(): array
    {
        return $this->productRepository->getMetrics();
    }
}
