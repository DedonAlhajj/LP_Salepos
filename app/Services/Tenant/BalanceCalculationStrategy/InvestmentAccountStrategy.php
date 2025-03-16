<?php

namespace App\Services\Tenant\BalanceCalculationStrategy;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InvestmentAccountStrategy implements BalanceCalculationStrategy
{
    public function calculate(Collection $transfers,object $balance): array
    {
        $accountId = $balance->id;
        $investmentReturns = DB::table('investment_returns')->where('account_id', $accountId)->sum('amount');

        return [
            'credit' => $balance[$accountId]->received + $investmentReturns + $balance[$accountId]->initial_balance,
            'debit'  => $balance[$accountId]->sent + $balance[$accountId]->expense + $balance[$accountId]->payroll
        ];
    }
}


