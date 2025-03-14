<?php

namespace App\DTOs;
use App\Models\Product;
use App\Models\Tax;
use App\Models\Unit;

class ProductSearchReturnPurchaseDTO
{
    public function __construct(
        public string $name,
        public string $code,
        public float $cost,
        public ?float $tax_rate,
        public ?string $tax_name,
        public int $tax_method,
        public string $unit_names,
        public string $unit_operators,
        public string $unit_operation_values,
        public int $product_id,
        public ?int $product_variant_id,
        public bool $is_imei
    ) {}

    public static function fromModel($product, ?Tax $tax, array $units, ?int $variant_id = null): array
    {
        return [
            $product->name,
            $product->code,
            $product->cost,
            $tax?->rate ?? 0,
            $tax?->name ?? 'No Tax',
            $product->tax_method,
            implode(",", array_column($units, 'name')) . ',',
            implode(",", array_column($units, 'operator')) . ',',
            implode(",", array_column($units, 'operation_value')) . ',',
            $product->id,
            $variant_id,
            $product->is_imei
        ];
    }
}
