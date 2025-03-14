<?php

namespace App\DTOs;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductVariant;
use App\Models\PurchaseProductReturn;
use App\Models\Tax;
use App\Models\Unit;

class ReturnProductPurchaseDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $code,
        public ?string $batch_no,
        public int $qty,
        public float $net_unit_cost,
        public float $discount,
        public float $tax,
        public float $total,
        public ?int $variant_id,
        public float $product_cost,
        public string $unit_name,
        public string $unit_operator,
        public string $unit_operation_value,
        public ?string $tax_name,
        public int $tax_method,
        public ?string $imei_number
    ) {}

    public static function fromModel(
        PurchaseProductReturn $productReturn,
        $products,
        $variants,
        $taxes,
        $units,
        $batches
    ): self {
        $product = $products[$productReturn->product_id] ?? new Product();
        $variant = $productReturn->variant_id ? ($variants[$productReturn->variant_id] ?? null) : null;
        $tax = $taxes[$productReturn->tax_rate] ?? null;
        $unit= $units[$products[$productReturn->product_id]->unit_id] ?? null;
        $batch = $batches[$productReturn->product_batch_id] ?? null;

        return new self(
            id: $product->id,
            name: $product->name,
            code: $variant?->item_code ?? $product->code,
            batch_no: $batch?->batch_no,
            qty: $productReturn->qty,
            net_unit_cost: $productReturn->net_unit_cost,
            discount: $productReturn->discount,
            tax: $productReturn->tax,
            total: $productReturn->total,
            variant_id: $variant?->id,
            product_cost: self::calculateProductCost($productReturn, $product),
            unit_name: $unit?->unit_name ?? 'N/A',
            unit_operator: $unit?->operator ?? 'N/A',
            unit_operation_value: $unit?->operation_value ?? 'N/A',
            tax_name: $tax?->name ?? 'No Tax',
            tax_method: $product->tax_method,
            imei_number: $productReturn->imei_number
        );
    }

    private static function calculateProductCost(PurchaseProductReturn $productReturn, Product $product): float
    {
        return $product->tax_method == 1
            ? $productReturn->net_unit_cost + ($productReturn->discount / max($productReturn->qty, 1))
            : ($productReturn->total / max($productReturn->qty, 1)) + ($productReturn->discount / max($productReturn->qty, 1));
    }
}

