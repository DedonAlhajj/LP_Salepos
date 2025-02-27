<?php

namespace App\Services\Tenant;

use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IncomeCategoryService
{

    public function getIncomCategories()
    {
        return IncomeCategory::all();
    }

    public function generateCode(){
        $uniqueNumber = str_replace('-', '', Str::uuid());
        $uniqueNumber = preg_replace('/[^0-9]/', '', $uniqueNumber);

        return substr($uniqueNumber, 0, 10);
    }

    public function storeIncomeCategory(array $data){
        IncomeCategory::create($data);
    }

    public function edit($id){
        return IncomeCategory::find($id);
    }

    public function updateIncome(array $data,$id)
    {
        $incomeCategory = IncomeCategory::find($data['income_category_id']);
        $incomeCategory->update($data);
    }


    public function deleteIncomeCategory($id)
    {
        try {
            $expenseCategory = IncomeCategory::findOrFail($id);
            $expenseCategory->delete();

        } catch (\Exception $e) {
            Log::error('Error while deleting the income: ' . $e->getMessage());
            throw new \Exception("operation failed: " . $e->getMessage());
        }
    }


}

