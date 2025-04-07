<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\QuotationRequest;
use App\Imports\BillerImport;
use App\Services\Tenant\BillerService;
use App\Services\Tenant\ImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class BillerController extends Controller
{

    protected $billerService;
    protected $importService;

    public function __construct(BillerService $billerService, ImportService $importService)
    {
        $this->billerService = $billerService;
        $this->importService = $importService;
    }

    /**
     * Display a list of all billers.
     * Retrieves biller data from the service layer and returns the index view.
     *
     * @return View The response view displaying the list of billers.
     */
    public function index(): View
    {
        // Fetch all biller records from the service layer
        $billers = $this->billerService->getAllBillerss();

        // Return the index view with the fetched biller data
        return view('Tenant.biller.index', compact('billers'));
    }

    /**
     * Show the create biller view.
     * Calls the service layer to prepare any necessary data before showing the view.
     *
     * @return View The response view displaying the biller creation form.
     */
    public function create()
    {
        // Prepare any necessary data for the creation process
        $this->billerService->create();

        // Return the create view
        return view('Tenant.biller.create');
    }

    /**
     * Store a new biller record.
     * Validates request data, passes it to the service layer, and redirects to the index page.
     * Handles any exceptions by logging and returning an error message.
     *
     * @param QuotationRequest $request The incoming validated request data.
     * @return RedirectResponse Redirects to the index page with a success or error message.
     */
    public function store(QuotationRequest $request): RedirectResponse
    {
        try {
            // Validate request data and pass it to the service for storage
            $message = $this->billerService->createBiller($request->validated());

            // Redirect to the biller index page with success message
            return redirect()->route('biller.index')->with('message', __($message));
        } catch (\Exception $e) {
            // Handle error, log it, and return an appropriate message
            return redirect()->back()
                ->withErrors(['error' => __('Failed to create Biller. Please try again.')])
                ->withInput();
        }
    }

    /**
     * Show the edit view for a specific biller.
     * Fetches biller data based on ID and returns the edit view.
     *
     * @param int $id The unique identifier of the biller to edit.
     * @return View The response view displaying the biller edit form.
     */
    public function edit($id): View
    {
        // Retrieve biller details for editing
        $biller = $this->billerService->getBillerEditData($id);

        // Return the edit view with retrieved data
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
