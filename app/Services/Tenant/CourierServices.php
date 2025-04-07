<?php

namespace App\Services\Tenant;

use App\Models\Courier;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourierServices
{

    public function getCourier()
    {
        try {
            return Cache::remember("Courier_all", 60, function () {
                return Courier::withoutTrashed()->get();
            });
        } catch (Exception $e) {
            Log::error("Error fetching modifications (Courier): " . $e->getMessage());
            throw new Exception("An error occurred while fetching the modification data (Courier)..");
        }
    }

    /**
     * Create a new courier entry in the database.
     *
     * This method ensures data integrity using a database transaction and clears
     * the cached courier data upon successful creation.
     *
     * @param array $data Data Transfer Object containing courier details.
     * @return mixed Result of the transaction operation.
     * @throws Exception If courier creation fails.
     */
    public function createCourier(array $data): mixed
    {
        try {
            return DB::transaction(function () use ($data) {
                // Persist the courier data in the database
                Courier::create($data);

                // Clear cached courier data to refresh with updated records
                Cache::forget('Courier_all');
            });
        } catch (Exception $e) {
            // Log error for debugging purposes
            Log::error("Failed to create courier: " . $e->getMessage());

            // Throw an exception to notify the caller
            throw new Exception('Failed to create courier, please try again');
        }
    }

    /**
     * Update an existing courier record in the database.
     *
     * This method retrieves the courier by its ID, updates the details, and clears
     * the cached courier data to ensure updated records.
     *
     * @param array $data Data Transfer Object containing updated courier details.
     * @throws Exception If the courier is not found or update fails.
     */
    public function updateCourier(array $data)
    {
        try {
            return DB::transaction(function () use ($data) {
                // Retrieve the courier by ID or fail if not found
                $courier = Courier::findOrFail($data['id']);


                // Update courier details
                $courier->update($data);

                // Clear cached courier data to refresh with updated records
                Cache::forget('Courier_all');
            });
        } catch (ModelNotFoundException $e) {
            // Log error indicating the courier does not exist
            Log::error('courier not found: ' . $e->getMessage());

            // Throw an exception with detailed information
            throw new Exception("courier not found with ID: {$data['id']}");
        } catch (Exception $e) {
            // Log any unexpected error during the update process
            Log::error('Error Updating courier: ' . $e->getMessage());

            // Throw a generic exception for error handling
            throw new Exception("Something went wrong while updating the courier");
        }
    }

    /**
     * Delete a single courier by its ID.
     *
     * This method retrieves the courier and deletes it. If the card is not found,
     * an exception is thrown.
     *
     * @param int $id The ID of the courier to be deleted.
     * @throws Exception If the courier is not found or deletion fails.
     */
    public function destroy(int $id)
    {
        try {
            // Retrieve and delete the courier
            Courier::findOrFail($id)->delete();

            Cache::forget('Courier_all');

        } catch (ModelNotFoundException $e) {
            // Log error when the courier is not found
            Log::error('courier not found: ' . $e->getMessage());

            // Throw exception to notify the caller
            throw new Exception('courier not found: ' . $e->getMessage());
        } catch (Exception $e) {
            // Log general error related to deletion
            Log::error('Error deleting courier: ' . $e->getMessage());

            // Throw generic exception for handling failure
            throw new Exception('An error occurred while deleting the courier.');
        }
    }
}
