<?php

namespace App\DTOs;

use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\ArrayShape;

class PayrollDTO
{
    public function __construct(
        public string  $created_at,
        public int     $employee_id,
        public int     $account_id,
        public int     $amount,
        public int     $paying_method,
        public ?string $note,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'created_at' => $this->created_at ?? now()->format('Y-m-d'),
            'reference_no' => 'payroll-' . now()->format('Ymd-His'),
            'employee_id' => $this->employee_id,
            'account_id' => $this->account_id,
            'user_id' => Auth::id(),
            'amount' => $this->amount,
            'paying_method' => $this->paying_method,
            'note'  => $this->note,
        ];
    }
}
