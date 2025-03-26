<?php

namespace App\DTOs;

class PackingSlipStoreDTO
{
    public function __construct(
        public int $sale_id,
        public float $amount,
        public array $is_packing
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            sale_id: (int) $request->input('sale_id'),
            amount: (float) $request->input('amount'),
            is_packing: (array) $request->input('is_packing')
        );
    }
}
