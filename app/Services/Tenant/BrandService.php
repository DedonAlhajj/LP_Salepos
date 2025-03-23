<?php

namespace App\Services\Tenant;

use App\DTOs\AccountDTO;
use App\DTOs\BalanceSheetDataDTO;
use App\Exceptions\AccountCreationException;
use App\Exceptions\AccountDeletionException;
use App\Models\Account;
use App\Models\Brand;
use App\Repositories\Tenant\BalanceSheetRepository;
use App\Repositories\Tenant\TransactionRepository;
use App\Services\Tenant\BalanceCalculationStrategy\BalanceSheetStrategyFactory;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;
use RuntimeException;

class BrandService
{

    private MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }


    /**
     * Retrieve all active brands from the cache or the database.
     *
     * @throws Exception If any error occurs during the retrieval process.
     *
     * This function uses caching to reduce database queries. It attempts to fetch
     * active brand and stores the result in the cache for 60 seconds.
     * In case of failure, it logs the error and throws an exception.
     */
    public function getBrandsWithoutTrashed()
    {
        try {
            return Cache::remember("brand_all", 60, function () {
                return Brand::withoutTrashed()->get();
            });
        } catch (Exception $e) {
            Log::error("Error fetching modifications (Brand): " . $e->getMessage());
            throw new Exception("An error occurred while fetching the modification data (Brand)..");
        }
    }

    /**
     * Handles the creation of a brand.
     *
     * This function manages the creation process, including saving brand details in the database
     * and uploading the associated image, if provided. It also clears the related cache to ensure
     * data consistency. All operations are wrapped within a database transaction to maintain integrity.
     *
     * @param array $data Data containing brand details (e.g., name, image).
     * @return mixed Returns the created brand object.
     * @throws Exception If any error occurs during brand creation, an exception is thrown.
     */
    public function createBrand(array $data): mixed
    {
        try {
            return DB::transaction(function () use ($data) {
                // Create the brand in the database
                $brand = Brand::create($data);

                // If an image is provided, upload and associate it with the brand
                if (isset($data['image'])) {
                    $this->mediaService->addDocument($brand, $data['image'], "brands");
                }

                // Clear the cache for all brands to ensure fresh data
                Cache::forget('brand_all');
            });
        } catch (Exception $e) {
            // Log the error and throw a new exception with a meaningful message
            Log::error("Error creating (Brand): " . $e->getMessage());
            throw new Exception('Failed to create brand: ' . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function edit($id)
    {
        try {
            return brand::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Log::error('brand not found (edit): ' . $e->getMessage());
            throw new \Exception('brand not found');
        }
    }

    /**
     * Handles updating an existing brand.
     *
     * This function retrieves and updates a brand's details in the database. If a new image is
     * provided, it replaces the existing one. The process includes clearing the relevant cache
     * for consistency and is wrapped within a transaction to maintain data integrity.
     *
     * @param array $data Data containing updated brand details (e.g., name, image, brand_id).
     * @return mixed Returns the updated brand object.
     * @throws ModelNotFoundException If the brand is not found.
     * @throws Exception If any other error occurs during the update process.
     */
    public function updateBrand(array $data): mixed
    {
        try {
            return DB::transaction(function () use ($data) {
                // Fetch the brand from the database (ensuring it exists)
                $brand = Brand::findOrFail($data['brand_id']);

                // If a new image is provided, remove the old one and upload the new image
                if (isset($data['image'])) {
                    $this->mediaService->uploadDocumentWithClear($brand, $data['image'], 'brands');
                }

                // Update the brand with new data
                $brand->update($data);

                // Clear the cache for all brands to ensure fresh data
                Cache::forget('brand_all');
            });
        } catch (ModelNotFoundException $e) {
            // Log the error for missing brand and throw a meaningful exception
            Log::error('Brand not found: ' . $e->getMessage());
            throw new \Exception('Brand not found');
        } catch (\Exception $e) {
            // Log any other error and throw an exception with a meaningful message
            Log::error('Brand update failed: ' . $e->getMessage());
            throw new \Exception('An error occurred while updating the brand. Please try again.');
        }
    }

    /**
     * Delete multiple Brand records by their IDs and clear the cache.
     *
     * @param array $BrandIds An array of IDs of Brands to delete.
     * @return bool Returns true if the deletion is successful.
     * @throws \Exception If any error occurs during the deletion process.
     *
     * This function deletes Brand records by their IDs. It ensures
     * cached data is invalidated after successful deletion.
     */
    public function deleteBrand(array $BrandIds): bool
    {
        try {
            // get Brand from the database
            $brands = Brand::whereIn('id', $BrandIds)->get();

            // Delete Brand from the database
            foreach ($brands as $brand) {
                $this->mediaService->deleteDocument($brand,"brands");
            }
            // Delete Brand from the database
            Brand::whereIn('id', $BrandIds)->delete();

            // Clear cache after deletion
            Cache::forget('brand_all');

            return true;
        } catch (ModelNotFoundException $e) {
            Log::error('Brand not found: ' . $e->getMessage());
            throw new Exception('Brand not found: ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error('Error deleting Brand: ' . $e->getMessage());
            throw new Exception('An error occurred while deleting data Brand.');
        }
    }

    /**
     * Delete a single Brand record by its ID and clear the cache.
     *
     * @param int $id The ID of the Brand to delete.
     * @throws \Exception If any error occurs during the deletion process.
     *
     * This function deletes a specific Brand record by ID. It also
     * clears the cached data to maintain data integrity.
     */
    public function destroy(int $id)
    {
        try {
            // get Brand from the database
            $brand = Brand::findOrFail($id);

            // delete brand image
            $this->mediaService->deleteDocument($brand,"brands");

            // Delete Brand from the database
            $brand->delete();

            Cache::forget('brand_all');
        } catch (ModelNotFoundException $e) {
            Log::error('Brand not found: ' . $e->getMessage());
            throw new Exception('Brand not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error deleting Brand: ' . $e->getMessage());
            throw new Exception('An error occurred while deleting data Brand.');
        }
    }





}

