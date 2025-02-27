<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\IncomeRequest;
use App\Services\Tenant\IncomeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IncomeController extends Controller
{
    protected IncomeService $incomeService;

    public function __construct(IncomeService $incomeService)
    {
        $this->incomeService = $incomeService;
    }

    public function index(Request $request)
    {
        try {
        $dataFilter = $this->incomeService->getFilters($request);

        $incomes = $this->incomeService->getIncomes($dataFilter);

        // Load the required lists
        $data = $this->incomeService->getDataIndex();


        return view('Tenant.income.index', compact(
            'dataFilter', 'incomes', 'data'
        ));

        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An error occurred while fetching data. Try again.');
        }
    }

    public function store(IncomeRequest $request): RedirectResponse
    {
        try {
            $this->incomeService->storeIncome($request->validated());
            return redirect()->route('incomes.index')->with('message', 'Income added successfully.');
        } catch (\Exception $e) {
            Log::error("Error in income store: " . $e->getMessage());
            return back()->with('not_permitted', 'An error occurred while store Expense. Try again.');
        }
    }

    public function edit($id)
    {
        try {
            return $this->incomeService->edit($id);

        } catch (\Exception $e) {
            Log::error("Error in income edit: " . $e->getMessage());
            return "Error";
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->incomeService->updateIncome($request->all());
            return redirect()->route('incomes.index')->with('message', 'Income updated successfully.');
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An error occurred while updating Expense. Try again.');
        }
    }

    public function deleteBySelection(Request $request)
    {
        try {
            $this->incomeService->deleteIncomes($request->input('incomeIdArray'));
            return response()->json('Income deleted successfully!');
        } catch (\Exception $e) {
            return response()->json('Error while deleted the income ,try again.');

        }
    }

    public function destroy($id)
    {
        try {
            $this->incomeService->deleteIncome($id);
            return redirect('incomes')->with('not_permitted', __('Data deleted successfully'));
        } catch (\Exception $e) {
            return redirect('incomes')->with('not_permitted', __('Error while deleted the data,try again.'));
        }
    }

}
