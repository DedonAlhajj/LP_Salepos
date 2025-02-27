<?php

namespace App\Services\Tenant;

use App\Actions\SendMailAction;
use App\Mail\CustomerCreate;
use App\Mail\SupplierCreate;
use App\Models\CustomerGroup;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Customer;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierService
{
    protected SendMailAction $sendMailAction;
    protected $paymentService;
    protected $cashRegisterService;
    protected $accountService;

    public function __construct(
        SendMailAction         $sendMailAction ,
        PaymentService         $paymentService,
        CashRegisterService    $cashRegisterService,
        ExpenseCategoryService $accountService,
    ) {
        $this->sendMailAction = $sendMailAction;
        $this->paymentService = $paymentService;
        $this->cashRegisterService = $cashRegisterService;
        $this->accountService = $accountService;
    }


    public function authorize($ability)
    {
        if (!Auth::guard('web')->user()->can($ability)) {
            throw new AuthorizationException(__('Sorry! You are not allowed to access this module.'));
        }
    }

    public function getSuppliers()
    {
        return Supplier::all();
    }

    public function getSupplier()
    {
        $this->authorize('suppliers-index');
        try {
            return Cache::remember('suppliers_list', now()->addMinutes(10), function () {
                // جلب كل الموردين دفعة واحدة
                $suppliers = Supplier::all();
                $supplierIds = $suppliers->pluck('id')->toArray();

                // حساب إجمالي المرتجعات لكل مورد
                $returnedAmounts = DB::table('purchases')
                    ->join('return_purchases', 'purchases.id', '=', 'return_purchases.purchase_id')
                    ->whereIn('purchases.supplier_id', $supplierIds)
                    ->where('purchases.payment_status', 1)
                    ->groupBy('purchases.supplier_id')
                    ->select('purchases.supplier_id', DB::raw('SUM(return_purchases.grand_total) as total_returned'))
                    ->pluck('total_returned', 'purchases.supplier_id')
                    ->toArray();

                // حساب إجمالي المدفوعات لكل مورد
                $purchaseData = Purchase::whereIn('supplier_id', $supplierIds)
                    ->where('payment_status', 1)
                    ->groupBy('supplier_id')
                    ->selectRaw('supplier_id, SUM(grand_total) as grand_total, SUM(paid_amount) as paid_amount')
                    ->get()
                    ->keyBy('supplier_id');

                // دمج البيانات مع الموردين
                foreach ($suppliers as $supplier) {
                    $supplier->returned_amount = $returnedAmounts[$supplier->id] ?? 0;
                    $supplier->purchase_data = $purchaseData[$supplier->id] ?? (object) ['grand_total' => 0, 'paid_amount' => 0];
                }

                return $suppliers;
            });
        } catch (Exception $e) {
            report($e);
            return collect([]);
        }
    }

    public function clearDue(array $validatedData)
    {
        DB::beginTransaction();
        try {
            $supplierId = $validatedData['supplier_id'];
            $amountToPay = $validatedData['amount'];
            $paymentNote = $validatedData['note'];

            // جلب الفواتير المستحقة دفعة واحدة
            $duePurchases = Purchase::where('payment_status', 1)
                ->where('supplier_id', $supplierId)
                ->select('id', 'warehouse_id', 'grand_total', 'paid_amount', 'payment_status')
                ->get();

            if ($duePurchases->isEmpty()) {
                return redirect()->back()->with('message', 'لا توجد فواتير مستحقة لهذا المورد.');
            }

            // جلب بيانات الحساب النقدي دفعة واحدة
            $cashRegister = $this->cashRegisterService->getCashRegisterIdAndWarehouse(Auth::guard('web')->id());
            $defaultAccount = $this->accountService->getDefaultAccountId();


            if (!$defaultAccount) {
                return redirect()->back()->with('message', 'لم يتم العثور على الحساب الافتراضي.');
            }

            foreach ($duePurchases as $purchase) {
                if ($amountToPay <= 0) break;

                $dueAmount = $purchase->grand_total - $purchase->paid_amount;

                // تحديد المبلغ الذي سيتم دفعه
                $paymentAmount = min($amountToPay, $dueAmount);
                $newPaymentStatus = ($amountToPay >= $dueAmount) ? 2 : 1;

                // إنشاء عملية الدفع
                $this->paymentService->createPayment(null,$purchase->id, $paymentAmount, $cashRegister?->id, $defaultAccount, $paymentNote ?? null);


                // تحديث بيانات الفاتورة
                $purchase->update([
                    'paid_amount'    => $purchase->paid_amount + $paymentAmount,
                    'payment_status' => $newPaymentStatus
                ]);

                // تقليل المبلغ المدفوع
                $amountToPay -= $paymentAmount;
            }
            Cache::forget('suppliers_list');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception("operation failed: " . $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('suppliers-add');
        return [
            'customer_groups' => CustomerGroup::get(['id', 'name']),
        ];
    }

    public function createSupplier(array $data): string
    {
        $this->authorize('suppliers-add');

        try {
            return DB::transaction(function () use ($data) {
                $image = $data['image'] ?? null;

                // إنشاء الكيان بدون الصورة
                $supplier = Supplier::create($data);

                if (isset($data['both'])) {
                    Customer::create($data);
                }
                // إذا كانت الصورة موجودة، يمكن إضافتها إلى Media Library
                if ($image) {
                    $supplier->addMedia($image)->toMediaCollection('supplier', 'supplier_media');
                }

                // 4️⃣ إرسال البريد الإلكتروني باستخدام خدمة منفصلة
                $this->sendMail($data, SupplierCreate::class);
                if (isset($data['both'])) {
                    $this->sendMail($data, CustomerCreate::class);
                }


                Cache::forget('suppliers_list');

                return __("Supplier" . (!empty($data['both']) ? ' and Customer' : '') . ' created successfully.');
            });
        } catch (Exception $e) {
            Log::error("Error creating Supplier: " . $e->getMessage());
            report($e);
            return __("An error occurred while creating the supplier. Please try again later.");
        }
    }

    private function sendMail($data, $eventClass): void
    {
        if (!$this->sendMailAction->execute($data, $eventClass)) {
            Log::warning("Please setup your mail settings to send mail.");
        }
    }

    public function getEditData()
    {
        $this->authorize('suppliers-edit');
    }

    public function updateSupplier(Supplier $supplier, array $data)
    {
        try {
            return DB::transaction(function () use ($data, $supplier) {
                // التحقق مما إذا كان حقل الصورة موجودًا في البيانات
                $image = $data['image'] ?? null;


                // تحديث بيانات الفواتير بدون الصورة
                $supplier = Supplier::findOrFail($supplier->id);
                $supplier->update($data);

                // تحديث الصورة في Media Library إذا تم إرسال صورة جديدة
                if (!empty($image)) {

                    $supplier->clearMediaCollection('supplier');
                    $supplier->addMedia($image)->toMediaCollection('supplier', 'supplier_media');
                }


                Cache::forget('suppliers_list');

                return __('Supplier updated successfully.');
            });
        } catch (Exception $e) {
            Log::error("Error updating Supplier: " . $e->getMessage());
            report($e);
            return __("An error occurred while updating the supplier. Please try again later.");
        }
    }

    public function deleteSuppliers(array $userIds)
    {
        DB::beginTransaction();

        try {

            $suppliers = Supplier::whereIn('id', $userIds)->get();

            foreach ($suppliers as $supplier) {
                $supplier->clearMediaCollection('supplier');
            }

            Supplier::whereIn('id', $userIds)->delete();
            Cache::forget('suppliers_list');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error while deleting the Supplier: ' . $e->getMessage());
            throw new Exception("operation failed: " . $e->getMessage());
        }
    }

    public function deleteSupplier($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->clearMediaCollection('supplier');
            $supplier->delete();

            Cache::forget('suppliers_list');
        } catch (\Exception $e) {
            Log::error('Error while deleting the Supplier: ' . $e->getMessage());
            throw new Exception("operation failed: " . $e->getMessage());
        }
    }

}

