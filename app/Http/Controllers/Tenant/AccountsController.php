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
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('account-index');

        $accounts = $this->accountService->getActiveAccounts();

        return view('Tenant.account.index', compact('accounts'));
    }

    /**
     * Store a newly created account.
     *
     * @param AccountRequest $request
     * @return RedirectResponse
     */
    public function store(AccountRequest $request): RedirectResponse
    {

        try {
            $accountDTO = new AccountDTO($request->validated());
            $this->accountService->createAccount($accountDTO);

            return redirect()->route('accounts.index')->with('message', 'Account created successfully');
        } catch (\Exception $e) {
            return redirect()->route('accounts.index')->with('not_permitted', $e->getMessage());
        }
    }

    /**
     * Handle setting an account as default.
     *
     * @param int $id The ID of the account to be set as default.
     * @return JsonResponse Response message.
     */
    public function makeDefault(int $id): JsonResponse
    {
        try {
            $message = $this->accountService->makeDefault($id);
            return response()->json($message, 200);
        } catch (ModelNotFoundException $e) {
            Log::error('Error makeDefault account: ' . $e->getMessage());
            return response()->json('Account not found', 404);
        } catch (\Exception $e) {
            Log::error('Error makeDefault account: ' . $e->getMessage());
            return response()->json('An unexpected error occurred', 500);
        }
    }

    /**
     * Update an existing account.
     */
    public function update(AccountRequest $request, int $id): RedirectResponse
    {
        try {
            // Convert Request to DTO
            $accountDTO = new AccountDTO($request->validated());
            // Update account
            $account = $this->accountService->updateAccount($accountDTO);

            // Redirect for web request
            return redirect()->route('accounts.index')->with('message', 'Account updated successfully');
        } catch (\Exception $e) {
            return redirect()->route('accounts.index')->with('not_permitted', $e->getMessage());
        }
    }

    /**
     * Delete an account safely.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        // Prevent deletion in demo mode
        if (config('app.demo_mode')) {
            return back()->with('not_permitted', 'This feature is disabled in demo mode.');
        }

        try {
            $this->accountService->deleteAccount($id);
            return redirect()->route('accounts.index')->with('message', 'Account deleted successfully!');
        } catch (AccountDeletionException $e) {
            return back()->with('not_permitted', $e->getMessage());
        } catch (\Exception $e) {
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
