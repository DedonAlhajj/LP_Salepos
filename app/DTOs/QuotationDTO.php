<?php

namespace App\DTOs;

use App\Models\Unit;
use Illuminate\Http\Request;

class QuotationDTO
{
    public function __construct(
        public int $biller_id,
        public int $supplier_id,
        public int $customer_id,
        public int $warehouse_id,
        public int $total_qty,
        public float $total_price,
        public float $total_discount,
        public float $order_tax,
        public float $order_tax_rate,
        public float $order_discount,
        public float $shipping_cost,
        public float $grand_total,
        public int $quotation_status,
        public ?string $note,
        public array $products = []
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            biller_id: $request->input('biller_id'),
            supplier_id: $request->input('supplier_id'),
            customer_id: $request->input('customer_id'),
            warehouse_id: $request->input('warehouse_id'),
            total_qty: (int) $request->input('total_qty'),
            total_price: (float) $request->input('total_price'),
            total_discount: (float) $request->input('total_discount'),
            order_tax: (float) $request->input('order_tax'),
            order_tax_rate: (float) $request->input('order_tax_rate'),
            order_discount: (float) $request->input('order_discount'),
            shipping_cost: (float) $request->input('shipping_cost'),
            grand_total: (float) $request->input('grand_total'),
            quotation_status: (int) $request->input('quotation_status'),
            note: $request->input('note'),
            products: array_map(fn($index) => [
                'product_id' => $request->input("product_id.$index"),
                'product_batch_id' => $request->input("product_batch_id.$index") ?? null,
                'variant_id' => $request->input("product_variant_id.$index") ?? null,  // تعديل هنا
                'product_code' => $request->input("product_code.$index"),
                'product_price' => (float) $request->input("product_price.$index"),
                'qty' => (int) $request->input("qty.$index"),
                'sale_unit_id' => Unit::where('unit_name', $request->input("sale_unit.$index"))->value('id') ?? 0,
                'net_unit_price' => (float) $request->input("net_unit_price.$index"),
                'discount' => (float) $request->input("discount.$index"),
                'tax_rate' => (float) $request->input("tax_rate.$index"),
                'tax' => (float) $request->input("tax.$index"),
                'total' => (float) $request->input("subtotal.$index"),
            ], array_keys($request->input('product_id', [])))
        );
    }


    public function toArray(): array
    {
        return [
            'biller_id' => $this->biller_id,
            'supplier_id' => $this->supplier_id,
            'customer_id' => $this->customer_id,
            'warehouse_id' => $this->warehouse_id,
            'total_qty' => $this->total_qty,
            'total_price' => $this->total_price,
            'total_discount' => $this->total_discount,
            'order_tax' => $this->order_tax,
            'order_tax_rate' => $this->order_tax_rate,
            'order_discount' => $this->order_discount,
            'shipping_cost' => $this->shipping_cost,
            'grand_total' => $this->grand_total,
            'quotation_status' => $this->quotation_status,
            'note' => $this->note,
        ];
    }

    public function prepareMailData($customer, $quotation): array
    {
        return [
            'email' => $customer->email,
            'reference_no' => $quotation->reference_no,
            'total_qty' => $this->total_qty,
            'total_price' => $this->total_price,
            'order_tax' => $this->order_tax,
            'order_tax_rate' => $this->order_tax_rate,
            'order_discount' => $this->order_discount,
            'shipping_cost' => $this->shipping_cost,
            'grand_total' => $this->grand_total,
            'products' => array_map(fn($p) => $p['product_id'], $this->products),
        ];
    }
}
