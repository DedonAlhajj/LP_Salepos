<?php

namespace App\DTOs;

use App\Models\Product;
use App\Models\Tax;
use App\Models\Unit;

class PurchaseProductDTO
{
    public function __construct(
        public int     $id,
        public string  $name,
        public string  $code,
        public ?int    $variant_id,
        public ?string $variant_code, // كود الفاريانت إن وجد
        public string  $batch_no,
        public float   $qty,
        public float   $net_unit_cost,
        public float   $discount,
        public float   $tax,
        public float   $tax_rate,
        public float   $subtotal,
        public int     $tax_method,
        public float   $unit_tax_value,
        public float   $product_cost,
        public ?string $imei_number,
        public string  $purchase_unit,
        public ?int    $product_batch_id,
        public ?string $tax_name // اسم الضريبة إن وجد
    ) {}

    public static function fromModel($productPurchase): self
    {
        $product = $productPurchase->product;
        $variant = $productPurchase->variant;
        $productBatch = $productPurchase->productBatch;
        $tax = Tax::where('rate', $productPurchase->tax_rate)->first();

        return new self(
            id: $product->id,
            name: $product->name,
            code: $product->code,
            variant_id: $variant?->id,
            variant_code: $variant?->item_code,
            batch_no: optional($productBatch)->batch_no ?? 'N/A',
            qty: $productPurchase->qty,
            net_unit_cost: $productPurchase->net_unit_cost,
            discount: $productPurchase->discount,
            tax: $productPurchase->tax,
            tax_rate: $productPurchase->tax_rate,
            subtotal: $productPurchase->total,
            tax_method: $product->tax_method,
            unit_tax_value: $productPurchase->tax / max($productPurchase->qty, 1), // تجنب القسمة على الصفر
            product_cost: ($product->tax_method == 1)
                ? ($productPurchase->net_unit_cost + ($productPurchase->discount / max($productPurchase->qty, 1)))
                : (($productPurchase->total / max($productPurchase->qty, 1)) + ($productPurchase->discount / max($productPurchase->qty, 1))),
            imei_number: $productPurchase->imei_number,
            purchase_unit: $product->type === 'standard'
                ? optional($product->unit)->unit_name ?? 'N/A'
                : 'N/A',
            product_batch_id: $productBatch?->id,
            tax_name: $tax ? $tax->name : 'No Tax'
        );
    }
}
