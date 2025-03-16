<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\MoneyTransferRequest;
use App\Services\Tenant\MoneyTransferService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class MoneyTransferController extends Controller
{

    protected MoneyTransferService $moneyTransferService;

    public function __construct(MoneyTransferService $moneyTransferService)
    {
        $this->moneyTransferService = $moneyTransferService;
    }

    /**
     * Display a listing of money transfers.
     *
     * This method retrieves the list of money transfers from the service layer and returns
     * the data to the view for display. It uses the authorization system to check if the user
     * has the necessary permission to access the money transfer functionality. If an error occurs
     * during the process, an error message is displayed to the user.
     *
     * @return View|Factory|Application|RedirectResponse The view to display with the data, or a redirect in case of an error.
     */
    public function index(): View|Factory|Application|RedirectResponse
    {
        try {
            // Authorize the user to ensure they have permission to view money transfers
            $this->authorize('money-transfer');

            // Retrieve the list of money transfers from the service layer
            $data = $this->moneyTransferService->index();

            // Return the view with the data to be displayed
            return view('Tenant.money_transfer.index', $data);
        } catch (\Exception $e) {
            // If an error occurs, redirect back with an error message
            return back()->with('not_permitted', 'An error occurred. Try again later.');
        }
    }

    /**
     * Handle the money transfer process, ensuring valid data and performance.
     *
     * This method handles the money transfer process by validating inputs, generating
     * a reference number, and storing the transaction. It ensures that the from_account_id
     * is not the same as the to_account_id, and handles any potential errors efficiently.
     *
     * @param \Illuminate\Http\Request $request The incoming request object containing transfer data.
     * @return \Illuminate\Http\RedirectResponse Redirects back with a success or error message.
     */
    public function store(MoneyTransferRequest $request): RedirectResponse
    {
        // Validate the incoming data
        $validatedData = $request->validated();

        try {
            // Generate a unique reference number for the transaction
            $validatedData['reference_no'] = $this->moneyTransferService->generateReferenceNo();

            // Use the MoneyTransferService to handle the business logic of storing the transfer
            $this->moneyTransferService->storeTransfer($validatedData);

            // Return a success message if everything goes well
            return redirect()->back()->with('message', 'Money transferred successfully');

        } catch (\Exception $e) {
            // Handle errors and provide meaningful messages
            return redirect()->back()->with('not_permitted', 'An error occurred while processing the transfer: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified money transfer record.
     *
     * This method handles the process of updating an existing money transfer record. It validates
     * the incoming request data using the MoneyTransferRequest, and then calls the service layer
     * to perform the update operation. In case of an error, it catches the exception and returns
     * an error message to the user.
     *
     * @param MoneyTransferRequest $request The validated request data.
     * @param int $id The ID of the money transfer to be updated.
     * @return RedirectResponse A redirect response indicating success or failure.
     */
    public function update(MoneyTransferRequest $request, $id): RedirectResponse
    {
        try {
            // Pass the validated data to the service layer for updating the transfer
            $data = $request->validated();
            $this->moneyTransferService->updateTransfer($data);

            // Return a success message if the update was successful
            return redirect()->back()->with('message', 'Money transfer updated successfully');
        } catch (\Exception $e) {
            // If an error occurs, catch the exception and return a failure message
            return redirect()->back()->with(['not_permitted' => 'Failed to update money transfer. ' . $e->getMessage()]);
        }
    }

    /**
     * Delete the specified money transfer record.
     *
     * This method handles the process of deleting a money transfer record. It first checks if
     * the transfer exists, then calls the service layer to perform the deletion operation.
     * In case of an error, it catches the exception and returns an error message to the user.
     *
     * @param int $id The ID of the money transfer to be deleted.
     * @return RedirectResponse A redirect response indicating success or failure.
     */
    public function destroy($id): RedirectResponse
    {
        try {
            // Pass the ID to the service layer for deleting the transfer
            $this->moneyTransferService->deleteTransfer($id);

            // Return a success message if the deletion was successful
            return redirect()->back()->with('message', 'Money transfer deleted successfully');
        } catch (\Exception $e) {
            // If an error occurs, catch the exception and return a failure message
            return redirect()->back()->with(['not_permitted' => 'Failed to delete money transfer. ' . $e->getMessage()]);
        }
    }

}
