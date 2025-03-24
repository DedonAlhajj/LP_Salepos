<?php

namespace App\DTOs;

class SmsSettingDTO
{
    public function __construct(
        public readonly int|string $sms_id,
        public readonly bool $active,
        public readonly ?array $details
    ) {}

    public static function fromModel($setting): self
    {
        return new self(
            sms_id: $setting->id ?? '',
            active: (bool) $setting->active,
            details: json_decode($setting->details, true) ?? []
        );
    }
}
