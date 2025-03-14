<?php

namespace App\Services\Tenant;

use App\Models\Account;
use App\Models\Unit;

class CalculationsService
{

    public static function calculate($qty, Unit $unit)
    {
        return ($unit->operator == '*') ? $qty * $unit->operation_value : $qty / $unit->operation_value;
    }

}

