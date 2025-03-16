<?php

namespace App\Services\Tenant\BalanceCalculationStrategy;

class BalanceSheetStrategyFactory
{
    public static function getStrategy(?string $accountType = null): BalanceCalculationStrategy
    {
        return match ($accountType ?? 'default') {
            'investment' => new InvestmentAccountStrategy(),
            'default'    => new StandardAccountStrategy(),
        };
    }
}
