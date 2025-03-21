<?php

namespace App\Services\Tenant;



use App\Models\Product_Warehouse;
use App\Models\ProductTransfer;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;

class WarehouseService
{

    /**
     * Retrieve all warehouses from the cache or database.
     *
     * @return Collection
     */
    public function getWarehouses(): Collection
    {
        return Cache::remember("Warehouse", 60, function () {
            // Fetch all warehouse records from the database
            return Warehouse::all();
        });
    }

    /**
     * Retrieve the count of all warehouses from the cache or database.
     *
     * @return int
     */
    public function warehousesCount(): int
    {
        return Cache::remember("Warehouse_Count", 60, function () {
            // Count the number of warehouses in the database
            return Warehouse::count();
        });
    }

    /**
     * Get the data needed for the index view.
     *
     * @return array
     */
    #[ArrayShape(['lims_warehouse_all' => "\Illuminate\Database\Eloquent\Collection", 'numberOfWarehouse' => "int"])]
    public function getDataIndex()
    {
        return [
            'lims_warehouse_all' => $this->getWarehouses(),
            'numberOfWarehouse' => $this->warehousesCount(),
        ];
    }

    /**
     * Create a new warehouse entry in the database.
     *
     * @param array $data
     * @return void|null
     */
    public function createWarehouse(array $data)
    {
        try {
            // Attempt to create a new warehouse record
            Warehouse::create($data);

            // Clear the cache for warehouses
            Cache::forget('Warehouse');
            Cache::forget('Warehouse_Count');
        } catch (\Exception $e) {
            // Log any error that occurs during creation
            Log::error('Failed to create Warehouse: ' . $e->getMessage());

            return null; // Return null if creation fails
        }
    }

    /**
     * Retrieve a warehouse by its ID for editing.
     *
     * @param int $id
     * @return Warehouse
     * @throws \Exception
     */
    public function edit(int $id): Warehouse
    {
        try {
            // Find the warehouse by ID or throw an exception if not found
            return Warehouse::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Warehouse not found.'); // Rethrow if the warehouse is not found
        } catch (\Exception $e) {
            // Log any other errors
            Log::error('Failed to retrieve Warehouse for editing: ' . $e->getMessage());

            throw new \Exception('Failed to retrieve Warehouse.'); // Rethrow the exception
        }
    }

    /**
     * Update an existing warehouse entry in the database.
     *
     * @param array $data
     * @return void|null
     * @throws \Exception
     */
    public function updateWarehouse(array $data)
    {
        try {
            // Find the warehouse by ID and update it with the provided data
            Warehouse::findOrFail($data['warehouse_id'])->update($data);

            // Clear the cache for warehouses
            Cache::forget('Warehouse');
            Cache::forget('Warehouse_Count');
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Warehouse not found.'); // Rethrow if the warehouse is not found
        } catch (\Exception $e) {
            // Log any error that occurs during the update process
            Log::error('Failed to update Warehouse: ' . $e->getMessage());

            return null; // Return null if update fails
        }
    }

    /**
     * Delete multiple warehouse based on an array of IDs.
     *
     * @param array $Ids List of warehouse IDs to be deleted.
     * @return bool Returns true if deletion was successful.
     * @throws ModelNotFoundException If the warehouse are not found.
     * @throws \Exception If an error occurs during deletion.
     */
    public function deleteWarehouses(array $Ids): bool
    {
        try {
            // Delete warehouse from the database
            Warehouse::whereIn('id', $Ids)->delete();

            // Clear cache after deletion
            Cache::forget('Warehouse');
            Cache::forget('Warehouse_Count');

            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting Department: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a single warehouse by ID.
     *
     * @param int $id The ID of the warehouse to be deleted.
     * @return bool Returns true if deletion was successful.
     * @throws ModelNotFoundException If the warehouse is not found.
     * @throws \Exception If an error occurs during deletion.
     */
    public function deleteWarehouse(int $id): bool
    {
        try {
            // Find the warehouse by ID or fail
            $warehouse = Warehouse::findOrFail($id)->delete();

            // Clear cache after deletion
            Cache::forget('Warehouse');
            Cache::forget('Warehouse_Count');

            return true;
        } catch (ModelNotFoundException $e) {
            Log::error('warehouse not found: ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error deleting warehouse: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getWarehousesById($user)
    {
        return Warehouse::when(!$user->hasRole(['Admin', 'Owner']), function ($query) use ($user) {
                return $query->where('id', $user->warehouse_id);
            })
            ->get();
    }

    /**
     * Update the stock quantity of a product in a specific warehouse.
     *
     * This method adjusts the stock quantity of a product within a warehouse, either increasing
     * or decreasing it based on the specified operation.
     *
     * @param ProductTransfer $productTransfer  The product transfer data, including product ID and variant ID.
     * @param int $warehouseId                  The ID of the warehouse where stock is being updated.
     * @param float $quantity                    The quantity to be added or subtracted.
     * @param string $operation                  The operation type: 'increase' to add stock, 'decrease' to reduce stock.
     *
     * @return void
     *
     * @throws ModelNotFoundException If the product in the warehouse is not found.
     */
    public function updateWarehouseStock(ProductTransfer $productTransfer, int $warehouseId, float $quantity, string $operation): void
    {
        // Initialize a query on the Product_Warehouse model
        $query = Product_Warehouse::query();

        // Check if the product has a variant and filter accordingly
        if ($productTransfer->variant_id) {
            $query->FindProductWithVariant($productTransfer->product_id, $productTransfer->variant_id, $warehouseId);
        } else {
            $query->FindProductWithoutVariant($productTransfer->product_id, $warehouseId);
        }

        // Retrieve the product warehouse entry or throw an exception if not found
        $productWarehouse = $query->firstOrFail();

        // Update the stock quantity based on the operation type
        if ($operation === 'increase') {
            $productWarehouse->increment('qty', $quantity); // Increase stock
        } else {
            $productWarehouse->decrement('qty', $quantity); // Decrease stock
        }
    }


}
