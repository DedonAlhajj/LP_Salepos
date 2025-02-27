<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\IncomeCategoryRequest;
use App\Services\Tenant\IncomeCategoryService;
use Illuminate\Support\Facades\Log;


class IncomeCategoryController extends Controller
{
    protected IncomeCategoryService $incomeCategoryService;

    public function __construct(IncomeCategoryService $incomeCategoryService)
    {
        $this->incomeCategoryService = $incomeCategoryService;
    }
    public function index()
    {
        try {
            $income_category_all = $this->incomeCategoryService->getIncomCategories();
            return view('Tenant.income_category.index', compact('income_category_all'));
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An error occurred while fetching data. Try again.');
        }
    }


    public function generateCode()
    {
        try {
            $code = $this->incomeCategoryService->generateCode();
            return response()->json($code);
        } catch (\Exception $e) {
            return response()->json("Error Generate Code");
        }
    }

    public function store(IncomeCategoryRequest $request)
    {
        try {
            $this->incomeCategoryService->storeIncomeCategory($request->validated());
            return redirect('income_categories')->with('message', 'Data inserted successfully');
        } catch (\Exception $e) {
            Log::error("Error in Expense Category store: " . $e->getMessage());
            return redirect('income_categories')->with('not_permitted', 'Error Storing Expense Category');
        }
    }


    public function edit($id)
    {
        try {
            return $this->incomeCategoryService->edit($id);
        } catch (\Exception $e) {
            Log::error("Error in Income Category Edit: " . $e->getMessage());
            return "Error";
        }
    }

    public function update(IncomeCategoryRequest $request, string $id)
    {
        try {
            $this->incomeCategoryService->updateIncome($request->all(),$id);
            return redirect('income_categories')->with('message', 'Data updated successfully');
        } catch (\Exception $e) {
            Log::error("Error in Expense Category store: " . $e->getMessage());
            return redirect('income_categories')->with('not_permitted', 'Error Updating Expense Category');
        }
    }

    public function destroy($id)
    {
        try {
            $this->incomeCategoryService->deleteIncomeCategory($id);
            return redirect('income_categories')->with('message', __('Data deleted successfully'));
        } catch (\Exception $e) {
            return redirect('income_categories')->with('not_permitted', __('Error while deleted the data,try again.'));
        }
    }
}
