<?php

namespace App\DTOs;

use Illuminate\Support\Collection;

class BalanceSheetDataDTO
{
    public function __construct(
        public Collection $accounts, // تغيير النوع إلى Collection
        public array $debit,
        public array $credit
    ) {}

    public function toArray(): array
    {
        return [
            'lims_account_list' => $this->accounts, // بدون تحويل إلى مصفوفة
            'debit' => $this->debit,
            'credit' => $this->credit,
        ];
    }
}
