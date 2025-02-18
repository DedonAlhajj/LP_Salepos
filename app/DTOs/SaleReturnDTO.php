<?php

namespace App\DTOs;

use App\Models\Returns;

class SaleReturnDTO
{
    public function __construct(
        public int $id,
        public string $date,
        public string $reference_no,
        public string $warehouse,
        public string $customer,
        public string $qty,
        public string $unit_price,
        public string $sub_total
    ) {}

    public static function fromModel(Returns $returnSale): self
    {
        return new self(
            id: $returnSale->id,
            date: $returnSale->created_at->format(config('date_format')),
            reference_no: $returnSale->reference_no,
            warehouse: $returnSale->warehouse->name,
            customer: "{$returnSale->customer->name} [{$returnSale->customer->phone_number}]",
            qty: number_format($returnSale->qty, config('decimal')) . ' ' . ($returnSale->unit_code ?? ''),
            unit_price: number_format(($returnSale->total / max($returnSale->qty, 1)), config('decimal')),
            sub_total: number_format($returnSale->total, config('decimal'))
        );
    }
}

