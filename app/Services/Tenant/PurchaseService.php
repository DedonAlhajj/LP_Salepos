<?php

namespace App\Services\Tenant;


use App\Actions\SendMailAction;
use App\Mail\CustomerDeposit;
use App\Models\Deposit;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\ProductPurchase;
use App\Models\Purchase;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseService
{

    public static function autoPurchase($product, $warehouseId, $stock)
    {
        // حساب الضرائب والتكلفة
        $costData = TaxCalculatorService::calculate($product, $stock);

        // بيانات الشراء
        $purchaseData = [
            'reference_no' => 'pr-' . date("Ymd") . '-' . date("his"),
            'user_id' => Auth::id(),
            'warehouse_id' => $warehouseId,
            'item' => 1,
            'total_qty' => $stock,
            'total_discount' => 0,
            'status' => 1,
            'payment_status' => 2,
            'total_tax' => $costData['tax'],
            'total_cost' => $costData['total_cost'],
            'order_tax' => 0,
            'grand_total' => $costData['total_cost'],
            'paid_amount' => $costData['total_cost'],
        ];

        // إنشاء سجل الشراء
        $purchase = Purchase::create($purchaseData);

        // إنشاء سجل تفاصيل الشراء
        ProductPurchase::create([
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'qty' => $stock,
            'recieved' => $stock,
            'purchase_unit_id' => $product->unit_id,
            'net_unit_cost' => $costData['net_unit_cost'],
            'discount' => 0,
            'tax_rate' => $costData['tax_rate'],
            'tax' => $costData['tax'],
            'total' => $costData['total_cost'],
        ]);

        // تحديث المخزون
        StockService::updateStock($product->id, $warehouseId, $stock);

        // إنشاء سجل الدفع
        Payment::create([
            'payment_reference' => 'ppr-' . date("Ymd") . '-' . date("his"),
            'user_id' => Auth::id(),
            'purchase_id' => $purchase->id,
            'account_id' => 0,
            'amount' => $costData['total_cost'],
            'change' => 0,
            'paying_method' => 'Cash',
        ]);
    }
}
