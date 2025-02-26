<?php

namespace App\Services\Tenant;


use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class ProductVariantService
{

    public function getProductVariant($productId, $variantId)
    {
        return ProductVariant::FindExactProduct($productId, $variantId)->first();
    }

    public function getProductVariants(array $variantIds): Collection
    {
        return ProductVariant::whereIn('id', $variantIds)
            ->select('id', 'product_id', 'item_code')
            ->get()
            ->keyBy('id');
    }

}

