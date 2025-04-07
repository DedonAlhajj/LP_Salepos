<?php

namespace App\DTOs;

class CouponUpdateDTO
{
    public function __construct(
        public int $coupon_id,
        public string $code,
        public string $type,
        public ?float $minimum_amount,
        public float $amount,
        public int $quantity,
        public string $expired_date,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            coupon_id:$request->input('coupon_id'),
            code: $request->input('code'),
            type: $request->input('type'),
            minimum_amount: $request->input('type') == 'percentage' ? 0 : $request->input('minimum_amount'),
            amount: (float) $request->input('amount'),
            quantity: $request->input('quantity'),
            expired_date: $request->input('expired_date'),
        );

    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'type' => $this->type,
            'minimum_amount' => $this->minimum_amount,
            'amount' => $this->amount,
            'quantity' => $this->quantity,
            'expired_date' => $this->expired_date,
        ];
    }

}
