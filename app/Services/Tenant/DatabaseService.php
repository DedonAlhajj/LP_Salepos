<?php

namespace App\Services\Tenant;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class DatabaseService
{
    /**
     * List of database tables that should not be truncated when resetting the database.
     *
     * These tables contain critical system data that should remain intact even after a database reset.
     *
     * @var array
     */
    protected array $excludedTables = [
        'accounts', 'general_settings', 'hrm_settings', 'languages', 'migrations',
        'password_resets', 'permissions', 'pos_setting', 'roles', 'role_has_permissions',
        'users', 'currencies', 'reward_point_settings', 'ecommerce_settings', 'external_services'
    ];

    /**
     * Resets the database by truncating all tables except the excluded ones.
     *
     * This method:
     * 1. Clears the cache to remove any stored data.
     * 2. Retrieves all database tables.
     * 3. Filters out tables that should not be truncated.
     * 4. Begins a database transaction to ensure atomicity.
     * 5. Truncates all non-excluded tables.
     * 6. Commits the transaction if successful; otherwise, rolls back in case of failure.
     *
     * @return bool Returns true if the reset is successful, otherwise false.
     */
    public function resetDatabase(): bool
    {
        try {
            // Clear the cache before resetting the database
            $this->clearCache();

            // Retrieve all table names from the database
            $tables = DB::select('SHOW TABLES');
            $databaseName = env('DB_DATABASE');
            $tableKey = 'Tables_in_' . $databaseName;

            // Filter out tables that are in the excluded list
            $tablesToTruncate = array_filter($tables, function ($table) use ($tableKey) {
                return !in_array($table->$tableKey, $this->excludedTables);
            });

            // Begin a transaction to ensure all operations are atomic
            DB::beginTransaction();

            // Truncate each table that is not excluded
            foreach ($tablesToTruncate as $table) {
                DB::table($table->$tableKey)->truncate();
            }

            // Commit the transaction after successful truncation
            DB::commit();
            return true;
        } catch (Exception $e) {
            // Roll back the transaction in case of failure
            DB::rollBack();

            // Log the error message for debugging
            Log::error("Database reset failed: " . $e->getMessage());

            return false;
        }
    }

    /**
     * Clears all cached data.
     *
     * This method flushes the application cache to ensure no outdated data remains
     * after performing a database reset.
     *
     * @return void
     */
    private function clearCache(): void
    {
        Cache::flush();
    }

}
