<?php

namespace App\Services\Tenant;

use App\Models\CustomField;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesService
{
    protected $paymentService;
    protected $cashRegisterService;
    protected $accountService;
    protected $customerService;

    public function __construct(
        PaymentService $paymentService,
        CashRegisterService $cashRegisterService,
        AccountService $accountService,
        CustomerService $customerService
    ) {
        $this->paymentService = $paymentService;
        $this->cashRegisterService = $cashRegisterService;
        $this->accountService = $accountService;
        $this->customerService = $customerService;
    }

    public function clearDue(array $validatedData)
    {
        DB::beginTransaction();

        try {
            $customerId = $validatedData['customer_id'];
            $totalPaidAmount = $validatedData['amount'];

            // استرجاع بيانات العميل
            $customer = $this->customerService->findById($customerId);

            // إضافة الرصيد السابق للعميل إلى المبلغ المدفوع
            $totalPaidAmount += $customer->credit_balance;

            $sales = Sale::where('customer_id', $customerId)
                ->where('payment_status', '!=', 4)
                ->select('id', 'warehouse_id', 'grand_total', 'paid_amount', 'payment_status')
                ->get();

            foreach ($sales as $sale) {
                if ($totalPaidAmount <= 0) {
                    break;
                }

                $dueAmount = $sale->grand_total - $sale->paid_amount;
                $paidAmount = min($totalPaidAmount, $dueAmount);
                $paymentStatus = ($paidAmount == $dueAmount) ? 4 : 2;

                $cashRegisterId = $this->cashRegisterService->getCashRegisterId(Auth::guard('web')->id(), $sale->warehouse_id);
                $accountId = $this->accountService->getDefaultAccountId();

                $this->paymentService->createPayment($sale->id,null, $paidAmount, $cashRegisterId, $accountId, $validatedData['note'] ?? null);

                $sale->update([
                    'paid_amount' => $sale->paid_amount + $paidAmount,
                    'payment_status' => $paymentStatus
                ]);

                $totalPaidAmount -= $paidAmount;
            }

            // ✅ التأكد أن المبلغ المتبقي يتم حفظه بشكل صحيح
            $remainingAmount = max(0, $totalPaidAmount); // التأكد من عدم وجود قيم سالبة

            // تحديث رصيد العميل في حال وجود مبلغ زائد

            $customer->update([
                'credit_balance' => $remainingAmount
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }


    public function getSalesData($customerIds)
    {
        return DB::table('sales')
            ->whereIn('customer_id', $customerIds)
            ->where('payment_status', '!=', 4)
            ->selectRaw('customer_id, SUM(grand_total) as grand_total, SUM(paid_amount) as paid_amount')
            ->groupBy('customer_id')
            ->get()
            ->keyBy('customer_id');
    }

    public function getReturnedAmounts($customerIds)
    {
        return DB::table('sales')
            ->leftJoin('returns', 'sales.id', '=', 'returns.sale_id')
            ->whereIn('sales.customer_id', $customerIds)
            ->where('sales.payment_status', '!=', 4)
            ->selectRaw('sales.customer_id, COALESCE(SUM(returns.grand_total), 0) as total_returned')
            ->groupBy('sales.customer_id')
            ->get()
            ->keyBy('customer_id');
    }


}

