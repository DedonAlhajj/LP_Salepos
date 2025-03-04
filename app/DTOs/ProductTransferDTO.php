<?php

namespace App\DTOs;


use App\Models\Unit;

class ProductTransferDTO
{
    public function __construct(
        public readonly int $product_id,
        public readonly ?string $imei_number,
        public readonly int $qty,
        public readonly int $purchase_unit_id,
        public readonly float $net_unit_cost,
        public readonly float $tax_rate,
        public readonly float $tax,
        public readonly float $total
    ) {}

    public static function fromRequest(array $data, int $index): self
    {
        // الحصول على بيانات الوحدة
        $lims_purchase_unit_data = Unit::where('unit_name', $data['purchase_unit'][$index])->firstOrFail();

        return new self(
            product_id: (int) $data['product_id'][$index],
            imei_number: $data['imei_number'][$index] ?? null,
            qty: (int) $data['qty'][$index],
            purchase_unit_id: $lims_purchase_unit_data->id,
            net_unit_cost: (float) $data['net_unit_cost'][$index],
            tax_rate: (float) $data['tax_rate'][$index],
            tax: (float) $data['tax'][$index],
            total: (float) $data['subtotal'][$index]
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
