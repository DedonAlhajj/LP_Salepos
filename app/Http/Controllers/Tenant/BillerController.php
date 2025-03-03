<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\QuotationRequest;
use App\Imports\BillerImport;
use App\Services\Tenant\BillerService;
use App\Services\Tenant\ImportService;
use Illuminate\Http\Request;


class BillerController extends Controller
{
    use \App\Traits\CacheForget;

    protected $billerService;
    protected $importService;

    public function __construct(BillerService $billerService,ImportService $importService)
    {
        $this->billerService = $billerService;
        $this->importService = $importService;
    }
    public function index()
    {
        $billers = $this->billerService->getAllBillerss();
        return view('Tenant.biller.index', compact('billers'));
    }

    public function create()
    {
        $this->billerService->create();
        return view('Tenant.biller.create');
    }

    public function store(QuotationRequest $request)
    {

        try {
            $message = $this->billerService->createBiller($request->validated());
            return redirect()->route('biller.index')->with('message', __($message));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => __('Failed to create Biller. Please try again.')])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $biller = $this->billerService->getBillerEditData($id);
        return view('Tenant.biller.edit', compact('biller'));
    }

    public function update(QuotationRequest $request, $id)
    {
        try {
            $this->billerService->updateBiller($id, $request->validated());
            return redirect('biller')->with('message', "Data updated successfully");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['message' => 'Error while updating the account,try again.'])
                ->withInput();
        }

    }

    public function importBiller(Request $request)
    {
        try {
            $this->importService->import(BillerImport::class, $request->file('file'));
            return redirect()->back()->with('message', __('Data imported successfully, data will be processed in the background.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function deleteBySelection(Request $request)
    {
        try {
            $this->billerService->deleteBillers($request->input('billerIdArray'));
            return response()->json('Billers deleted successfully!');
        } catch (\Exception $e) {
            return response()->json('Error while deleted the account,try again.');

        }
    }

    public function destroy($id)
    {
        try {
            $this->billerService->deleteBiller($id);
            return redirect('biller')->with('not_permitted', __('Data deleted successfully'));
        } catch (\Exception $e) {
            return redirect('biller')->with('not_permitted', __('Error while deleted the data,try again.'));
        }
    }

    public function indexTrashed()
    {
        $billers = $this->billerService->getTrashedBiller();
        return view('Tenant.biller.indexTrashed', compact('billers'));
    }

    public function restore($id)
    {
        try {
            $this->billerService->restoreBiller($id);
            return redirect('biller')->with('not_permitted', __('Restored Data successfully'));
        } catch (\Exception $e) {
            return redirect('biller')->with('not_permitted', __('Error while restored the data,try again.'));
        }
    }
}
