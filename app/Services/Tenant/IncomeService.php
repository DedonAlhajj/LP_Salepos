<?php

namespace App\Services\Tenant;

use App\Models\Income;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncomeService
{

    protected WarehouseService $warehouseService;
    protected AccountService $accountService;
    protected CashRegisterService $cashRegisterService;

    public function __construct(
        WarehouseService $warehouseService,
        AccountService $accountService,
        CashRegisterService $cashRegisterService
    ) {
        $this->warehouseService = $warehouseService;
        $this->accountService = $accountService;
        $this->cashRegisterService = $cashRegisterService;
    }


    /** index Fun */
    public function getIncomes(array $filters)
    {
        return Income::with(['warehouse', 'incomeCategory'])
            //  ->filterByDate($filters['starting_date'], $filters['ending_date'])
            ->applyStaffAccess()
            ->applyWarehouseFilter($filters['warehouse_id'])
            ->orderBy($filters['order_by'], $filters['order_dir'])
            ->get();
    }

    public function getFilters($request)
    {
        return [
            'warehouse_id' => $request->input('warehouse_id', 0),
            'order_by' => 'created_at',
            'order_dir' => "desc",
            'starting_date' => $request->input('starting_date', now()->subYear()->format('Y-m-d')),
            'ending_date' => $request->input('ending_date', now()->format('Y-m-d')),
        ];
    }

    public function getDataIndex(){
        return [
            'lims_account_list' => $this->accountService->getActiveAccounts(),
            'lims_warehouse_list' => $this->warehouseService->getWarehouses(),
        ];
    }


    /** Store */
    public function storeIncome(array $data)
    {
        try {
            return Income::create([
                'reference_no' => 'er-' . now()->format('Ymd-His'),
                'user_id' => Auth::id(),
                'income_category_id' => $data['income_category_id'],
                'amount' =>$data['amount'],
                'warehouse_id' => $data['warehouse_id'],
                'account_id' => $data['account_id'],
                'note' => $data['note'] ?? null,
                'created_at' => $data['created_at'] ?? now(),
                'cash_register_id' => $this->cashRegisterService->getCashRegisterId(Auth::id(),$data['warehouse_id']),
            ]);
        } catch (\Exception $e) {
            Log::error("Error in Income store: " . $e->getMessage());
            throw new \Exception($e);
        }
    }

    /** Update */

    public  function edit($id)
    {
        try {
            $income_data = Income::find($id);
            $income_data->date = date('d-m-Y', strtotime($income_data->created_at->toDateString()));
            return $income_data;

        } catch (\Exception $e) {
            Log::error("Error in Income edit: " . $e->getMessage());
            throw new \Exception($e);
        }
    }

    public function updateIncome(array $data)
    {
        try {
            $income_data = Income::find($data['income_id']);
            $data['created_at'] = date("Y-m-d H:i:s", strtotime($data['created_at']));
            $income_data->update($data);
            return $income_data;

        } catch (\Exception $e) {
            Log::error("Error in Income store: " . $e->getMessage());
            throw new \Exception($e);
        }
    }


    /** Delete */

    public function deleteIncomes(array $Ids)
    {
        DB::beginTransaction();

        try {

            Income::whereIn('id', $Ids)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error while deleting the Income: ' . $e->getMessage());
            throw new \Exception("operation failed: " . $e->getMessage());
        }
    }

    public function deleteIncome($id)
    {
        try {
            $expense = Income::findOrFail($id);
            $expense->delete();

        } catch (\Exception $e) {
            Log::error('Error while deleting the Income: ' . $e->getMessage());
            throw new \Exception("operation failed: " . $e->getMessage());
        }
    }







}

