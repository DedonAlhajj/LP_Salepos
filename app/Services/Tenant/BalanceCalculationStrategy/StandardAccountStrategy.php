<?php

namespace App\Services\Tenant\BalanceCalculationStrategy;

use Illuminate\Support\Collection;

class StandardAccountStrategy implements BalanceCalculationStrategy
{
    public function calculate(Collection $transfers, object $balance): array
    {
        $accountId = $balance->id;
        $sentMoney = $transfers->where('from_account_id', $accountId)->sum('total');
        $receivedMoney = $transfers->where('to_account_id', $accountId)->sum('total');

        return [
            'credit' => $balance->received + $balance->return_purchase + $receivedMoney + $balance->initial_balance,
            'debit'  => $balance->sent + $balance->return_sale + $balance->expense + $balance->payroll + $sentMoney,
        ];
    }
}

