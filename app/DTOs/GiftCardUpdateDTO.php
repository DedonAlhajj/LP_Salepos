<?php

namespace App\DTOs;

class GiftCardUpdateDTO
{
    public function __construct(
        public int $id,
        public string $cardNo,
        public float $amount,
        public ?int $userId,
        public ?int $customerId,
        public string $expiredDate
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            id: (int) $request->input('gift_card_id'),
            cardNo: $request->input('card_no_edit'),
            amount: (float) $request->input('amount_edit'),
            userId: $request->input('user_edit') ? (int) $request->input('user_id_edit') : null,
            customerId: $request->input('user_edit') ? null : (int) $request->input('customer_id_edit'),
            expiredDate: $request->input('expired_date_edit')
        );
    }
}
