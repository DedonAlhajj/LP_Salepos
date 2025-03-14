<?php

namespace App\DTOs;

use App\Models\Product;
use App\Models\Unit;

class PurchaseReturnDTO
{
    public function __construct(
        public array $data,
    )
    {
    }

    public static function fromRequest($request): self
    {
        return new self([

            //actual_qty
            //is_return
            //product_variant_id
            'reference_no' => 'prr-' . now()->format('Ymd-His'),
            'user_id' => auth()->id(),
            'purchase_id' => $request->input('purchase_id'),
            'product_id' => $request->input('is_return', []),
            'product_batch_id' => $request->input('product_batch_id', []),
            'product_code' => $request->input('product_code', []),
            'qty' => $request->input('qty', []),
            'purchase_unit' => $request->input('purchase_unit', []),
            'net_unit_cost' => $request->input('net_unit_cost', []),
            'discount' => $request->input('discount', []),
            'tax_rate' => $request->input('tax_rate', []),
            'tax' => $request->input('tax', []),
            'subtotal' => $request->input('subtotal', []),
            'imei_number' => $request->input('imei_number', []),
            'total_qty' => $request->input('total_qty'),
            'total_discount' => $request->input('total_discount'),
            'total_tax' => $request->input('total_tax'),
            'total_cost' => $request->input('total_cost'),
            'item' => $request->input('item'),
            'order_tax' => $request->input('order_tax'),
            'grand_total' => $request->input('grand_total'),
            'account_id' => $request->input('account_id'),
            'order_tax_rate' => $request->input('order_tax_rate'),
            'return_note' => $request->input('return_note'),
            'staff_note' => $request->input('staff_note'),
            'document' => $request->file('document'),
        ]);
    }
}
