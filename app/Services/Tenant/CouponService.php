<?php

namespace App\Services\Tenant;

use App\DTOs\CouponDTO;
use App\DTOs\CouponUpdateDTO;
use App\Models\Coupon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CouponService
{

    /**
     * Retrieve all coupon data from the database.
     *
     * This method caches coupon data for 60 minutes to improve performance
     * and reduce database queries.
     *
     * @return Collection Collection of coupon records.
     * @throws Exception If data retrieval fails.
     */
    public function getIndexData(): Collection
    {
        try {
            return Cache::remember("Coupon_all", 60, function () {
                // Fetch all coupons ordered by descending ID
                return Coupon::orderBy('id', 'desc')->get();
            });
        } catch (Exception $e) {
            // Log error for debugging purposes
            Log::error("Error fetching modifications (Coupon): " . $e->getMessage());

            // Throw an exception to notify the caller
            throw new Exception("An error occurred while fetching the modification data.");
        }
    }

    /**
     * Create a new coupon entry in the database.
     *
     * This method ensures data integrity using a database transaction and clears
     * the cached coupon data upon successful creation.
     *
     * @param CouponDTO $dto Data Transfer Object containing coupon details.
     * @return mixed Result of the transaction operation.
     * @throws Exception If coupon creation fails.
     */
    public function createCoupon(CouponDTO $dto): mixed
    {
        try {
            return DB::transaction(function () use ($dto) {
                // Persist the coupon data in the database
                Coupon::create($dto->toArray());

                // Clear cached coupon data to refresh with updated records
                Cache::forget('Coupon_all');
            });
        } catch (Exception $e) {
            // Log error for debugging purposes
            Log::error("Failed to create Coupon: " . $e->getMessage());

            // Throw an exception to notify the caller
            throw new Exception('Failed to create Coupon, please try again');
        }
    }

    /**
     * Update an existing coupon record in the database.
     *
     * This method retrieves the coupon by its ID, updates the details, and clears
     * the cached coupon data to ensure updated records.
     *
     * @param CouponUpdateDTO $dto Data Transfer Object containing updated coupon details.
     * @throws Exception If the coupon is not found or update fails.
     */
    public function updateCoupon(CouponUpdateDTO $dto)
    {
        try {
            return DB::transaction(function () use ($dto) {
                // Retrieve the coupon by ID or fail if not found
                $coupon = Coupon::findOrFail($dto->coupon_id);

                // Update coupon details
                $coupon->update($dto->toArray());

                // Clear cached coupon data to refresh with updated records
                Cache::forget('Coupon_all');
            });
        } catch (ModelNotFoundException $e) {
            // Log error indicating the coupon does not exist
            Log::error('Coupon not found: ' . $e->getMessage());

            // Throw an exception with detailed information
            throw new Exception("Coupon not found with ID: {$dto->coupon_id}");
        } catch (Exception $e) {
            // Log any unexpected error during the update process
            Log::error('Error Updating Coupon: ' . $e->getMessage());

            // Throw a generic exception for error handling
            throw new Exception("Something went wrong while updating the Coupon");
        }
    }

    /**
     * Generate a unique numeric code for a Coupon.
     *
     * This method utilizes UUID, removes non-numeric characters,
     * and ensures a 10-digit unique code.
     *
     * @return string A unique 10-digit numeric code.
     * @throws Exception If code generation fails.
     */
    public function generateCode(): string
    {
        try {
            // Generate a UUID and remove dashes
            $uniqueNumber = str_replace('-', '', Str::uuid());

            // Keep only numeric characters from the UUID
            $uniqueNumber = preg_replace('/[^0-9]/', '', $uniqueNumber);

            // Return the first 10 digits to ensure a short unique code
            return substr($uniqueNumber, 0, 10);

        } catch (Exception $e) {
            // Handle any unexpected errors during code generation
            throw new Exception('Failed to generate Coupon card, please try again');
        }
    }

    /**
     * Delete multiple Coupons from the database.
     *
     * This method removes Coupons using their IDs. If a Coupon is not found,
     * an exception is thrown.
     *
     * @param array $CouponIds Array of Coupon IDs to be deleted.
     * @throws Exception If the Coupon is not found or deletion fails.
     */
    public function deleteCoupon(array $CouponIds)
    {
        try {
            // Delete Coupons matching the provided IDs
            Coupon::whereIn('id', $CouponIds)->delete();

            Cache::forget('Coupon_all');

        } catch (ModelNotFoundException $e) {
            // Log error when a Coupon is not found
            Log::error('Coupon not found: ' . $e->getMessage());

            // Throw exception for error handling
            throw new Exception('Coupon not found: ' . $e->getMessage());
        } catch (Exception $e) {
            // Log general deletion error
            Log::error('Error deleting Coupon: ' . $e->getMessage());

            // Throw exception for general failure
            throw new Exception('An error occurred while deleting the Coupon.');
        }
    }

    /**
     * Delete a single Coupon by its ID.
     *
     * This method retrieves the Coupon and deletes it. If the card is not found,
     * an exception is thrown.
     *
     * @param int $id The ID of the Coupon to be deleted.
     * @throws Exception If the Coupon is not found or deletion fails.
     */
    public function destroy(int $id)
    {
        try {
            // Retrieve and delete the Coupon
            Coupon::findOrFail($id)->delete();

            Cache::forget('Coupon_all');

        } catch (ModelNotFoundException $e) {
            // Log error when the Coupon is not found
            Log::error('Coupon not found: ' . $e->getMessage());

            // Throw exception to notify the caller
            throw new Exception('Coupon not found: ' . $e->getMessage());
        } catch (Exception $e) {
            // Log general error related to deletion
            Log::error('Error deleting Coupon: ' . $e->getMessage());

            // Throw generic exception for handling failure
            throw new Exception('An error occurred while deleting the Coupon.');
        }
    }

}
