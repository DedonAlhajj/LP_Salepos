<?php

namespace App\Services\Tenant\BalanceCalculationStrategy;

use Illuminate\Support\Collection;

interface BalanceCalculationStrategy
{
    public function calculate(Collection $transfers, object $balance): array;

}
