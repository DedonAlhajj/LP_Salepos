<?php

namespace App\Services\Tenant;

use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExpenseCategoryService
{

    public function getExpenseCategories()
    {
        return ExpenseCategory::all();
    }

    public function generateCode(){
        $uniqueNumber = str_replace('-', '', Str::uuid());
        $uniqueNumber = preg_replace('/[^0-9]/', '', $uniqueNumber);

        return substr($uniqueNumber, 0, 10);
    }

    public function storeExpenseCategory(array $data){
        ExpenseCategory::create($data);
    }

    public function updateExpense(array $data,$id){

        $expenseCategory = ExpenseCategory::find($id);
        $expenseCategory->update($data);
    }

    public function deleteExpenseCategorys(array $Ids)
    {
        DB::beginTransaction();

        try {

            ExpenseCategory::whereIn('id', $Ids)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error while deleting the Expense Categories: ' . $e->getMessage());
            throw new \Exception("operation failed: " . $e->getMessage());
        }
    }

    public function deleteExpenseCategory($id)
    {
        try {
            $expenseCategory = ExpenseCategory::findOrFail($id);
            $expenseCategory->delete();

        } catch (\Exception $e) {
            Log::error('Error while deleting the account: ' . $e->getMessage());
            throw new \Exception("operation failed: " . $e->getMessage());
        }
    }


}

