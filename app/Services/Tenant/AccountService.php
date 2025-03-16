<?php

namespace App\Services\Tenant;

use App\DTOs\AccountDTO;
use App\DTOs\BalanceSheetDataDTO;
use App\Exceptions\AccountCreationException;
use App\Exceptions\AccountDeletionException;
use App\Models\Account;
use App\Repositories\Tenant\BalanceSheetRepository;
use App\Repositories\Tenant\TransactionRepository;
use App\Services\Tenant\BalanceCalculationStrategy\BalanceSheetStrategyFactory;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;
use RuntimeException;

class AccountService
{
    protected BalanceSheetRepository $repository;
    protected CalculationsService $balanceCalculator;
    protected TransactionRepository $transactionRepository;

    public function __construct(
        BalanceSheetRepository $repository,
        CalculationsService $balanceCalculator,
        TransactionRepository $transactionRepository)
    {
        $this->repository = $repository;
        $this->balanceCalculator = $balanceCalculator;
        $this->transactionRepository = $transactionRepository;
    }

    public function getDefaultAccountId(): int
    {
        return Account::where('is_default', 1)->value('id');
    }


    public function getActiveAccounts()
    {
        return Account::all();
    }


    public function getAccountsWithoutTrashed()
    {
        return Account::withoutTrashed()->get();
    }

    /**
     * Set the specified account as default while removing the previous default.
     *
     * @param int $accountId The ID of the account to be set as default.
     * @throws ModelNotFoundException If the account is not found.
     * @return string Success message.
     */
    public function makeDefault(int $accountId): string
    {
        return DB::transaction(function () use ($accountId) {
            // Remove the current default account
            Account::where('is_default', true)->update(['is_default' => false]);

            // Find the new account and set it as default
            $account = Account::find($accountId);

            if (!$account) {
                throw new ModelNotFoundException("Account with ID {$accountId} not found.");
            }

            $account->update(['is_default' => true]);

            return 'Account set as default successfully.';
        });
    }

