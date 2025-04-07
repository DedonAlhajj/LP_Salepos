<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class RechargeGiftCardDTO
{
    public function __construct(
        public int $giftCardId,
        public float $amount,
        public int $rechargedBy
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            giftCardId: (int) $request->input('gift_card_id'),
            amount: (float) $request->input('amount'),
            rechargedBy: auth()->id()
        );
    }

    public function toArray(): array
    {
        return [
            'gift_card_id' => $this->giftCardId,
            'amount' => $this->amount,
            'user_id' => $this->rechargedBy,
        ];
    }
}
