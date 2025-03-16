<?php

namespace App\Http\Controllers\Tenant;

use App\DTOs\AccountDTO;
use App\Exceptions\AccountDeletionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\AccountRequest;
use App\Http\Requests\Tenant\AccountStatementRequest;
use App\Services\Tenant\AccountService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;



class AccountsController extends Controller
{

    protected AccountService $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }


    /**
     * Display a listing of active accounts.
     *
     * This method checks if the user is authorized to access the account index page using
     * the 'account-index' permission. Then, it retrieves the list of active accounts using
     * the account service and returns it to the view for rendering.
     *
     * @return \Illuminate\View\View The view containing the list of active accounts.
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(): \Illuminate\View\View
    {
        // Authorize the user to ensure they have permission to view accounts
        $this->authorize('account-index');

        // Retrieve the active accounts from the account service
        $accounts = $this->accountService->getActiveAccounts();

        // Return the view with the active accounts
        return view('Tenant.account.index', compact('accounts'));
    }

    /**
     * Store a newly created account.
     *
     * This method validates the incoming request data using the AccountRequest and creates
     * a new account using the AccountService. If the account creation is successful, the user
     * is redirected back to the accounts index page with a success message. If an error occurs,
     * it catches the exception and redirects back with an error message.
     *
     * @param AccountRequest $request The validated request data for creating an account.
     * @return RedirectResponse The response to redirect to the accounts index page.
     */
    public function store(AccountRequest $request): RedirectResponse
    {
        try {
            // Create an AccountDTO from the validated request data
            $accountDTO = new AccountDTO($request->validated());

            // Use the account service to create the new account
            $this->accountService->createAccount($accountDTO);

            // Redirect back to the accounts index page with a success message
            return redirect()->route('accounts.index')->with('message', 'Account created successfully');
        } catch (\Exception $e) {
            // Redirect back to the accounts index page with an error message
            return redirect()->route('accounts.index')->with('not_permitted', $e->getMessage());
        }
    }

    /**
     * Handle setting an account as the default account.
     *
     * This method attempts to set the specified account as the default by calling the
     * account service. If successful, a success message is returned as a JSON response.
     * If the account is not found or another error occurs, an appropriate error message
     * is returned as a JSON response.
     *
     * @param int $id The ID of the account to be set as default.
     * @return JsonResponse The response with the result message (success or error).
     */
    public function makeDefault(int $id): JsonResponse
    {
        try {
            // Attempt to set the account as default using the account service
            $message = $this->accountService->makeDefault($id);

            // Return a JSON response with a success message
            return response()->json($message, 200);
        } catch (ModelNotFoundException $e) {
            // Log the error if the account is not found
            Log::error('Error makeDefault account: ' . $e->getMessage());

            // Return a JSON response with a 404 error message
            return response()->json('Account not found', 404);
        } catch (\Exception $e) {
            // Log any other errors that occur
            Log::error('Error makeDefault account: ' . $e->getMessage());

            // Return a JSON response with a 500 error message
            return response()->json('An unexpected error occurred', 500);
        }
    }

    /**
     * Update the specified account in storage.
     *
     * This method handles updating an existing account. It first validates the incoming request
     * using the `AccountRequest`. After validation, it converts the data into a Data Transfer
     * Object (DTO) and calls the `accountService` to update the account. If the update is successful,
     * the user is redirected back to the account index page with a success message. If any exception
     * occurs during the process, the user is redirected back with an error message.
     *
     * @param AccountRequest $request The validated request data for updating an account.
     * @param int $id The ID of the account to be updated.
     * @return RedirectResponse The response to redirect to the accounts index page.
     */
    public function update(AccountRequest $request, int $id): RedirectResponse
    {
        try {
            // Convert the validated request data into a DTO (Data Transfer Object)
            $accountDTO = new AccountDTO($request->validated());

            // Call the service to update the account
            $account = $this->accountService->updateAccount($accountDTO);

            // Redirect to the accounts index page with a success message
            return redirect()->route('accounts.index')->with('message', 'Account updated successfully');
        } catch (\Exception $e) {
            // In case of failure, redirect with an error message
            return redirect()->route('accounts.index')->with('not_permitted', $e->getMessage());
        }
    }

    /**
     * Remove the specified account from storage.
     *
     * This method handles deleting an account. Before performing any action, it checks whether the
     * application is in demo mode (as specified in the configuration). If demo mode is enabled,
     * deletion is not allowed, and the user is redirected with a message indicating the feature is
     * disabled. If the account is not in demo mode, the method attempts to delete the account by
     * calling the `accountService`. If the deletion is successful, the user is redirected with
     * a success message. If the deletion fails (e.g., custom exception `AccountDeletionException`),
     * an appropriate error message is shown.
     *
     * @param int $id The ID of the account to be deleted.
     * @return RedirectResponse The response after attempting to delete the account.
     */
    public function destroy(int $id): RedirectResponse
    {
        // Prevent deletion if the application is in demo mode
        if (config('app.demo_mode')) {
            return back()->with('not_permitted', 'This feature is disabled in demo mode.');
        }

        try {
            // Attempt to delete the account using the account service
            $this->accountService->deleteAccount($id);

            // Redirect with a success message if the account was deleted successfully
            return redirect()->route('accounts.index')->with('message', 'Account deleted successfully!');
        } catch (AccountDeletionException $e) {
            // Handle specific account deletion exception and return an error message
            return back()->with('not_permitted', $e->getMessage());
        } catch (\Exception $e) {
            // Catch any other unexpected exceptions and return a generic error message
            return back()->with('not_permitted', 'An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Handles the retrieval and display of the balance sheet for the tenant's account.
     *
     * This function performs the following steps:
     * 1. Verifies the current user's permission to access the balance sheet using the `authorize` method.
     * 2. Calls the `getBalanceSheet` method from the `accountService` to fetch the balance sheet data.
     * 3. If the data is retrieved successfully, it returns a view (`Tenant.account.balance_sheet`) and passes the balance sheet data as an array.
     * 4. If an error occurs (specifically a `RuntimeException`), it catches the exception and redirects the user back with an error message indicating that the operation could not be completed.
     *
     //* @return \Illuminate\View\View The balance sheet view containing the retrieved data, or a redirect with an error message if an exception occurs.
     * @throws \RuntimeException If there is an error while fetching the balance sheet data.
     */
    public function balanceSheet(): View|Factory|Application|RedirectResponse
    {
        // Authorize the user to access the balance sheet page
        $this->authorize('balance-sheet');

        try {
            // Retrieve the balance sheet data using the account service
            $data = $this->accountService->getBalanceSheet();

            // Return the balance sheet view with the data converted to an array
            return view('Tenant.account.balance_sheet', $data->toArray());

        } catch (\RuntimeException $e) {
            // If an error occurs, redirect back with a message indicating an error occurred
            return back()->with('not_permitted', 'An error occurred. Try again later.');
        }
    }

    /**
     * Display the account statement page.
     *
     * This method handles the request to fetch the account statement based on the given request parameters.
     * It retrieves the statement data from the service layer and passes it to the view.
     *
     * @param AccountStatementRequest $request The validated request containing account details.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse Returns the account statement view or redirects back with an error message.
     */
    public function accountStatement(AccountStatementRequest $request)
    {
        try {
            // Fetch the account statement details using the service layer
            $result = $this->accountService->getAccountStatement($request->validated());

            // Return the account statement view with the retrieved data
            return view('Tenant.account.account_statement', [
                'lims_account_data' => $result['account'], // Account details
                'all_transaction_list' => $result['transactions'], // Transactions related to the account
                'balance' => $result['balance'], // Account balance
            ]);
        } catch (\Exception $e) {
            // Handle any errors by redirecting back with an error message
            return back()->with('error', 'An error occurred. Please try again later.');
        }
    }


}
