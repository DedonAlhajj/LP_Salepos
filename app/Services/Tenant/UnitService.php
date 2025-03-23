<?php

namespace App\Services\Tenant;

use App\Models\Unit;
use App\Models\Product;
use App\Models\ProductReturn;
use App\Models\Tax;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;

class UnitService
{


    /**
     * Retrieve all active Unit from the cache or the database.
     *
     * @throws Exception If any error occurs during the retrieval process.
     *
     * This function uses caching to reduce database queries. It attempts to fetch
     * active Unit and stores the result in the cache for 60 seconds.
     * In case of failure, it logs the error and throws an exception.
     */
    public function getUnitWithoutTrashed()
    {
        try {
            return Cache::remember("Unit_all", 60, function () {
                return Unit::withoutTrashed()->get();
            });
        } catch (Exception $e) {
            Log::error("Error fetching modifications (Unit): " . $e->getMessage());
            throw new Exception("An error occurred while fetching the modification data (Unit)..");
        }
    }

    /**
     * Handles the creation of a Unit.
     *
     * This function manages the creation process, including saving Unit details in the database
     * and uploading the associated image, if provided. It also clears the related cache to ensure
     * data consistency. All operations are wrapped within a database transaction to maintain integrity.
     *
     * @param array $data Data containing Unit details (e.g., name, image).
     * @return mixed Returns the created Unit object.
     * @throws Exception If any error occurs during Unit creation, an exception is thrown.
     */
    public function createUnit(array $data): mixed
    {
        try {
            return DB::transaction(function () use ($data) {
                if(!isset($data['base_unit'])){
                    $data['operator'] = '*';
                    $data['operation_value'] = 1;
                }

                // Create the Unit in the database
                Unit::create($data);

                // Clear the cache for all Units to ensure fresh data
                Cache::forget('Unit_all');
            });
        } catch (Exception $e) {
            // Log the error and throw a new exception with a meaningful message
            Log::error("Error creating (Unit): " . $e->getMessage());
            throw new Exception('Failed to create Unit: ' . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function edit($id)
    {
        try {
            return $this->getUnit($id);
        } catch (ModelNotFoundException $e) {
            Log::error('Unit not found (edit): ' . $e->getMessage());
            throw new \Exception('Unit not found');
        }
    }

    /**
     * Handles updating an existing Unit.
     *
     * This function retrieves and updates a Unit's details in the database. If a new image is
     * provided, it replaces the existing one. The process includes clearing the relevant cache
     * for consistency and is wrapped within a transaction to maintain data integrity.
     *
     * @param array $data Data containing updated Unit details (e.g., name, image, Unit_id).
     * @return mixed Returns the updated Unit object.
     * @throws ModelNotFoundException If the Unit is not found.
     * @throws Exception If any other error occurs during the update process.
     */
    public function updateUnit(array $data): mixed
    {
        try {
            return DB::transaction(function () use ($data) {
                if(!isset($data['base_unit'])){
                    $data['operator'] = '*';
                    $data['operation_value'] = 1;
                }
                // Fetch the Unit from the database (ensuring it exists)
                $Unit = Unit::findOrFail($data['unit_id']);

                // Update the Unit with new data
                $Unit->update($data);

                // Clear the cache for all Units to ensure fresh data
                Cache::forget('Unit_all');
            });
        } catch (ModelNotFoundException $e) {
            // Log the error for missing Unit and throw a meaningful exception
            Log::error('Unit not found: ' . $e->getMessage());
            throw new \Exception('Unit not found');
        } catch (\Exception $e) {
            // Log any other error and throw an exception with a meaningful message
            Log::error('Unit update failed: ' . $e->getMessage());
            throw new \Exception('An error occurred while updating the Unit. Please try again.');
        }
    }

    /**
     * Delete multiple Unit records by their IDs and clear the cache.
     *
     * @param array $UnitIds An array of IDs of Units to delete.
     * @return bool Returns true if the deletion is successful.
     * @throws \Exception If any error occurs during the deletion process.
     *
     * This function deletes Unit records by their IDs. It ensures
     * cached data is invalidated after successful deletion.
     */
    public function deleteUnit(array $UnitIds): bool
    {
        try {
            // get Unit from the database
            Unit::whereIn('id', $UnitIds)->delete();

            // Clear cache after deletion
            Cache::forget('Unit_all');

            return true;
        } catch (ModelNotFoundException $e) {
            Log::error('Unit not found: ' . $e->getMessage());
            throw new Exception('Unit not found: ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error('Error deleting Unit: ' . $e->getMessage());
            throw new Exception('An error occurred while deleting data Unit.');
        }
    }

    /**
     * Delete a single Unit record by its ID and clear the cache.
     *
     * @param int $id The ID of the Unit to delete.
     * @throws \Exception If any error occurs during the deletion process.
     *
     * This function deletes a specific Unit record by ID. It also
     * clears the cached data to maintain data integrity.
     */
    public function destroy(int $id)
    {
        try {
            // Delete Unit from the database
            Unit::findOrFail($id)->delete();

            Cache::forget('Unit_all');
        } catch (ModelNotFoundException $e) {
            Log::error('Unit not found: ' . $e->getMessage());
            throw new Exception('Unit not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error deleting Unit: ' . $e->getMessage());
            throw new Exception('An error occurred while deleting data Unit.');
        }
    }

    /**
     * Retrieve sale units related to a specific base unit.
     *
     * This function fetches all units where the provided unit ID matches either the `base_unit`
     * or the unit's own `id`. The result is a key-value collection of unit names mapped to their IDs.
     *
     * @param int $id The ID of the base unit.
     * @return \Illuminate\Support\Collection A collection of unit names with their corresponding IDs.
     */
    public function getSaleUnits(int $id)
    {
        // Retrieve units that either match the base unit ID or have the same ID
        return Unit::where("base_unit", $id)
            ->orWhere('id', $id)
            ->pluck('unit_name', 'id'); // Return only unit names and IDs
    }

    /**
     * Retrieve a single unit by its ID.
     *
     * This function fetches a unit from the database by its ID. If the unit is not found,
     * it throws a ModelNotFoundException.
     *
     * @param int $unitId The ID of the unit to retrieve.
     * @return Unit The unit object fetched from the database.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the unit is not found.
     */
    public function getUnit($unitId)
    {
        // Find the unit by its ID or throw an exception if not found
        return Unit::findOrFail($unitId);
    }

    /**
     * Retrieve units related to a specific product.
     *
     * This function fetches all units where the `base_unit` matches the product's unit ID
     * or the unit's own ID matches the product's unit ID. It maps the result into an array
     * with relevant unit details like name, operator, and operation value.
     *
     * @param Product $product The product whose units are to be retrieved.
     * @return array An array of unit details (name, operator, operation value).
     */
    public function getUnitsByProduct(Product $product): array
    {
        // Fetch units related to the product's unit ID and transform them into a structured array
        return Unit::where('base_unit', $product->unit_id)
            ->orWhere('id', $product->unit_id)
            ->get()
            ->map(fn($unit) => [
                'name' => $unit->unit_name,          // Unit name
                'operator' => $unit->operator,      // Operator used for conversion
                'operation_value' => $unit->operation_value // Value used for conversion
            ])
            ->toArray();
    }

    /**
     * Convert a quantity to the base unit based on the unit's operation.
     *
     * This function converts a given quantity to its equivalent in the base unit
     * using the unit's operator and operation value. If the unit name is not found,
     * the original quantity is returned.
     *
     * @param float|int $quantity The quantity to be converted.
     * @param string $unit_name The name of the unit to convert from.
     * @return float|int The equivalent quantity in the base unit.
     */
    public function convertToBaseUnit($quantity, $unit_name): float|int
    {
        // Retrieve the unit by its name
        $unit = Unit::where('unit_name', $unit_name)->first();

        // If the unit doesn't exist, return the original quantity
        if (!$unit) return $quantity;

        // Perform the conversion based on the unit's operator (* or /)
        return ($unit->operator == '*')
            ? ($quantity * $unit->operation_value)
            : ($quantity / $unit->operation_value);
    }

    /**
     * Retrieve unit-related data for a given base unit and purchase unit.
     *
     * This function fetches all units associated with the base unit ID and organizes unit names,
     * operators, and operation values into separate arrays. The purchase unit, if matched, is
     * prioritized in the output arrays by placing its data at the beginning.
     *
     * @param int $baseUnitId The ID of the base unit.
     * @param int $purchaseUnitId The ID of the purchase unit.
     * @return array An associative array containing 'unit_name', 'unit_operator', and 'unit_operation_value'.
     */
    public function getUnitData(int $baseUnitId, int $purchaseUnitId): array
    {
        // Retrieve all units related to the base unit or its ID
        $units = Unit::where('base_unit', $baseUnitId)->orWhere('id', $baseUnitId)->get();

        // Initialize arrays for unit properties
        $unit_name = [];
        $unit_operator = [];
        $unit_operation_value = [];

        // Organize unit data, prioritizing the purchase unit
        foreach ($units as $unit) {
            if ($purchaseUnitId == $unit->id) {
                // Add purchase unit's data to the beginning of the arrays
                array_unshift($unit_name, $unit->unit_name);
                array_unshift($unit_operator, $unit->operator);
                array_unshift($unit_operation_value, $unit->operation_value);
            } else {
                // Add other units' data to the arrays
                $unit_name[] = $unit->unit_name;
                $unit_operator[] = $unit->operator;
                $unit_operation_value[] = $unit->operation_value;
            }
        }

        // Return the collected data as an associative array
        return compact('unit_name', 'unit_operator', 'unit_operation_value');
    }

    /**
     * Calculate the received quantity's equivalent in the base unit.
     *
     * This function converts the received quantity into its base unit equivalent
     * using the operator and operation value of the specified unit. If the unit
     * is not found, an exception is thrown.
     *
     * @param string $unitName The name of the unit for conversion.
     * @param float $receivedQty The quantity received in the specified unit.
     * @return float The equivalent quantity in the base unit.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the unit is not found.
     */
    public function calculateReceivedValue(string $unitName, float $receivedQty): float
    {
        // Retrieve the unit by its name
        $unit = Unit::where('unit_name', $unitName)->firstOrFail();

        // Perform the conversion based on the unit's operator
        return ($unit->operator == '*')
            ? $receivedQty * $unit->operation_value
            : $receivedQty / $unit->operation_value;
    }

    /**
     * Retrieve the ID of a unit based on its name.
     *
     * This function retrieves the ID of a unit that matches the given name.
     *
     * @param string $unitName The name of the unit.
     * @return int The ID of the unit.
     */
    public function getUnitId(string $unitName): int
    {
        // Fetch the unit ID based on its name
        return Unit::where('unit_name', $unitName)->value('id');
    }

    /**
     * Retrieve unit details for a given product.
     *
     * This function fetches units related to a product's unit ID. If the product type
     * is not 'standard', default values indicating 'not applicable' are returned.
     * For standard products, the result includes unit details such as name, operator,
     * and operation value.
     *
     * @param Product $product The product object containing unit details.
     */
    public function getUnits(Product $product): array
    {
        // If the product type is not 'standard', return default values
        if ($product->type !== 'standard') {
            return [
                'name' => 'n/a',
                'operator' => 'n/a',
                'operation_value' => 'n/a'
            ];
        }

        // Fetch units related to the product's unit ID and transform the result
        return Unit::whereIn('id', [$product->unit_id])
            ->orWhere('base_unit', $product->unit_id)
            ->get()
            ->map(fn($unit) => [
                'name' => $unit->unit_name,          // Unit name
                'operator' => $unit->operator,      // Operator used for conversion
                'operation_value' => $unit->operation_value // Value used for conversion
            ]);
    }

    /**
     * Retrieve product units and organize unit data.
     *
     * This function fetches all units related to the base unit of the product, organizing
     * their names, operators, and operation values into separate strings. If the sale unit
     * matches a unit, its details are prioritized and placed at the beginning of the strings.
     *
     * @param Product $product The product object containing base unit information.
     * @param ProductReturn $productReturn The product return object containing sale unit information.
     * @return array An associative array with unit names, operators, and operation values as strings.
     */
    #[ArrayShape(['unit_name' => "string", 'unit_operator' => "string", 'unit_operation_value' => "string"])] public function
    getProductUnits(Product $product, ProductReturn $productReturn): array
    {
        // Fetch all units related to the product's base unit or its ID
        $units = Unit::where('base_unit', $product->unit_id)
            ->orWhere('id', $product->unit_id)
            ->get();

        // Initialize arrays for unit properties
        $unit_name = [];
        $unit_operator = [];
        $unit_operation_value = [];

        // Process each unit and organize data
        foreach ($units as $unit) {
            if ($productReturn->sale_unit_id == $unit->id) {
                // Prioritize sale unit by adding it at the beginning
                array_unshift($unit_name, $unit->unit_name);
                array_unshift($unit_operator, $unit->operator);
                array_unshift($unit_operation_value, $unit->operation_value);
            } else {
                // Append other units' data to the arrays
                $unit_name[] = $unit->unit_name;
                $unit_operator[] = $unit->operator;
                $unit_operation_value[] = $unit->operation_value;
            }
        }

        // Combine arrays into strings separated by commas and return as associative array
        return [
            'unit_name' => implode(',', $unit_name) . ',',
            'unit_operator' => implode(',', $unit_operator) . ',',
            'unit_operation_value' => implode(',', $unit_operation_value) . ','
        ];
    }

    /**
     * Retrieve detailed unit data for a specific product.
     *
     * This function fetches all units associated with the product's base unit or ID,
     * organizing the unit names, operators, and operation values into separate arrays.
     * If the product's sale unit matches a unit, its details are prioritized and placed
     * at the beginning of the arrays.
     *
     * @param Product $product The product object containing unit information.
     * @return array An associative array containing unit details:
     *               - 'unitName': Array of unit names.
     *               - 'unitOperator': Array of unit operators.
     *               - 'unitOperationValue': Array of unit operation values.
     */
    #[ArrayShape(['unitName' => "array", 'unitOperator' => "array", 'unitOperationValue' => "array"])] public function
    getUnitDetails($product): array
    {
        // Fetch all units related to the product's base unit or its ID
        $units = Unit::where("base_unit", $product->unit_id)
            ->orWhere('id', $product->unit_id)
            ->get();

        // Initialize arrays for unit properties
        $unitName = [];
        $unitOperator = [];
        $unitOperationValue = [];

        // Process each unit and organize data
        foreach ($units as $unit) {
            if ($product->sale_unit_id == $unit->id) {
                // Prioritize sale unit by adding it at the beginning
                array_unshift($unitName, $unit->unit_name);
                array_unshift($unitOperator, $unit->operator);
                array_unshift($unitOperationValue, $unit->operation_value);
            } else {
                // Append other units' data to the arrays
                $unitName[] = $unit->unit_name;
                $unitOperator[] = $unit->operator;
                $unitOperationValue[] = $unit->operation_value;
            }
        }

        // Return the collected data as an associative array
        return [
            'unitName' => $unitName,
            'unitOperator' => $unitOperator,
            'unitOperationValue' => $unitOperationValue
        ];
    }


}

