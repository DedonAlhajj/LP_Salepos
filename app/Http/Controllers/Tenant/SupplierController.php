<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ClearDueSupplierRequest;
use App\Http\Requests\Tenant\SupplierRequest;
use App\Imports\SupplierImport;
use App\Services\Tenant\ImportService;
use App\Services\Tenant\SupplierService;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{


    protected $supplierService;
    protected $importService;

    public function __construct(SupplierService $supplierService,ImportService $importService)
    {
        $this->supplierService = $supplierService;
        $this->importService = $importService;
    }
    public function index()
    {
        $suppliers = $this->supplierService->getSupplier();
        return view('Tenant.supplier.index', compact('suppliers'));
    }

    public function clearDue(ClearDueSupplierRequest $request)
    {

        try {
            $this->supplierService->clearDue($request->validated());

            return redirect()->back()->with('message', 'Due cleared successfully');

        } catch (\Exception $e) {
            Log::error('clearDue Failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('message', __('An error occurred while creating the customer.'));
        }
    }

    public function create()
    {
        $data = $this->supplierService->create();
        return view('Tenant.supplier.create', $data);
    }

    public function store(SupplierRequest $request)
    {
        try {
            $message = $this->supplierService->createSupplier($request->validated());
            return redirect('supplier')->with('message', $message);

        } catch (\Exception $e) {
            Log::error('Failed to create supplier', ['error' => $e->getMessage()]);
            return redirect()->back()->with('message', __('An error occurred while creating the supplier.'));
        }
    }

    public function edit(Supplier $supplier)
    {
        $this->supplierService->getEditData();
        return view('Tenant.supplier.edit', compact('supplier'));
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        try {
            // تحديث بيانات العميل باستخدام Service
            $message = $this->supplierService->updateSupplier($supplier, $request->validated());
            return redirect('supplier')->with('message',$message);

        } catch (\Exception $e) {
            Log::error('Failed to update customer', ['error' => $e->getMessage()]);
            return redirect()->back()->with('message', __('An error occurred while updating the customer.'));
        }
    }

    public function deleteBySelection(Request $request)
    {
        try {
            $this->supplierService->deleteSuppliers($request->input('supplierIdArray'));
            return response()->json('Supplier deleted successfully!');
        } catch (\Exception $e) {
            return response()->json('Error while deleted the account,try again.');

        }
    }

    public function destroy($id)
    {
        try {
            $this->supplierService->deleteSupplier($id);
            return redirect('supplier')->with('not_permitted', __('Data deleted successfully'));
        } catch (\Exception $e) {
            return redirect('supplier')->with('not_permitted', __('Error while deleted the data,try again.'));
        }
    }

    public function importSupplier(Request $request)
    {
        try {
            $this->importService->import(SupplierImport::class, $request->file('file'));
            return redirect()->back()->with('message', __('Data imported successfully, data will be processed in the background.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
