<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ClearDueRequest;
use App\Http\Requests\Tenant\CustomerRequest;
use App\Imports\CustomerImport;
use App\Services\Tenant\CustomerService;
use App\Services\Tenant\DepositService;
use App\Services\Tenant\ImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    protected $customerService;
    protected $importService;
    protected $depositService;

    public function __construct(
        DepositService $depositService,
        CustomerService $customerService,
        ImportService $importService)
    {
        $this->depositService = $depositService;
        $this->customerService = $customerService;
        $this->importService = $importService;
    }

    public function index()
    {
        $data = $this->customerService->getCustomersWithDetails();

        return view('Tenant.customer.index', $data);
    }

    public function clearDue(ClearDueRequest $request): RedirectResponse
    {
        try {
            $this->customerService->clearCustomerDue($request->validated());

            return redirect()->back()->with('message', 'Due cleared successfully');

        } catch (\Exception $e) {
            Log::error('clearDue Failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('message', __('An error occurred while creating the customer.'));
        }
    }

    public function create()
    {
        $data = $this->customerService->create();
        return view('Tenant.customer.create', $data);
    }

    public function store(CustomerRequest $request)
    {

        try {
            // التحقق من صحة البيانات
            $validatedData = $request->validated();

            // إنشاء العميل باستخدام Service
            $customerInfo = $this->customerService->createCustomer($validatedData);

            // إرجاع الاستجابة بناءً على `pos`
            return $validatedData['pos']
                ? response()->json($customerInfo, 201)
                : redirect()->route('customer.index')->with('message', $customerInfo['message']);

        } catch (\Exception $e) {
            Log::error('Failed to create customer', ['error' => $e->getMessage()]);
            return redirect()->back()->with('message', __('An error occurred while creating the customer.'));
        }
    }

    public function edit(Customer $customer)
    {
        $data = $this->customerService->getEditData($customer);
        return view('Tenant.customer.edit', $data);
    }

    public function update(CustomerRequest $request, Customer $customer)
    {

        try {
            // تحديث بيانات العميل باستخدام Service
            $customerInfo = $this->customerService->updateCustomer($customer, $request->validated());

            return redirect()->route('customer.index')
                ->with('message', $customerInfo['message']);

        } catch (\Exception $e) {
            Log::error('Failed to update customer', ['error' => $e->getMessage()]);
            return redirect()->back()->with('message', __('An error occurred while updating the customer.'));
        }
    }

    public function importCustomer(Request $request)
    {
        try {
            $this->importService->import(CustomerImport::class, $request->file('file'));
            return redirect()->back()->with('message', __('Data imported successfully, data will be processed in the background.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getDeposit($customerId)
    {
        $deposits = $this->depositService->getDepositsByCustomerId($customerId);
        return response()->json($deposits);
    }

    public function addDeposit(Request $request)
    {
        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount'      => 'required|numeric|min:1',
            'note'        => 'nullable|string|max:500',
        ]);

        try {
            $message = $this->depositService->addDeposit($validatedData);
            return redirect()->route('customer.index')->with('message', $message);

        } catch (\Exception $e) {
            Log::error('Failed to create customer', ['error' => $e->getMessage()]);
            return redirect()->back()->with('message', __('An error occurred while creating the customer.'));
        }


    }

    public function updateDeposit(Request $request)
    {
        $validatedData = $request->validate([
            'deposit_id' => 'required|exists:deposits,id',
            'amount'     => 'required|numeric|min:1',
            'note'       => 'nullable|string|max:500',
        ]);

        try {
            $this->depositService->updateDeposit($validatedData);
            return redirect()->route('customer.index')->with('success', 'Deposit updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to create customer', ['error' => $e->getMessage()]);
            return redirect()->back()->with('message', __('An error occurred while updating the customer.'));
        }
    }

    public function deleteDeposit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deposits,id',
        ]);

        try {
            $this->depositService->deleteDeposit($request->id);
            return redirect()->route('customer.index')->with('success', 'Deposit deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to create customer', ['error' => $e->getMessage()]);
            return redirect()->back()->with('message', __('An error occurred while deleting the customer.'));
        }
    }

    public function deleteBySelection(Request $request)
    {
        try {
            $this->customerService->deleteCustomers($request->input('customerIdArray'));
            return response()->json('Customer deleted successfully!');
        } catch (\Exception $e) {
            return response()->json('Error while deleted the account,try again.');

        }
    }

    public function destroy($id)
    {
        try {
            $this->customerService->deleteCustomer($id);
            return redirect()->route('customer.index')->with('not_permitted', __('Data deleted successfully'));
        } catch (\Exception $e) {
            return redirect()->route('customer.index')->with('not_permitted', __('Error while deleted the data,try again.'));
        }
    }




}
