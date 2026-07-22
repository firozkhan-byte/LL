<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function searchAndFilter(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Product::with(['category', 'brand', 'manufacturer', 'images'])->whereNull('parent_id');

        // Soft deletes state filter
        if (($filters['status'] ?? null) === 'deleted') {
            $query->onlyTrashed();
        } else {
            // Apply regular status filter
            if (! empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }
        }

        // Search text (Name, SKU, Barcode, HSN)
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('hsn_code', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Brand Filter
        if (! empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        // Liquor Type Filter
        if (! empty($filters['liquor_type'])) {
            $query->where('liquor_type', $filters['liquor_type']);
        }

        // Volume Filter
        if (! empty($filters['volume_ml'])) {
            $query->where('volume_ml', $filters['volume_ml']);
        }

        // Price boundaries
        if (! empty($filters['min_price'])) {
            $query->where('selling_price', '>=', $filters['min_price']);
        }
        if (! empty($filters['max_price'])) {
            $query->where('selling_price', '<=', $filters['max_price']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function find(string $id): ?Product
    {
        return Product::with(['category', 'brand', 'manufacturer', 'images'])->find($id);
    }

    public function create(array $data): Product
    {
        $images = $data['images'] ?? [];
        if (isset($data['images'])) {
            unset($data['images']);
        }

        $product = Product::create($data);

        foreach ($images as $img) {
            $product->images()->create([
                'image_path' => $img['image_path'],
                'is_primary' => $img['is_primary'] ?? false,
            ]);
        }

        return $product;
    }

    public function update(string $id, array $data): ?Product
    {
        $product = Product::find($id);
        if (! $product) {
            return null;
        }

        $images = $data['images'] ?? null;
        if (isset($data['images'])) {
            unset($data['images']);
        }

        $product->update($data);

        if ($images !== null) {
            // Re-sync images
            $product->images()->delete();
            foreach ($images as $img) {
                $product->images()->create([
                    'image_path' => $img['image_path'],
                    'is_primary' => $img['is_primary'] ?? false,
                ]);
            }
        }

        return $product;
    }

    public function delete(string $id): bool
    {
        $product = Product::find($id);

        return $product ? $product->delete() : false;
    }

    public function restore(string $id): bool
    {
        $product = Product::onlyTrashed()->find($id);

        return $product ? $product->restore() : false;
    }

    public function getMetrics(): array
    {
        return [
            'total_products' => Product::count(),
            'brands_count' => Brand::count(),
            'categories_count' => Category::count(),
            'inactive_count' => Product::where('status', 'inactive')->count(),
        ];
    }
}
