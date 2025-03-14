<?php

namespace App\Repositories\Tenant;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository
{


    public function findByCode(string $code): ?Product
    {
         $product = Product::where('code', $code)
            ->orWhereHas('variants', fn($query) => $query->where('item_code', $code))
            ->first();
        if (!$product) {
            throw new \Exception("Product with code $code not found.");
        }
        $product->load('variants', 'tax');

        return $product;

    }


    public function getProductsInWarehouse(int $warehouseId): array
    {
        return Product::with([
            'warehouses' => function ($query) use ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            },
            'variants',
        ])
            ->get()
            ->map(function ($product) {
                $warehouse = $product->warehouses->first();
                $variant = $product->variants->first();

                return [
                    'product' => $product,
                    'warehouse' => $warehouse,
                    'variant' => $variant
                ];
            })
            ->toArray();
    }

    public function findByCodeOrVariant(string $productCode)
    {
        return Product::query()
            ->select([
                'products.id', 'products.name', 'products.code', 'products.cost',
                'products.tax_id', 'products.tax_method', 'products.unit_id',
                'products.purchase_unit_id', 'products.is_batch', 'products.is_imei',
                'product_variants.id as product_variant_id', 'product_variants.item_code',
                'product_variants.additional_cost'
            ])
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->where(fn(Builder $query) =>
            $query->where('products.code', $productCode)
                ->orWhere('product_variants.item_code', $productCode)
            )
            ->with('tax:id,rate,name', 'unit:id,unit_name,operator,operation_value')
            ->first();
    }
}
