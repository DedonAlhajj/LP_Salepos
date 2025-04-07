<?php

namespace App\DTOs;

use Illuminate\Support\Facades\Auth;

class CouponDTO
{
    public function __construct(
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
            code: $request->input('code'),
            type: $request->input('type'),
            minimum_amount: $request->input('minimum_amount') ? $request->input('minimum_amount') : null,
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
            'used' => 0,
            'user_id' => Auth::id(),
        ];
    }

}