    /**
     * Create a new bank account with proper validation and error handling.
     *
     * @param AccountDTO $accountDTO
     * @return Account
     * @throws AccountCreationException
     */
    public function createAccount(AccountDTO $accountDTO): Account
    {
        try {
            return DB::transaction(function () use ($accountDTO) {
                $isFirstAccount = !Account::exists(); // تحقق سريع للوجود بدلاً من `all()`
                return Account::create([
                    'account_no' => $accountDTO->account_no,
                    'name' => $accountDTO->name,
                    'initial_balance' => $accountDTO->initial_balance,
                    'total_balance' => $accountDTO->initial_balance, // تعيين الرصيد الكلي كبداية
                    'note' => $accountDTO->note,
                    'is_default' => $isFirstAccount ? 1 : 0, // تحديد الحساب الافتراضي
                ]);
            });
        } catch (\Exception $e) {
            throw new AccountCreationException('Failed to create account: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing account.
     */
    public function updateAccount(AccountDTO $accountDTO): Account
    {
        try {
            // Fetch account (Single Query Optimization)
            $account = Account::findOrFail($accountDTO->accountId);

            // Prepare data for update
            $updateData = [
                'account_no' => $accountDTO->account_no,
                'name' => $accountDTO->name,
                'initial_balance' => $accountDTO->initial_balance,
                'total_balance' => $accountDTO->initial_balance ?? 0,
                'note' => $accountDTO->note,
            ];

            // Update account
            $account->update($updateData);

            return $account;
        } catch (\Exception $e) {
            Log::error('Account Update Failed: ' . $e->getMessage());
            throw new \Exception('An error occurred while updating the account. Please try again.');
        }
    }

    /**
     * Handle account deletion with validation and exception handling.
     *
     * @param int $accountId
     * @throws AccountDeletionException
     */
    public function deleteAccount(int $accountId): void
    {
        DB::transaction(function () use ($accountId) {
            $account = Account::findOrFail($accountId);

            // Prevent deleting a default account
            if ($account->is_default) {
                throw new AccountDeletionException('Please make another account default first!');
            }

            // Soft delete (if `softDelete` is enabled, otherwise deactivate it)
            $account->delete();
        });
    }

    /**
     * Retrieves and calculates the balance sheet data for all accounts, including both debit and credit values.
     *
     * This function performs the following steps:
     * 1. Retrieves a list of accounts (excluding trashed accounts) using the `getAccountsWithoutTrashed` method.
     * 2. Extracts the account IDs from the accounts list for use in fetching additional data.
     * 3. Calls the repository methods to fetch balance data and transfer data for the given account IDs.
     * 4. Iterates through each account's balance and applies a strategy to calculate the credit and debit for each account.
     * 5. Returns the final balance sheet data, including the account list, debit amounts, and credit amounts.
     *
     * The balance sheet data is returned as a `BalanceSheetDataDTO` object, which contains the following:
     * - `accounts`: A collection of accounts (excluding trashed accounts).
     * - `debit`: An array containing the debit values for each account.
     * - `credit`: An array containing the credit values for each account.
     *
     * @return BalanceSheetDataDTO The balance sheet data containing accounts, debit, and credit amounts.
     * @throws RuntimeException If an error occurs during the calculation process, an exception is logged, and a RuntimeException is thrown.
     */
    public function getBalanceSheet(): BalanceSheetDataDTO
    {
        try {
            // Retrieve accounts without trashed ones
            $accounts = $this->getAccountsWithoutTrashed();

            // Get the list of account IDs
            $accountIds = $accounts->pluck('id')->toArray();

            // Fetch the balances and transfers for the given account IDs
            $balances = $this->repository->getBalances($accountIds);
            $transfers = $this->repository->getTransfers($accountIds);

            // Initialize empty arrays to store debit and credit values
            $debit = [];
            $credit = [];

            // Loop through each balance and calculate debit/credit using a strategy
            foreach ($balances as $balance) {
                // Get the appropriate strategy for balance sheet calculations
                $strategy = BalanceSheetStrategyFactory::getStrategy();

                // Calculate debit and credit based on the strategy and transfer data
                $result = $strategy->calculate($transfers, $balance);

                // Store the calculated debit and credit values
                $credit[] = $result['credit'];
                $debit[]  = $result['debit'];
            }

            // Return the balance sheet data as a DTO
            return new BalanceSheetDataDTO($accounts, $debit, $credit);

        } catch (Exception $e) {
            // Log the error and throw an exception if any issues occur
            Log::error('BalanceSheetService Error: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('An error occurred while calculating the balance sheet');
        }
    }

    /**
     * Retrieve the account statement data.
     *
     * This method fetches an account's details, retrieves all financial transactions
     * associated with the account, calculates the balance, and returns the necessary data.
     *
     * @param array $data An array containing the account ID and filtering parameters.
     * @return array Returns an array containing account details, transactions, and balance.
     * @throws \Exception If the account is not found or an error occurs during processing.
     */
    #[ArrayShape(['account' => "mixed", 'transactions' => "mixed", 'balance' => "int"])]
    public function getAccountStatement(array $data): array
    {
        try {
            // Retrieve the account by ID or throw an exception if not found
            $account = Account::findOrFail($data['account_id']);

            // Retrieve all transactions for the specified account based on filters
            $transactions = $this->transactionRepository->getTransactionsByType($data);

            // Calculate the account balance using the balance calculator service
            $transactions = $this->balanceCalculator->calculateBalance($transactions, $account->id);

            // Return account details, transactions, and the final calculated balance
            return [
                'account' => $account,
                'transactions' => $transactions,
                'balance' => $this->balanceCalculator->getBalance(),
            ];
        } catch (\Exception $e) {
            // Log the error and throw an exception with a user-friendly message
            Log::error('Error fetching account statement: ' . $e->getMessage());
            throw new \Exception('Unable to fetch account statement at this time.');
        }
    }






}

