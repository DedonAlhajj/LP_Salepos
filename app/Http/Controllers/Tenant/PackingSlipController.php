<?php

namespace App\Http\Controllers\Tenant;

use App\DTOs\PackingSlipStoreDTO;
use App\Http\Controllers\Controller;
use App\Services\Tenant\PackingSlipService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackingSlipController extends Controller
{
    protected PackingSlipService $packingSlipService;

    public function __construct(PackingSlipService $packingSlipService)
    {
        $this->packingSlipService = $packingSlipService;
    }

    /**
     * Displays the packing slips for the logged-in user by retrieving data from the service layer.
     * In case of an error, redirects back with an error message.
     *
     * @return View|RedirectResponse The packing slips view or an error redirect.
     */
    public function index(): View|RedirectResponse
    {
        try {
            // Fetch PackingSlips data using the service layer
            $packingSlips = $this->packingSlipService->getPackingSlips();

            // Pass the retrieved PackingSlips data to the view
            return view('Tenant.packing_slip.index', compact('packingSlips'));
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->withErrors(['not_permitted' => __('An error occurred while loading PackingSlips data.')]);
        }
    }


    /**
     * Stores a new packing slip by processing the user request and creating the packing slip
     * using the service layer. Redirects back with a success or error message.
     *
     * @param Request $request The user request containing packing slip data.
     * @return RedirectResponse A redirect response with success or error message.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Convert the request data into a PackingSlipStoreDTO object
            $dto = PackingSlipStoreDTO::fromRequest($request);

            // Use the service layer to create a new packing slip
            $this->packingSlipService->createPackingSlip($dto);

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Packing slip created successfully');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->with('not_permitted', $e->getMessage());
        }
    }


    /**
     * Generates the invoice data for a specific packing slip and displays it in the invoice view.
     * In case of an error, redirects back with an error message.
     *
     * @param int $id The ID of the packing slip to generate the invoice for.
     * @return View|RedirectResponse The invoice view or an error redirect.
     */
    public function genInvoice(int $id): View|RedirectResponse
    {
        try {
            // Generate invoice data using the service layer
            $invoiceData = $this->packingSlipService->generateInvoiceData($id);

            // Pass the invoice data to the view
            return view('Tenant.packing_slip.invoice', $invoiceData);
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->withErrors(['not_permitted' => __($e->getMessage())]);
        }
    }


    /**
     * Deletes a specific packing slip and performs cleanup of associated data.
     * Redirects back with a success or error message.
     *
     * @param int $id The ID of the packing slip to delete.
     * @return RedirectResponse A redirect response with success or error message.
     */
    public function delete(int $id): RedirectResponse
    {
        try {
            // Use the service layer to delete the packing slip
            $this->packingSlipService->deletePackingSlip($id);

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Packing Slip deleted successfully');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->withErrors(['not_permitted' => $e->getMessage()]);
        }
    }


}
