<?php

namespace App\Services\Tenant;


use App\Actions\SendMailAction;
use App\Mail\CustomerDeposit;
use App\Models\Tax;
use App\Models\Deposit;
use App\Models\Customer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaxCalculatorService
{

    /**
     * Retrieve all active Tax from the cache or the database.
     *
     * @throws Exception If any error occurs during the retrieval process.
     *
     * This function uses caching to reduce database queries. It attempts to fetch
     * active Tax and stores the result in the cache for 60 seconds.
     * In case of failure, it logs the error and throws an exception.
     */
    public function getTaxes()
    {
        try {
            return Cache::remember("Tax_all", 60, function () {
                return Tax::withoutTrashed()->get();
            });
        } catch (Exception $e) {
            Log::error("Error fetching modifications (Tax): " . $e->getMessage());
            throw new Exception("An error occurred while fetching the modification data (Tax)..");
        }
    }

    /**
     * Handles the creation of a Tax.
     *
     * This function manages the creation process, including saving Tax details in the database
     * and uploading the associated image, if provided. It also clears the related cache to ensure
     * data consistency. All operations are wrapped within a database transaction to maintain integrity.
     *
     * @param array $data Data containing Tax details (e.g., name).
     * @return mixed Returns the created Tax object.
     * @throws Exception If any error occurs during Tax creation, an exception is thrown.
     */
    public function createTax(array $data): mixed
    {
        try {
            return DB::transaction(function () use ($data) {
                // Create the Tax in the database
                $Tax = Tax::create($data);

                // Clear the cache for all Taxes to ensure fresh data
                Cache::forget('Tax_all');
            });
        } catch (Exception $e) {
            // Log the error and throw a new exception with a meaningful message
            Log::error("Error creating (Tax): " . $e->getMessage());
            throw new Exception('Failed to create Tax: ' . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function edit($id)
    {
        try {
            return Tax::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Log::error('Tax not found (edit): ' . $e->getMessage());
            throw new \Exception('Tax not found');
        }
    }

    /**
     * Handles updating an existing Tax.
     *
     * This function retrieves and updates a Tax's details in the database. If a new image is
     * provided, it replaces the existing one. The process includes clearing the relevant cache
     * for consistency and is wrapped within a transaction to maintain data integrity.
     *
     * @param array $data Data containing updated Tax details (e.g., name, tax_id).
     * @return mixed Returns the updated Tax object.
     * @throws ModelNotFoundException If the Tax is not found.
     * @throws Exception If any other error occurs during the update process.
     */
    public function updateTax(array $data): mixed
    {
        try {
            return DB::transaction(function () use ($data) {
                // Update the Tax with new data
                Tax::findOrFail($data['tax_id'])->update($data);

                // Clear the cache for all Taxs to ensure fresh data
                Cache::forget('Tax_all');
            });
        } catch (ModelNotFoundException $e) {
            // Log the error for missing Tax and throw a meaningful exception
            Log::error('Tax not found: ' . $e->getMessage());
            throw new \Exception('Tax not found');
        } catch (\Exception $e) {
            // Log any other error and throw an exception with a meaningful message
            Log::error('Tax update failed: ' . $e->getMessage());
            throw new \Exception('An error occurred while updating the Tax. Please try again.');
        }
    }

    /**
     * Delete multiple Tax records by their IDs and clear the cache.
     *
     * @param array $TaxIds An array of IDs of Taxes to delete.
     * @return bool Returns true if the deletion is successful.
     * @throws \Exception If any error occurs during the deletion process.
     *
     * This function deletes Tax records by their IDs. It ensures
     * cached data is invalidated after successful deletion.
     */
    public function deleteTax(array $TaxIds): bool
    {
        try {
            // Delete Tax from the database
            Tax::whereIn('id', $TaxIds)->delete();

            // Clear cache after deletion
            Cache::forget('Tax_all');

            return true;
        } catch (ModelNotFoundException $e) {
            Log::error('Tax not found: ' . $e->getMessage());
            throw new Exception('Tax not found: ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error('Error deleting Tax: ' . $e->getMessage());
            throw new Exception('An error occurred while deleting data Tax.');
        }
    }

    /**
     * Delete a single Tax record by its ID and clear the cache.
     *
     * @param int $id The ID of the Tax to delete.
     * @throws \Exception If any error occurs during the deletion process.
     *
     * This function deletes a specific Tax record by ID. It also
     * clears the cached data to maintain data integrity.
     */
    public function destroy(int $id)
    {
        try {
            // delete Tax from the database
            $Tax = Tax::findOrFail($id)->delete();

            Cache::forget('Tax_all');
        } catch (ModelNotFoundException $e) {
            Log::error('Tax not found: ' . $e->getMessage());
            throw new Exception('Tax not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error deleting Tax: ' . $e->getMessage());
            throw new Exception('An error occurred while deleting data Tax.');
        }
    }

    public function getTaxById(int $tax_id): ?Tax
    {
        return Tax::find($tax_id);
    }

    public static function calculate($product, $stock)
    {
        $taxRate = 0.00;
        $taxAmount = 0.00;
        $cost = $product->cost * $stock;
        $netUnitCost = $product->cost;

        if ($product->tax_id) {
            $taxData = DB::table('taxes')->select('rate')->find($product->tax_id);
            $taxRate = $taxData->rate;

            if ($product->tax_method == 1) {
                $taxAmount = $product->cost * $stock * ($taxRate / 100);
                $cost = ($product->cost * $stock) + $taxAmount;
            } else {
                $netUnitCost = (100 / (100 + $taxRate)) * $product->cost;
                $taxAmount = ($product->cost - $netUnitCost) * $stock;
                $cost = $product->cost * $stock;
            }
        }

        // إرجاع القيم بعد تنسيقها
        return [
            'net_unit_cost' => number_format($netUnitCost, 2, '.', ''),
            'tax_rate' => $taxRate,
            'tax' => number_format($taxAmount, 2, '.', ''),
            'total_cost' => number_format($cost, 2, '.', ''),
        ];
    }

    public function getTaxesWhereIn(array $taxRates): Collection
    {
        return Tax::whereIn('rate', $taxRates)->get()->keyBy('rate');
    }


}
