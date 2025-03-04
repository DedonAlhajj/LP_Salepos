<?php

namespace App\Repositories\Tenant;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository
{
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
