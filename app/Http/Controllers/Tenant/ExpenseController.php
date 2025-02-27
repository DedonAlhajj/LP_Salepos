<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ExpenseRequest;
use App\Services\Tenant\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Traits\StaffAccess;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    use StaffAccess;

    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }
    public function index(Request $request)
    {
        try {
            // Set default values
            $dataFilter = $this->expenseService->getFilters($request);

            // Fetch data via `ExpenseService`
            $expenses = $this->expenseService->getFilteredExpenses($dataFilter);

            // Load the required lists
            $data = $this->expenseService->getDataIndex();

            return view('Tenant.expense.index', compact(
                'dataFilter', 'expenses', 'data'
            ));
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An error occurred while fetching data. Try again.');
        }
    }

    public function store(ExpenseRequest $request): RedirectResponse
    {
        try {
            $this->expenseService->storeExpense($request->validated());
            return redirect()->route('expenses.index')->with('message', 'Expense added successfully.');
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An error occurred while store Expense. Try again.');
        }
    }


    public function edit($id)
    {
        try {
            return $this->expenseService->edit($id);

        } catch (\Exception $e) {
            Log::error("Error in Expense edit: " . $e->getMessage());
            return "Error";
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->expenseService->updateExpense($request->all());
            return redirect()->route('expenses.index')->with('message', 'Expense updated successfully.');
        } catch (\Exception $e) {
            return back()->with('not_permitted', 'An error occurred while updating Expense. Try again.');
        }
    }

    public function deleteBySelection(Request $request)
    {
        try {
            $this->expenseService->deleteExpenses($request->input('expenseIdArray'));
            return response()->json('Expense deleted successfully!');
        } catch (\Exception $e) {
            return response()->json('Error while deleted the account,try again.');

        }
    }

    public function destroy($id)
    {
        try {
            $this->expenseService->deleteExpense($id);
            return redirect('expenses')->with('not_permitted', __('Data deleted successfully'));
        } catch (\Exception $e) {
            return redirect('expenses')->with('not_permitted', __('Error while deleted the data,try again.'));
        }
    }

}
