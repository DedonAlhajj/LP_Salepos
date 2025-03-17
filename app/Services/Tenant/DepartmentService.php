<?php

namespace App\Services\Tenant;

use App\Models\Department;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartmentService
{

    /**
     * Retrieve all active departments that are not soft deleted.
     *
     * This method fetches all departments that have not been soft deleted
     * using the `withoutTrashed()` method.
     *
     * @return array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     * A collection of active departments.
     */
    public function getActiveDepartment(): array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
    {
        return Department::withoutTrashed()->get();
    }

    /**
     * Store a new department in the database.
     *
     * This method starts a database transaction to ensure data integrity.
     * It attempts to create a new department record and commits the transaction
     * upon success. If an error occurs, the transaction is rolled back to prevent
     * partial data insertion.
     *
     * @param array $data The data required to create a new department.
     * @throws \Exception If an error occurs, it is caught and rethrown.
     */
    public function storeDepartment(array $data)
    {
        // Start the transaction to ensure data integrity
        DB::beginTransaction();

        try {
            // Create the Department record in the database
            Department::create($data);

            // Commit the transaction to save changes
            DB::commit();
        } catch (\Exception $e) {
            // Rollback if something goes wrong
            DB::rollBack();
            throw $e;  // Rethrow the exception to be handled in the controller
        }
    }

    /**
     * Update an existing department record.
     *
     * This method attempts to find a department by its ID, and if found, updates
     * its attributes. If the department does not exist, it throws a
     * `ModelNotFoundException`. Any unexpected error is also caught and logged.
     *
     * @param array $data The data used to update the department (must include department_id).
     * @return bool Returns true if the update was successful.
     * @throws ModelNotFoundException If the department does not exist.
     * @throws \Exception For any other unexpected errors.
     */
    public function updateDepartment(array $data): bool
    {
        try {
            // Check if the Department exists by its ID
            $department = Department::findOrFail($data['department_id']);

            // Update the Department record with the new data
            $department->update($data);

            // Return true if the update was successful
            return true;
        } catch (ModelNotFoundException $e) {
            // If the Department was not found, log the error and rethrow the exception
            Log::error('Department not found: ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            // If any other error occurs, log it and rethrow the exception
            Log::error('Error updating Department: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete multiple departments by their IDs.
     *
     * This method attempts to delete multiple departments based on the provided
     * array of department IDs. If a department is not found, an exception is
     * thrown and logged. Any other errors are also logged.
     *
     * @param array $departmentId An array of department IDs to be deleted.
     * @return bool Returns true if deletion was successful.
     * @throws ModelNotFoundException If any department is not found.
     * @throws \Exception For any other unexpected errors.
     */
    public function deleteDepartments(array $departmentId): bool
    {
        try {
            // Attempt to find and delete the Department by its ID
            Department::whereIn('id', $departmentId)->delete();

            // Return true if the deletion was successful
            return true;
        } catch (ModelNotFoundException $e) {
            // If the Department was not found, log the error and rethrow the exception
            Log::error('Department not found: ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            // If any other error occurs, log it and rethrow the exception
            Log::error('Error deleting Department: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a single department by its ID.
     *
     * This method attempts to find a department by its ID and delete it.
     * If the department is not found, a `ModelNotFoundException` is thrown.
     * Any other unexpected errors are logged.
     *
     * @param int $id The ID of the department to delete.
     * @return bool Returns true if deletion was successful.
     * @throws ModelNotFoundException If the department does not exist.
     * @throws \Exception For any other unexpected errors.
     */
    public function deleteDepartment(int $id): bool
    {
        try {
            // Attempt to find and delete the Department by its ID
            Department::findOrFail($id)->delete();

            // Return true if the deletion was successful
            return true;
        } catch (ModelNotFoundException $e) {
            // If the Department was not found, log the error and rethrow the exception
            Log::error('Department not found: ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            // If any other error occurs, log it and rethrow the exception
            Log::error('Error deleting Department: ' . $e->getMessage());
            throw $e;
        }
    }
}

