<?php

namespace App\Services\Tenant;

use App\Actions\SendMailAction;
use App\Mail\CustomerCreate;
use App\Mail\SupplierCreate;
use App\Models\CustomerGroup;
use App\Models\CustomField;
use App\Models\Supplier;
use App\Models\Customer;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerService
{
    protected SendMailAction $sendMailAction;
    protected UserService $userService;
    protected CustomFieldService $customFieldService;



    public function __construct(
        SendMailAction $sendMailAction ,
        UserService $userService,
        CustomFieldService $customFieldService)
    {
        $this->sendMailAction = $sendMailAction;
        $this->userService = $userService;
        $this->customFieldService = $customFieldService;
    }

    public function authorize($ability)
    {
        if (!Auth::guard('web')->user()->can($ability)) {
            throw new AuthorizationException(__('Sorry! You are not allowed to access this module.'));
        }
    }


    public function getCustomers()
    {
        return Customer::all();
    }

    /** Get percentage CustomerGroup data for the given Customer id. */
    public function getCustomerGroup($id)
    {
        return CustomerGroup::where('id', function ($query) use ($id) {
            $query->select('customer_group_id')
                ->from('customers')
                ->where('id', $id);
        })
            ->value('percentage');
    }

    public function getCustomersWithDetails()
    {
        $this->authorize('customers-index');
        // تحميل الحقول المخصصة المتعلقة بالعملاء
        $customFields = $this->customFieldService->getCustomFields('customer');
        $fieldNames = $this->customFieldService->getFieldNames($customFields);

        // تحميل بيانات العملاء
        $customers = Customer::with(['discountPlans:id,name', 'customerGroup:id,name', 'customFields'])->get();
        $customerIds = $customers->pluck('id');

        // جلب بيانات المبيعات والمرتجعات
        $salesService = app(SalesService::class);
        $salesData = $salesService->getSalesData($customerIds);
        $returnedAmounts = $salesService->getReturnedAmounts($customerIds);

        // تجهيز البيانات
        $data = $customers->map(fn($customer) => $this->formatCustomerData($customer, $salesData, $returnedAmounts, $fieldNames));

        return compact('fieldNames', 'customFields', 'data');
    }

    private function formatCustomerData($customer, $salesData, $returnedAmounts, $fieldNames)
    {
        $saleData = $salesData[$customer->id] ?? (object) ['grand_total' => 0, 'paid_amount' => 0];
        $returnedAmount = $returnedAmounts[$customer->id]->total_returned ?? 0;

        return [
            'id' => $customer->id,
            'customer_group' => $customer->customerGroup->name ?? '-',
            'credit_balance' =>$customer->credit_balance,
            'customer_details' => $this->formatCustomerDetails($customer),
            'discount_plan' => $customer->discountPlans->pluck('name')->implode(', '),
            'reward_point' => $customer->points,
            'deposited_balance' => number_format($customer->deposit - $customer->expense, 2),
            'total_due' => number_format($saleData->grand_total - $returnedAmount - $saleData->paid_amount, 2),
            'custom_fields' => $this->customFieldService->getCustomerCustomFields($customer, $fieldNames),
        ];
    }

    private function formatCustomerDetails($customer)
    {
        $details = array_filter([
            $customer->name,
            $customer->company_name,
            $customer->email,
            $customer->phone_number,
            "{$customer->address}, {$customer->city}",
            $customer->country
        ]);

        return implode('<br>', $details);
    }

    public function clearCustomerDue(array $validatedData)
    {
        // 🚀 تحميل SalesService فقط عند الحاجة
        $salesService = app(SalesService::class);
        $salesService->clearDue($validatedData);
    }

    public function create()
    {
        $this->authorize('customers-add');
        return [
            'customer_groups' => CustomerGroup::get(['id', 'name']),
            'custom_fields' => CustomField::where('entity_type', 'customer')->get(),
        ];
    }

    public function createCustomer(array $data)
    {
        DB::beginTransaction();

        try {
            // **إنشاء المستخدم إذا كان مطلوبًا**
            if (!empty($data['user'])) {

                $user = $this->userService->createUserRecord([
                    ...$data,
                    'is_active' => true,
                    'role' => 'Customer'
                ]);
                $data['user_id'] = $user->id;
            }

            // **إنشاء المورد إذا كان مطلوبًا**
            if (!empty($data['both'])) {
                Supplier::create($data);
            }

            // **إنشاء العميل**
            $customer = Customer::create($data);

            // **إضافة الحقول المخصصة**
            $this->customFieldService->storeCustomFields($customer, $data);

            if (!$this->sendMailAction->execute($data, CustomerCreate::class)) {
                $message = __('User created successfully. Please setup your mail settings to send mail.');
            } else {
                $messageParts[] = 'Customer';
                if (!empty($data['both'])) {
                    $this->sendMailAction->execute($data, SupplierCreate::class);
                }
                $message = __(implode(' and ', $messageParts) . ' created successfully.');
            }

            DB::commit();

            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone_number' => $customer->phone_number,
                'message' => $message
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Customer creation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function findById(int $customerId)
    {
        return Customer::findOrFail($customerId); // يبحث عن العميل أو يرمي استثناء إذا لم يتم العثور عليه
    }

    public function getEditData($customer)
    {
        $this->authorize('customers-edit');

        return [
            'customer' => $customer,
            'customer_groups' => CustomerGroup::select('id', 'name')->get(),
            'custom_fields' => CustomField::where('entity_type', 'customer')->get(),
            'custom_field_values' => $customer->customFields->pluck('value', 'custom_field_id'),
        ];
    }

    public function updateCustomer(Customer $customer, array $data)
    {
        DB::beginTransaction();
        $data = array_filter($data, function($value) {
            return !is_null($value);  // إزالة القيم null من البيانات
        });

        try {
            // **تحديث بيانات المستخدم إن وجد**
            if (isset($data['user'])) {
                $user = $this->userService->updateOrCreateUserRecord($customer, $data);
                $data['user_id'] = $user->id;
            }

            // **تحديث العميل**
            $customer->update($data);

            // **تحديث الحقول المخصصة**
            $this->customFieldService->updateCustomFields($customer, $data);

            DB::commit();

            return [
                'id' => $customer->id,
                'message' => __('Customer updated successfully.'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Customer update failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function deleteCustomers(array $userIds)
    {
        DB::beginTransaction();

        try {

            Customer::whereIn('id', $userIds)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error while deleting the account: ' . $e->getMessage());
            throw new Exception("operation failed: " . $e->getMessage());
        }
    }

    public function deleteCustomer($id)
    {
        try {
            $user = Customer::findOrFail($id);
            $user->delete();

        } catch (\Exception $e) {
            Log::error('Error while deleting the account: ' . $e->getMessage());
            throw new Exception("operation failed: " . $e->getMessage());
        }
    }

}

