<?php

namespace App\Repositories\Tenant;

use App\Models\Account;
use App\Models\MoneyTransfer;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BalanceSheetRepository
{


    /**
     * Retrieves account balances and aggregated financial data for a given set of account IDs.
     *
     * This function queries the `accounts` table and joins it with aggregated data from various financial transactions
     * (like received payments, sent payments, return sales, return purchases, expenses, and payrolls).
     * It then returns the final balance details for each account including initial balance and the aggregated values.
     *
     * @param array $accountIds Array of account IDs to fetch the balance data for.
     * @return \Illuminate\Support\Collection A collection of the queried account balances and financial data.
     */
    public function getBalances(array $accountIds):Collection
    {
        // Build the query to retrieve account balances along with aggregated financial data
        $query = DB::table('accounts')
            // Select relevant fields from the accounts table and the aggregated financial data
            ->selectRaw("
            accounts.id,
            accounts.initial_balance,
            COALESCE(aggregated.received, 0) as received,
            COALESCE(aggregated.sent, 0) as sent,
            COALESCE(aggregated.total, 0) as return_sale,
            COALESCE(aggregated.return_purchase, 0) as return_purchase,
            COALESCE(aggregated.expense, 0) as expense,
            COALESCE(aggregated.payroll, 0) as payroll
        ")
            // Perform a left join with the aggregated data from different financial transactions
            ->leftJoinSub($this->getAggregatedQueries($accountIds), 'aggregated', 'accounts.id', '=', 'aggregated.account_id')
            // Filter the results to only include the accounts specified in the given account IDs
            ->whereIn('accounts.id', $accountIds)
            // Execute the query and return the result as a collection
            ->get();

        // Return the query result, which contains the account balances and aggregated financial data
        return $query;
    }

    /**
     * Retrieves aggregated financial data for a given set of account IDs from various transaction tables.
     *
     * This function generates a raw SQL query that combines aggregated financial data from different tables
     * like payments, returns, return purchases, expenses, and payrolls. The query computes sums for various financial
     * metrics such as received, sent, return purchase, expense, and payroll, grouped by account ID.
     *
     * @param array $accountIds Array of account IDs to fetch the aggregated data for.
     * @return \Illuminate\Database\Query\Builder A raw SQL query object containing the aggregated financial data.
     */
    private function getAggregatedQueries(array $accountIds):Builder
    {
        // Build a raw SQL query that aggregates financial data from multiple transaction tables
        return DB::table(DB::raw("
    (
        -- Aggregates payments data: sums received and sent amounts for each account
        SELECT account_id, SUM(received) AS received, SUM(sent) AS sent, 0 AS total, 0 AS return_purchase, 0 AS expense, 0 AS payroll
        FROM (
            SELECT account_id,
            SUM(CASE WHEN sale_id IS NOT NULL THEN amount ELSE 0 END) AS received,
            SUM(CASE WHEN purchase_id IS NOT NULL THEN amount ELSE 0 END) AS sent
            FROM payments
            WHERE account_id IN (" . implode(',', $accountIds) . ")
            GROUP BY account_id
        ) payments_data
        GROUP BY account_id

        -- Aggregates returns data: sums grand_total for each account from the returns table
        UNION ALL
        SELECT account_id, 0, 0, SUM(grand_total), 0, 0, 0
        FROM returns
        WHERE account_id IN (" . implode(',', $accountIds) . ")
        GROUP BY account_id

        -- Aggregates return purchases data: sums grand_total for each account from the return_purchases table
        UNION ALL
        SELECT account_id, 0, 0, 0, SUM(grand_total), 0, 0
        FROM return_purchases
        WHERE account_id IN (" . implode(',', $accountIds) . ")
        GROUP BY account_id

        -- Aggregates expenses data: sums amount for each account from the expenses table
        UNION ALL
        SELECT account_id, 0, 0, 0, 0, SUM(amount), 0
        FROM expenses
        WHERE account_id IN (" . implode(',', $accountIds) . ")
        GROUP BY account_id

        -- Aggregates payroll data: sums amount for each account from the payrolls table
        UNION ALL
        SELECT account_id, 0, 0, 0, 0, 0, SUM(amount)
        FROM payrolls
        WHERE account_id IN (" . implode(',', $accountIds) . ")
        GROUP BY account_id
    ) AS aggregated_data
    "));
    }

    /**
     * Retrieves aggregated transfer data (both incoming and outgoing) for a given set of account IDs.
     *
     * This function performs two separate queries:
     * 1. It retrieves the total amount of money transferred out from the specified accounts (via the `from_account_id`).
     * 2. It retrieves the total amount of money transferred into the specified accounts (via the `to_account_id`).
     *
     * The results of these two queries are then combined using a `UNION ALL` to give a unified list of transfers.
     * Each entry will include the account ID, the total amount of the transfer, and a type indicating whether
     * the transfer was incoming ('in') or outgoing ('out').
     *
     * @param array $accountIds Array of account IDs to fetch transfer data for.
     * @return \Illuminate\Database\Eloquent\Collection A collection of transfer data, containing account ID, total amount, and type.
     */
    public function getTransfers(array $accountIds): \Illuminate\Database\Eloquent\Collection
    {
        // Query to get the total outgoing transfers for each account
        $transfersFrom = MoneyTransfer::whereIn('from_account_id', $accountIds)
            ->selectRaw("from_account_id AS account_id, SUM(amount) AS total, 'out' AS type")
            ->groupBy('from_account_id'); // Group by the source account

        // Query to get the total incoming transfers for each account
        $transfersTo = MoneyTransfer::whereIn('to_account_id', $accountIds)
            ->selectRaw("to_account_id AS account_id, SUM(amount) AS total, 'in' AS type")
            ->groupBy('to_account_id'); // Group by the target account

        // Combine the results of both queries using UNION ALL and return the result
        return $transfersFrom->unionAll($transfersTo)->get();
    }




}
