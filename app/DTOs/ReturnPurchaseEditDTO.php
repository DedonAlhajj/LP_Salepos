<?php

namespace App\DTOs;


use App\Models\ReturnPurchase;

class ReturnPurchaseEditDTO
{
    public function __construct(
        public int $id,
        public int $warehouse_id,
        public int $supplier_id,
        public int $account_id,
        public array $products,
        public float $total_qty,
        public float $total_discount,
        public float $total_tax,
        public float $total_cost,
        public float $order_tax,
        public float $grand_total,
        public string|null $return_note,
        public string|null $staff_note,
        public string|null $document
    ) {}

    public static function fromRequest($request, int $id): self
    {
        return new self(
            id: $id,
            warehouse_id: $request->warehouse_id,
            supplier_id: $request->supplier_id,
            account_id: $request->account_id,
            products: self::formatProducts($request),
            total_qty: $request->total_qty,
            total_discount: $request->total_discount,
            total_tax: $request->total_tax,
            total_cost: $request->total_cost,
            order_tax: $request->order_tax,
            grand_total: $request->grand_total,
            return_note: $request->return_note,
            staff_note: $request->staff_note,
            document: $request->file('document')
        );
    }

    private static function formatProducts($request): array
    {
        $products = [];
        foreach ($request->product_id as $key => $product_id) {
            $products[] = [
                'product_id' => $product_id,
                'variant_id' => $request->product_variant_id[$key] ?? null,
                'batch_id' => $request->product_batch_id[$key] ?? null,
                'product_code' => $request->product_code[$key] ?? null,
                'qty' => (float) $request->qty[$key],
                'purchase_unit' => $request->purchase_unit[$key],
                'net_unit_cost' => (float) $request->net_unit_cost[$key],
                'discount' => (float) $request->discount[$key],
                'tax_rate' => (float) $request->tax_rate[$key],
                'tax' => (float) $request->tax[$key],
                'subtotal' => (float) $request->subtotal[$key],
                'imei_number' => $request->imei_number[$key] ?? null,
            ];
        }
        return $products;
    }
}
