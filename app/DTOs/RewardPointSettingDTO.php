<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class RewardPointSettingDTO
{
    public function __construct(
        public readonly bool $is_active,
        public readonly int $per_point_amount,
        public readonly int $minimum_amount,
        public readonly int $duration,
        public readonly string $type
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            is_active: $request->boolean('is_active'),
            per_point_amount: (int) $request->input('per_point_amount', 0),
            minimum_amount: (int) $request->input('minimum_amount', 0),
            duration: (int) $request->input('duration', 1),
            type: (string) $request->input('type', 'Year')
        );
    }
}
