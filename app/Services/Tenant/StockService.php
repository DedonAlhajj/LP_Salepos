<?php

namespace App\Services\Tenant;


use App\Actions\SendMailAction;
use App\Mail\CustomerDeposit;
use App\Models\Deposit;
use App\Models\Customer;
use App\Models\Product_Warehouse;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService
{

    public static function updateStock($productId, $warehouseId, $stock)
    {
        $productWarehouse = Product_Warehouse::firstOrNew([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
        ]);

        $productWarehouse->qty += $stock;
        $productWarehouse->save();
    }
}
