<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ExpenseCategoryRequest;
use App\Imports\ExpenseCategoryImport;
use App\Services\Tenant\ExpenseCategoryService;
use App\Services\Tenant\ImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExpenseCategoryController extends Controller
{

    protected $expenseCategoryService;
    protected $importService;

    public function __construct(ExpenseCategoryService $expenseCategoryService,ImportService $importService)
    {
        $this->expenseCategoryService = $expenseCategoryService;
        $this->importService = $importService;
    }
    public function index()
    {
        try {
            $expense_category_all = $this->expenseCategoryService->getExpenseCategories();
            return view('Tenant.expense_category.index', compact('expense_category_all'));
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An error occurred while fetching data. Try again.');
        }
    }


    public function generateCode()
    {
        try {
            $code = $this->expenseCategoryService->generateCode();
            return response()->json($code);
        } catch (\Exception $e) {
            return response()->json("Error Generate Code");
        }

    }

    public function store(ExpenseCategoryRequest $request)
    {
        try {
            $this->expenseCategoryService->storeExpenseCategory($request->all());
            return redirect('expense_categories')->with('message', 'Data inserted successfully');
        } catch (\Exception $e) {
            Log::error("Error in Expense Category store: " . $e->getMessage());
            return redirect('expense_categories')->with('not_permitted', 'Error Storing Expense Category');
        }
    }


    public function update(ExpenseCategoryRequest $request, $id)
    {
        try {
            $this->expenseCategoryService->updateExpense($request->all(),$id);
            return redirect('expense_categories')->with('message', 'Data updated successfully');
        } catch (\Exception $e) {
            Log::error("Error in Expense Category store: " . $e->getMessage());
            return redirect('expense_categories')->with('not_permitted', 'Error Updating Expense Category');
        }
    }

    public function import(Request $request){
        try {
            $this->importService->import(ExpenseCategoryImport::class, $request->file('file'));
            return redirect()->back()->with('message', __('Data imported successfully, data will be processed in the background.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('not_permitted', $e->getMessage());
        }
    }

    public function deleteBySelection(Request $request)
    {
        try {
            $this->expenseCategoryService->deleteExpenseCategorys($request->input('expense_categoryIdArray'));
            return response()->json('Expense Category deleted successfully!');
        } catch (\Exception $e) {
            return response()->json('Error while deleted the account,try again.');

        }
    }

    public function destroy($id)
    {
        try {
            $this->expenseCategoryService->deleteExpenseCategory($id);
            return redirect('expense_categories')->with('not_permitted', __('Data deleted successfully'));
        } catch (\Exception $e) {
            return redirect('expense_categories')->with('not_permitted', __('Error while deleted the data,try again.'));
        }
    }

}
