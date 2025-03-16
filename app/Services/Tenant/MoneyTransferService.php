<?php

namespace App\Services\Tenant;

use App\Models\MoneyTransfer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MoneyTransferService
{
    protected AccountService $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }


    public function getMoneyTransfer()
    {
        return MoneyTransfer::all();
    }

    public function index()
    {
        return [
            'money_transfer_all' => $this->getMoneyTransfer(),
            'account_list' => $this->accountService->getAccountsWithoutTrashed(),
        ];
    }

    /**
     * Generate a unique reference number for the transaction.
     *
     * The reference number includes the current date and time, ensuring it's unique for every transaction.
     *
     * @return string The generated reference number.
     */
    public function generateReferenceNo()
    {
        return 'mtr-' . date("Ymd") . '-' . date("his");
    }

    /**
     * Store the money transfer data in the database.
     *
     * @param array $data The validated transfer data.
     * @throws \Exception If there is an error during the transaction.
     */
    public function storeTransfer(array $data)
    {
        // Start the transaction to ensure data integrity
        DB::beginTransaction();

        try {
            // Create the money transfer record in the database
            MoneyTransfer::create($data);

            // Commit the transaction to save changes
            DB::commit();
        } catch (\Exception $e) {
            // Rollback if something goes wrong
            DB::rollBack();
            throw $e;  // Rethrow the exception to be handled in the controller
        }
    }

    /**
     * Update the money transfer record in the database.
     *
     * This method attempts to find the money transfer by its ID, and updates it with the
     * provided data. It includes validation to ensure the "from_account_id" is not the same
     * as the "to_account_id". If the transfer is found, the record is updated and saved.
     * If any error occurs, an exception is thrown and logged.
     *
     * @param array $data The data to update the transfer with.
     * @return bool True if the transfer was updated successfully, false otherwise.
     * @throws \Exception If the transfer does not exist or if any other error occurs.
     */
    public function updateTransfer(array $data): bool
    {
        try {
            // Check if the transfer exists by its ID
            $transfer = MoneyTransfer::findOrFail($data['id']);

            // Ensure that the "from_account_id" is not the same as "to_account_id"
            if ($data['from_account_id'] == $data['to_account_id']) {
                throw new \Exception('The from_account_id cannot be the same as to_account_id.');
            }

            // Update the transfer record with the new data
            $transfer->update($data);

            // Return true if the update was successful
            return true;
        } catch (ModelNotFoundException $e) {
            // If the transfer was not found, log the error and rethrow the exception
            Log::error('Money transfer not found: ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            // If any other error occurs, log it and rethrow the exception
            Log::error('Error updating money transfer: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a money transfer record from the database.
     *
     * This method attempts to find the transfer by its ID and deletes it. If the transfer is
     * not found or if there is an error during the deletion process, an exception is thrown
     * and logged.
     *
     * @param int $id The ID of the money transfer to delete.
     * @return bool True if the transfer was deleted successfully, false otherwise.
     * @throws \Exception If the transfer does not exist or if any other error occurs.
     */
    public function deleteTransfer(int $id): bool
    {
        try {
            // Attempt to find and delete the transfer by its ID
            MoneyTransfer::findOrFail($id)->delete();

            // Return true if the deletion was successful
            return true;
        } catch (ModelNotFoundException $e) {
            // If the transfer was not found, log the error and rethrow the exception
            Log::error('Money transfer not found: ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            // If any other error occurs, log it and rethrow the exception
            Log::error('Error deleting money transfer: ' . $e->getMessage());
            throw $e;
        }
    }




}

