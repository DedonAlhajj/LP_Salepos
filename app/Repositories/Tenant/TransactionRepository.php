<?php

namespace App\Repositories\Tenant;


use App\Models\{Payment, MoneyTransfer, ReturnPurchase, Expense, Returns, Payroll};
use Illuminate\Support\Collection;

class TransactionRepository
{
    /**
     * Retrieve financial transactions based on the transaction type.
     *
     * This method fetches transactions related to a specific account based on the requested type:
     * - Type `0`: Retrieves both credit and debit transactions.
     * - Type `1`: Retrieves only debit transactions.
     * - Type `2`: Retrieves only credit transactions.
     *
     * The transactions are fetched using the `getTransactions` method and then sorted by creation date.
     *
     * @param array $data An array containing 'account_id', 'type', 'start_date', and 'end_date'.
     * @return \Illuminate\Support\Collection A collection of transactions sorted in descending order by creation date.
     */
    public function getTransactionsByType(array $data): Collection
    {
        // Initialize an empty collection for transactions
        $query = collect();

        // Fetch credit transactions if the type is '0' (both) or '2' (credit only)
        if ($data['type'] == '0' || $data['type'] == '2') {
            $query = $query->concat($this->getTransactions($data, 'credit'));
        }

        // Fetch debit transactions if the type is '0' (both) or '1' (debit only)
        if ($data['type'] == '0' || $data['type'] == '1') {
            $query = $query->concat($this->getTransactions($data, 'debit'));
        }

        // Return the transactions sorted in descending order by creation date
        return $query->sortByDesc('created_at');
    }


    /**
     * Retrieve account transactions of a specific type (credit or debit).
     *
     * This method retrieves transactions from various financial tables based on the transaction type.
     * - **Credit Transactions**: Payments (for sales), money transfers (incoming), and return purchases.
     * - **Debit Transactions**: Payments (for purchases), expenses, returns, and payroll transactions.
     *
     * The retrieved transactions are filtered by the account ID and the specified date range.
     *
     * @param array $data An array containing 'account_id', 'start_date', and 'end_date'.
     * @param string $type The type of transaction ('credit' or 'debit').
     * @return \Illuminate\Support\Collection A collection of filtered transactions.
     */
    private function getTransactions(array $data, string $type): Collection
    {
        $queries = [];

        if ($type === 'credit') {
            // Fetch payments related to sales (incoming money)
            $queries[] = Payment::whereNotNull('sale_id')
                ->where('account_id', $data['account_id'])
                ->selectRaw("IFNULL(payment_reference, id) as reference_no, sale_id, amount, created_at");

            // Fetch money transfers received into the account
            $queries[] = MoneyTransfer::where('to_account_id', $data['account_id'])
                ->select('reference_no', 'amount', 'created_at');

            // Fetch return purchases (money refunded to the account)
            $queries[] = ReturnPurchase::where('account_id', $data['account_id'])
                ->select('reference_no', 'grand_total as amount', 'created_at');

        } else { // Debit transactions
            // Fetch payments related to purchases (outgoing money)
            $queries[] = Payment::whereNotNull('purchase_id')
                ->where('account_id', $data['account_id'])
                ->selectRaw("IFNULL(payment_reference, id) as reference_no, purchase_id, amount, created_at");

            // Fetch expenses made from the account
            $queries[] = Expense::where('account_id', $data['account_id'])
                ->select('reference_no', 'amount', 'created_at');

            // Fetch returns (money paid back to customers)
            $queries[] = Returns::where('account_id', $data['account_id'])
                ->select('reference_no', 'grand_total as amount', 'created_at');

            // Fetch payroll transactions (salaries paid)
            $queries[] = Payroll::where('account_id', $data['account_id'])
                ->select('reference_no', 'amount', 'created_at');
        }

        // Filter transactions by the provided date range and sort them by creation date
        return collect(array_reduce($queries, function ($carry, $query) use ($data) {
            return $carry->concat(
                $query->whereBetween('created_at', [$data['start_date'], $data['end_date']])
                    ->orderBy('created_at', 'desc')
                    ->get()
            );
        }, collect()));
    }



}

