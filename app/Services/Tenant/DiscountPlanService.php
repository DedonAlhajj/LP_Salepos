<?php

namespace App\Services\Tenant;


use App\Models\Customer;
use App\Models\DiscountPlan;
use App\Models\DiscountPlanCustomer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiscountPlanService
{

    public function getDiscountPlan(): \Illuminate\Database\Eloquent\Collection|array
    {
        try {
            return DiscountPlan::with('customers')->orderBy('id', 'desc')->get();
        }catch (\Exception $exception) {
            Log::error('DiscountPlan index Failed: ' . $exception->getMessage());
            throw new \Exception('An error occurred while DiscountPlan. Please try again.');
        }
    }

    public function createDiscountPlan(array $data)
    {
        DB::beginTransaction();
        try {
            // Attempt to create a new DiscountPlan using data from the DTO
            $discount_plan = DiscountPlan::create($data);
            foreach ($data['customer_id'] as $key => $customer_id) {
                DiscountPlanCustomer::create(['discount_plan_id' => $discount_plan->id, 'customer_id' => $customer_id]);
            }
            DB::commit(); // Commit the transaction if everything succeeds
        } catch (\Exception $e) {
            DB::rollBack();
            // Log any error that occurs during the creation process
            Log::error('Failed to create DiscountPlan: ' . $e->getMessage());
            return null; // Return null if creation fails
        }
    }

    public function edit($id)
    {
        return [
            'lims_discount_plan' => DiscountPlan::with('customers')->find($id),
            'lims_customer_list' => Customer::all(),
            'customer_ids' => DiscountPlanCustomer::where('discount_plan_id', $id)->pluck('customer_id')->toArray(),
        ];
    }

    public function updateDiscountPlan(int $id, array $data)
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $discountPlan = DiscountPlan::findOrFail($id);

                // جلب العملاء الحاليين
                $preCustomerIds = DiscountPlanCustomer::where('discount_plan_id', $id)->pluck('customer_id')->toArray();

                // حذف العملاء الغير موجودين في البيانات الجديدة
                DiscountPlanCustomer::where('discount_plan_id', $id)
                    ->whereNotIn('customer_id', $data['customer_id'])
                    ->delete();

                // إدراج العملاء الجدد غير الموجودين مسبقًا
                $newCustomers = array_diff($data['customer_id'], $preCustomerIds);
                $insertData = array_map(fn($customerId) => [
                    'discount_plan_id' => $id,
                    'customer_id' => $customerId
                ], $newCustomers);
                DiscountPlanCustomer::insert($insertData);

                // تحديث الخطة
                $discountPlan->update($data);
            });
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new \Exception('discountPlan not found.'); // Rethrow if the discountPlan is not found
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve discountPlan: ' . $e->getMessage()); // Log any other errors
            throw new \Exception('Failed to approve discountPlan.'); // Rethrow the exception
        }
    }



}

