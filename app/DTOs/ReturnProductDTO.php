<?php

namespace App\DTOs;

class ReturnProductDTO
{
    public function __construct(
        public int $product_id,
        public string $product_name,
        public string $product_code,
        public ?int $product_variant_id,
        public float $product_price,
        public int $qty,
        public float $net_unit_price,
        public float $discount,
        public float $tax,
        public float $total,
        public ?string $batch_no,
        public string $unit_name,
        public string $unit_operator,
        public string $unit_operation_value,
        public ?string $tax_name,
        public int $tax_method,
        public float $tax_rate,
        public ?string $imei_number
    ) {}
}

