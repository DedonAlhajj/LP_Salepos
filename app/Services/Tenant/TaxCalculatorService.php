<?php

namespace App\Services\Tenant;


use App\Actions\SendMailAction;
use App\Mail\CustomerDeposit;
use App\Models\Deposit;
use App\Models\Customer;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaxCalculatorService
{

    public static function calculate($product, $stock)
    {
        $taxRate = 0.00;
        $taxAmount = 0.00;
        $cost = $product->cost * $stock;
        $netUnitCost = $product->cost;

        if ($product->tax_id) {
            $taxData = DB::table('taxes')->select('rate')->find($product->tax_id);
            $taxRate = $taxData->rate;

            if ($product->tax_method == 1) {
                $taxAmount = $product->cost * $stock * ($taxRate / 100);
                $cost = ($product->cost * $stock) + $taxAmount;
            } else {
                $netUnitCost = (100 / (100 + $taxRate)) * $product->cost;
                $taxAmount = ($product->cost - $netUnitCost) * $stock;
                $cost = $product->cost * $stock;
            }
        }

        // إرجاع القيم بعد تنسيقها
        return [
            'net_unit_cost' => number_format($netUnitCost, 2, '.', ''),
            'tax_rate' => $taxRate,
            'tax' => number_format($taxAmount, 2, '.', ''),
            'total_cost' => number_format($cost, 2, '.', ''),
        ];
    }

}
