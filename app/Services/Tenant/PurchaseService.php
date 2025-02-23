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
use function PHPUnit\Framework\throwException;

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

    public function getPurchases($filters)
    {
        try {
            $query = Purchase::with(['supplier', 'warehouse'])
                ->forDateRange($filters['starting_date'], $filters['ending_date'])
                ->forWarehouse($filters['warehouse_id'])
                ->filterByPurchaseStatus($filters['purchase_status'])
                ->filterByPaymentStatus($filters['payment_status'])
                ->staffAccessCheck();

        $purchases = $query->orderBy('created_at', 'desc')->get();

        return $this->formatPurchases($purchases);

        } catch (\Exception $e) {
            Log::error("Error Purchase fetching modifications: " . $e->getMessage());
            throw new Exception("Purchase operation failed : " . $e->getMessage());
        }
    }

    private function formatPurchases($purchases)
    {
        return $purchases->map(function ($purchase) {
            return [
                'id'              => $purchase->id,
                'date'            => date(config('date_format'), strtotime($purchase->created_at->toDateString())),
                'reference_no'    => $purchase->reference_no,
                'supplier'        => [
                    'name'         => $purchase->supplier->name ?? 'N/A',
                    'company_name' => $purchase->supplier->company_name ?? 'N/A',
                    'email'        => $purchase->supplier->email ?? 'N/A',
                    'phone'        => $purchase->supplier->phone_number ?? 'N/A',
                    'address'      => preg_replace('/\s+/S', " ", $purchase->supplier->address ?? 'N/A'),
                    'city'         => $purchase->supplier->city ?? 'N/A',
                ],
                'warehouse'       => [
                    'name'   => $purchase->warehouse->name ?? 'N/A',
                    'phone'  => $purchase->warehouse->phone ?? 'N/A',
                    'address'=> preg_replace('/\s+/S', " ", $purchase->warehouse->address ?? 'N/A'),
                ],
                'purchase_status' => $this->formatPurchaseStatus($purchase->status),
                'payment_status'  => $this->formatPaymentStatus($purchase->payment_status),
                'total_tax'       => number_format($purchase->total_tax, config('decimal')),
                'total_discount'  => number_format($purchase->total_discount, config('decimal')),
                'total_cost'      => number_format($purchase->total_cost, config('decimal')),
                'order_tax'       => number_format($purchase->order_tax, config('decimal')),
                'order_tax_rate'  => number_format($purchase->order_tax_rate, config('decimal')),
                'order_discount'  => number_format($purchase->order_discount, config('decimal')),
                'shipping_cost'   => number_format($purchase->shipping_cost, config('decimal')),
                'grand_total'     => number_format($purchase->grand_total, config('decimal')),
                'returned_amount' => number_format(DB::table('return_purchases')->where('purchase_id', $purchase->id)->sum('grand_total'), config('decimal')),
                'paid_amount'     => number_format($purchase->paid_amount, config('decimal')),
                'due'             => number_format($purchase->grand_total - $purchase->paid_amount, config('decimal')),
                'note'            => preg_replace('/\s+/S', " ", $purchase->note ?? 'N/A'),
                'created_by'      => [
                    'name'  => $purchase->user->name ?? 'N/A',
                    'email' => $purchase->user->email ?? 'N/A',
                ],
                'document'        => $purchase->document ?? null,
                'currency'        => [
                    'code'         => $purchase->currency->code ?? 'N/A',
                    'exchange_rate'=> $purchase->exchange_rate ?? 'N/A',
                ]
            ];
        });

    }

    private function formatPurchaseStatus($status)
    {
        return match ($status) {
            1 => trans('file.Recieved'),
            2 => trans('file.Partial'),
            3 => trans('file.Pending'),
            default => trans('file.Ordered'),
        };
    }

    private function formatPaymentStatus($status)
    {
        return $status == 1 ? trans('file.Due') : trans('file.Paid');
    }

    public function getFilters($request)
    {
        return [
            'warehouse_id'    => $request->input('warehouse_id', 0),
            'purchase_status' => $request->input('purchase_status', 0),
            'payment_status'  => $request->input('payment_status', 0),
            'starting_date'   => $request->input('starting_date', now()->subYear()->format('Y-m-d')),
            'ending_date'     => $request->input('ending_date', now()->format('Y-m-d')),
        ];
    }

}
