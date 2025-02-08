<?php

namespace App\Services\Tenant;

use App\Models\CashRegister;

class CashRegisterService
{

    public function getCashRegisterId(int $userId, int $warehouseId): ?int
    {
        return CashRegister::where([
            ['user_id', $userId],
            ['warehouse_id', $warehouseId],
            ['status', 1]
        ])->value('id');
    }

    public function getCashRegisterIdAndWarehouse(int $userId)
    {
        return  CashRegister::where('user_id', $userId)
            ->where('status', 1)
            ->select('id', 'warehouse_id')
            ->first();
    }

}

