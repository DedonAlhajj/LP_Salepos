<?php

namespace App\Services\Tenant;


use App\Models\CustomerGroup;
use App\Models\Table;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CustomerGroupService
{

    /**
     * Retrieve all active customer groups from the cache or the database.
     *
     * @return Collection A collection of customer group records that are not trashed.
     * @throws \Exception If any error occurs during the retrieval process.
     *
     * This function uses caching to reduce database queries. It attempts to fetch
     * active customer groups and stores the result in the cache for 60 seconds.
     * In case of failure, it logs the error and throws an exception.
     */
    public function getActiveCustomerGroup(): Collection
    {
        try {
            return Cache::remember("CustomerGroup_all", 60, function () {
                return CustomerGroup::withoutTrashed()->get();
            });
        } catch (Exception $e) {
            Log::error("Error fetching modifications (CustomerGroup): " . $e->getMessage());
            throw new \Exception("An error occurred while fetching the modification data (CustomerGroup)..");
        }
    }

    /**
     * Store a new customer group record in the database and clear the cache.
     *
     * @param array $data The data used to create a new customer group record.
     * @throws \Exception If any error occurs during the creation process.
     *
     * This function creates a new customer group in the database and ensures
     * that cached data related to customer groups is invalidated after insertion.
     */
    public function storeCustomerGroup(array $data)
    {
        try {
            CustomerGroup::create($data);

            Cache::forget('CustomerGroup_all');

        } catch (\Exception $e) {
            Log::error('An error occurred while saving data CustomerGroup.: ' . $e->getMessage());
            throw new Exception('An error occurred while saving data CustomerGroup.');
        }
    }

    /**
     * Find a customer group record by its ID.
     *
     * @param int $id The ID of the customer group to retrieve.
     * @return CustomerGroup The customer group record.
     * @throws ModelNotFoundException If the customer group is not found.
     *
     * This function retrieves a customer group record by ID. If the record doesn't
     * exist, it logs the error and throws a ModelNotFoundException.
     */
    public function edit(int $id): CustomerGroup
    {
        try {

            return CustomerGroup::findOrFail($id);

        } catch (ModelNotFoundException $e) {
            Log::error('CustomerGroup not found: ' . $e->getMessage());
            throw new ModelNotFoundException('CustomerGroup not found: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing customer group record and clear the cache.
     *
     * @param array $request An associative array containing updated data.
     * @throws \Exception If any error occurs during the update process.
     *
     * This function updates the specified customer group record in the database.
     * After updating, it invalidates the cache to ensure data consistency.
     */
    public function updateCustomerGroup(array $request)
    {

        try {

            CustomerGroup::findOrFail($request['customer_group_id'])->update($request);

            Cache::forget('CustomerGroup_all');

        } catch (ModelNotFoundException $e) {
            Log::error('CustomerGroup not found: ' . $e->getMessage());
            throw new Exception('CustomerGroup not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('An error occurred while updating data CustomerGroup.: ' . $e->getMessage());
            throw new Exception('An error occurred while updating data CustomerGroup.');
        }
    }

    /**
     * Delete multiple customer group records by their IDs and clear the cache.
     *
     * @param array $CustomerGroupIds An array of IDs of customer groups to delete.
     * @return bool Returns true if the deletion is successful.
     * @throws \Exception If any error occurs during the deletion process.
     *
     * This function deletes customer group records by their IDs. It ensures
     * cached data is invalidated after successful deletion.
     */
    public function deleteCustomerGroup(array $CustomerGroupIds): bool
    {
        try {
            // Delete CustomerGroup from the database
            CustomerGroup::whereIn('id', $CustomerGroupIds)->delete();

            // Clear cache after deletion
            Cache::forget('CustomerGroup_all');

            return true;
        } catch (ModelNotFoundException $e) {
            Log::error('CustomerGroup not found: ' . $e->getMessage());
            throw new Exception('CustomerGroup not found: ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error('Error deleting CustomerGroup: ' . $e->getMessage());
            throw new Exception('An error occurred while deleting data CustomerGroup.');
        }
    }

    /**
     * Delete a single customer group record by its ID and clear the cache.
     *
     * @param int $id The ID of the customer group to delete.
     * @throws \Exception If any error occurs during the deletion process.
     *
     * This function deletes a specific customer group record by ID. It also
     * clears the cached data to maintain data integrity.
     */
    public function destroy(int $id)
    {
        try {
            CustomerGroup::findOrFail($id)->delete();

            Cache::forget('CustomerGroup_all');
        } catch (ModelNotFoundException $e) {
            Log::error('CustomerGroup not found: ' . $e->getMessage());
            throw new Exception('CustomerGroup not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error deleting CustomerGroup: ' . $e->getMessage());
            throw new Exception('An error occurred while deleting data CustomerGroup.');
        }
    }

}
