<?php

namespace App\DTOs;


use App\Models\GeneralSetting;
use Illuminate\Database\Eloquent\Collection;

class GeneralSettingDTO
{
    public function __construct(
        public ?GeneralSetting $generalSetting,
        public array $accounts,
        public array $currencies, // ✅ يجب أن يكون Collection وليس Array
        public array $timezones
    ) {}

    public function toArray(): array
    {
        return [
            'generalSetting' => $this->generalSetting,
            'accounts' => $this->accounts,
            'currencies' => $this->currencies, // ✅ لا تحوله إلى array هنا
            'timezones' => $this->timezones
        ];
    }
}

