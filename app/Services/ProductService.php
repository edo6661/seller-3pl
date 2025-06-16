<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function getUserProducts(int $userId): Collection
    {
        return Product::where('user_id', $userId)
            ->orderBy('name')
            ->get();
    }

    public function getActiveProducts(int $userId): Collection
    {
        return Product::where('user_id', $userId)
            ->active()
            ->orderBy('name')
            ->get();
    }

    public function getInactiveProducts(int $userId): Collection
    {
        return Product::where('user_id', $userId)
            ->inactive()
            ->orderBy('name')
            ->get();
    }

    public function getProductById(int $id): ?Product
    {
        return Product::with('user')->find($id);
    }

    public function createProduct(array $data): Product
    {
        $data['is_active'] = $data['is_active'] ?? true;
        return Product::create($data);
    }

    public function updateProduct(int $id, array $data): Product
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

    public function deleteProduct(int $id): bool
    {
        $product = Product::find($id);
        if ($product) {
            // Check if product is used in pickup requests
            if ($product->pickupRequestItems()->exists()) {
                // Don't delete, just deactivate
                $product->update(['is_active' => false]);
                return true;
            }
            return $product->delete();
        }
        return false;
    }

    public function toggleProductStatus(int $id): Product
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);
        return $product;
    }

    public function searchProducts(string $search, int $userId): Collection
    {
        return Product::where('user_id', $userId)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get();
    }

    public function getProductStats(int $userId): array
    {
        $total = Product::where('user_id', $userId)->count();
        $active = Product::where('user_id', $userId)->active()->count();
        $inactive = $total - $active;

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive
        ];
    }

}
