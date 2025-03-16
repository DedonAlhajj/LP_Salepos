<?php

namespace App\Services\Tenant;

use App\Models\Account;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Unit;

class CalculationsService
{
    private $balance = 0;

    public static function calculate($qty, Unit $unit)
    {
        return ($unit->operator == '*') ? $qty * $unit->operation_value : $qty / $unit->operation_value;
    }


    /**  Balance Calculator */

    /**
     * Calculate the balance for each transaction and update the transaction's credit, debit, and balance.
     *
     * This method processes each transaction, checks if it is a credit or debit transaction,
     * updates the transaction's reference number, and calculates the cumulative balance for the account.
     * It also tracks credit and debit amounts separately.
     *
     * @param \Illuminate\Support\Collection $transactions Collection of transactions to calculate balance for.
     * @param int $accountId The ID of the account to check for credits or debits.
     * @return \Illuminate\Support\Collection The updated collection of transactions with calculated balance, credit, and debit values.
     */
    public function calculateBalance($transactions, $accountId)
    {
        return $transactions->transform(function ($transaction) use ($accountId) {
            // Get the reference number for the transaction (either from Sale or Purchase)
            $transaction->transaction_reference = $this->getTransactionReference($transaction);

            // Initialize credit and debit amounts to 0 for each transaction
            $transaction->credit = 0;
            $transaction->debit = 0;

            // Check if the transaction is a credit or debit and update balance accordingly
            if ($this->isCreditTransaction($transaction, $accountId)) {
                $this->balance += $transaction->amount; // Increase balance for credit transaction
                $transaction->credit = $transaction->amount;
            } else {
                $this->balance -= $transaction->amount; // Decrease balance for debit transaction
                $transaction->debit = $transaction->amount;
            }

            // Set the updated balance for the transaction
            $transaction->balance = $this->balance;

            return $transaction;
        });
    }

    /**
     * Get the current balance.
     *
     * This method returns the cumulative balance calculated for the transactions.
     *
     * @return float The current balance.
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Get the transaction reference based on the sale or purchase ID.
     *
     * This method returns the reference number from either the associated sale or purchase.
     * It prioritizes the sale reference number if available, otherwise, it returns the purchase reference.
     * If neither is available, it returns null.
     *
     * @param object $transaction The transaction object to extract the reference number from.
     * @return string|null The transaction reference number, or null if not found.
     */
    private function getTransactionReference($transaction)
    {
        return Sale::where('id', $transaction->sale_id)->value('reference_no') ??
            Purchase::where('id', $transaction->purchase_id)->value('reference_no') ??
            null;
    }

    /**
     * Check if the transaction is a credit transaction.
     *
     * This method checks if the transaction should be classified as a credit based on predefined identifiers
     * or if the transaction's destination account matches the provided account ID.
     * The credit identifiers include 'spr', 'prr', and 'mtr', and transactions can also be credit
     * if the account ID matches the `to_account_id` field.
     *
     * @param object $transaction The transaction object to check.
     * @param int $accountId The account ID to check against the transaction's `to_account_id`.
     * @return bool True if the transaction is a credit transaction, false otherwise.
     */
    private function isCreditTransaction($transaction, $accountId)
    {
        $creditIdentifiers = ['spr', 'prr', 'mtr']; // Predefined credit identifiers
        return collect($creditIdentifiers)->some(fn($id) => str_contains($transaction->reference_no, $id)) ||
            (isset($transaction->to_account_id) && $transaction->to_account_id == $accountId);
    }


}

