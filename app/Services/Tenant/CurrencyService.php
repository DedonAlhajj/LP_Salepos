<?php

namespace App\Services\Tenant;

use App\Models\Currency;
use App\Models\GeneralSetting;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CurrencyService
{

    /**
     * Retrieve all active Currency from the cache or the database.
     *
     * @throws Exception If any error occurs during the retrieval process.
     *
     * This function uses caching to reduce database queries. It attempts to fetch
     * active Currency and stores the result in the cache for 60 seconds.
     * In case of failure, it logs the error and throws an exception.
     */
    public function getCurrencies()
    {
        try {
            return Cache::remember("Currency_all", 60, function () {
                return Currency::withoutTrashed()->get();
            });
        } catch (Exception $e) {
            Log::error("Error fetching modifications (Currency): " . $e->getMessage());
            throw new Exception("An error occurred while fetching the modification data (Currency)..");
        }
    }

    /**
     * Handles the creation of a Currency.
     *
     * This function manages the creation process, including saving Currency details in the database
     * and uploading the associated image, if provided. It also clears the related cache to ensure
     * data consistency. All operations are wrapped within a database transaction to maintain integrity.
     *
     * @param array $data Data containing Currency details (e.g., name, image).
     * @return mixed Returns the created Currency object.
     * @throws Exception If any error occurs during Currency creation, an exception is thrown.
     */
    public function createCurrency(array $data): mixed
    {
        try {
            return DB::transaction(function () use ($data) {
                // Create the Currency in the database
                Currency::create($data);

                // Clear the cache for all Currencys to ensure fresh data
                Cache::forget('Currency_all');
            });
        } catch (Exception $e) {
            // Log the error and throw a new exception with a meaningful message
            Log::error("Error creating (Currency): " . $e->getMessage());
            throw new Exception('Failed to create Currency: ' . $e->getMessage());
        }
    }

    /**
     * Handles updating an existing Currency.
     *
     * This function retrieves and updates a Currency's details in the database. If a new image is
     * provided, it replaces the existing one. The process includes clearing the relevant cache
     * for consistency and is wrapped within a transaction to maintain data integrity.
     *
     * @param array $data Data containing updated Currency details (e.g., name, image, Currency_id).
     * @return mixed Returns the updated Currency object.
     * @throws ModelNotFoundException If the Currency is not found.
     * @throws Exception If any other error occurs during the update process.
     */
    public function updateCurrency(array $data): mixed
    {
        try {
            return DB::transaction(function () use ($data) {

                if($data['exchange_rate'] == 1) {
                    GeneralSetting::latest()->first()->update(['currency' => $data['currency_id']]);
                }
                // Fetch the Currency from the database (ensuring it exists)
                $Currency = Currency::findOrFail($data['currency_id']);

                // Update the Currency with new data
                $Currency->update($data);

                // Clear the cache for all Currency's to ensure fresh data
                Cache::forget('Currency_all');
            });
        } catch (ModelNotFoundException $e) {
            // Log the error for missing Currency and throw a meaningful exception
            Log::error('Currency not found: ' . $e->getMessage());
            throw new \Exception('Currency not found');
        } catch (\Exception $e) {
            // Log any other error and throw an exception with a meaningful message
            Log::error('Currency update failed: ' . $e->getMessage());
            throw new \Exception('An error occurred while updating the Currency. Please try again.');
        }
    }

    /**
     * Delete a single Currency record by its ID and clear the cache.
     *
     * @param int $id The ID of the Currency to delete.
     * @throws \Exception If any error occurs during the deletion process.
     *
     * This function deletes a specific Currency record by ID. It also
     * clears the cached data to maintain data integrity.
     */
    public function destroy(int $id)
    {
        try {
            // Delete Currency from the database
            Currency::findOrFail($id)->delete();

            Cache::forget('Currency_all');
        } catch (ModelNotFoundException $e) {
            Log::error('Currency not found: ' . $e->getMessage());
            throw new Exception('Currency not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error deleting Currency: ' . $e->getMessage());
            throw new Exception('An error occurred while deleting data Currency.');
        }
    }

}
