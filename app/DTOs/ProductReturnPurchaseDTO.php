<?php

namespace App\DTOs;
use App\Models\Product;
use App\Models\Product_Warehouse;
use App\Models\ProductVariant;
use App\Models\Warehouse;

class ProductReturnPurchaseDTO
{
    public function __construct(
        public string $code,
        public string $name,
        public string $type,
        public float $qty,
        public ?bool $is_batch,
        public ?string $variant_code = null
    ) {}

    public static function fromModel(Product $product, ?Warehouse $warehouse, ?ProductVariant $variant = null): self
    {
        return new self(
            code: $variant?->item_code ?? $product->code,
            name: $product->name,
            type: $product->type,
            qty: $warehouse->qty ?? 0,
            is_batch: $product->is_batch,
            variant_code: $variant?->item_code
        );
    }
}
