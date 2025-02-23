<?php

namespace App\Services\Tenant;


use App\Models\ProductVariant;

class ProductVariantService
{

    public function getProductVariant($productId, $variantId)
    {
        return ProductVariant::FindExactProduct($productId, $variantId)->first();
    }

}

