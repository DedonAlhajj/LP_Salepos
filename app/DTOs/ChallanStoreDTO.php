<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class ChallanStoreDTO
{
    public function __construct(
        public array $packingSlipList,
        public array $amountList,
        public ?string $createdAt,
        public int $courierId
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            packingSlipList: $request->input('packing_slip_list', []),
            amountList: $request->input('amount_list', []),
            createdAt: $request->input('created_at'),
            courierId: (int) $request->input('courier_id')
        );
    }
}
