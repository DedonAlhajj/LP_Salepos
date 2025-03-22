<?php

namespace App\Services\Tenant;


use App\Models\Table;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TableService
{

    /**
     * Retrieve all active table records from the cache or the database.
     *
     * @return Collection A collection of table records that are not trashed.
     * @throws \Exception If an error occurs while fetching the data.
     *
     * This method uses caching to minimize database queries. It stores the retrieved table records
     * in the cache for 60 seconds. If an error occurs during data retrieval, it logs the error and
     * throws an exception to inform the caller.
     */
    public function getActiveTable(): Collection
    {
        try {
            return Cache::remember("Table_all", 60, function () {
                return Table::withoutTrashed()->get();
            });
        } catch (Exception $e) {
            Log::error("Error fetching modifications (Table): " . $e->getMessage());
            throw new \Exception("An error occurred while fetching the modification data (Table)..");
        }
    }

    /**
     * Store a new table record in the database and clear the related cache.
     *
     * @param array $data The data used to create a new table record.
     * @throws \Exception If an error occurs while saving the data.
     *
     * This method creates a new table record in the database. After the record is saved,
     * it invalidates the cache to ensure that outdated data is not used in subsequent queries.
     * If an error occurs during this process, it logs the error and throws an exception.
     */
    public function storeTable(array $data)
    {
        try {
            Table::create($data);

            Cache::forget('Table_all');

        } catch (\Exception $e) {
            Log::error('An error occurred while saving data table.: ' . $e->getMessage());
            throw new Exception('An error occurred while saving data table.');
        }
    }

    /**
     * Update an existing table record and clear the related cache.
     *
     * @param array $request An associative array containing updated table data.
     * @throws \Exception If an error occurs during the update process.
     * @throws ModelNotFoundException If the specified table record does not exist.
     *
     * This method updates a table record identified by the `table_id` provided in the request array.
     * After updating the record, it clears the cache to ensure consistent data is displayed.
     * If the table record does not exist or an error occurs, appropriate exceptions are logged and thrown.
     */
    public function updateTable(array $request)
    {

        try {

            Table::findOrFail($request['table_id'])->update($request);

            Cache::forget('Table_all');

        } catch (ModelNotFoundException $e) {
            Log::error('Table not found: ' . $e->getMessage());
            throw new Exception('Table not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('An error occurred while updating data.: ' . $e->getMessage());
            throw new Exception('An error occurred while updating data table.');
        }
    }

    /**
     * Delete a specific table record by its ID and clear the cache.
     *
     * @param int $id The ID of the table record to delete.
     * @throws ModelNotFoundException If the table record is not found.
     * @throws \Exception If an error occurs during the deletion process.
     *
     * This method finds a table record by its ID and deletes it from the database.
     * After the deletion, it clears the relevant cache to ensure data consistency.
     * If the specified table record is not found, a ModelNotFoundException is logged and thrown.
     * For any other errors during the process, the error is logged, and a general exception is thrown to notify the caller.
     */
    public function destroy(int $id)
    {
        try {
            // Find the table record by ID or throw a ModelNotFoundException
            Table::findOrFail($id)->delete();

            // Clear the cached data related to the tables
            Cache::forget('Table_all');
        } catch (ModelNotFoundException $e) {
            Log::error('Table not found: ' . $e->getMessage());
            throw new Exception('Table not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error deleting table: ' . $e->getMessage());
            throw new Exception('An error occurred while deleting data table.');
        }
    }


}
