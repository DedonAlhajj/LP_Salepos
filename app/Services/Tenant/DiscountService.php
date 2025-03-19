<?php

namespace App\Services\Tenant;


use App\Models\Customer;
use App\Models\Discount;
use App\Models\DiscountPlan;
use App\Models\DiscountPlanCustomer;
use App\Models\DiscountPlanDiscount;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;

class DiscountService
{

    protected DiscountPlanService $discountPlanService;

    public function __construct(DiscountPlanService $discountPlanService)
    {
        $this->discountPlanService = $discountPlanService;
    }
    /**
     * Retrieve all discounts with their related discount plans.
     *
     * This method fetches all discount records from the database, including their associated discount plans.
     * The results are ordered by ID in descending order.
     * If an error occurs, it logs the error and throws an exception.
     *
     * @return Collection|array List of discounts with their related discount plans.
     * @throws \Exception If retrieving the discounts fails.
     */
    public function getDiscount(): Collection|array
    {
        try {
            // Retrieve all discounts with their related discount plans, ordered by ID (latest first)
            return Discount::with('discountPlans')->orderBy('id', 'desc')->get();
        } catch (\Exception $exception) {
            // Log the error for debugging purposes
            Log::error('Failed to retrieve discount index: ' . $exception->getMessage());

            // Throw an exception to indicate failure
            throw new \Exception('An error occurred while retrieving discounts. Please try again.');
        }
    }

    /**
     * Retrieve all available discount plans.
     *
     * This method fetches the list of all available discount plans from the service layer.
     *
     * @return Collection|array List of discount plans.
     * @throws \Exception If retrieving discount plans fails.
     */
    public function create(): Collection|array
    {
        return $this->discountPlanService->getDiscountPlan();
    }

    /**
     * Create a new discount along with its associated discount plans.
     *
     * This method processes the provided discount data, formats dates and lists correctly,
     * and stores the discount record in the database within a transaction.
     * If any error occurs during the creation process, the transaction is rolled back,
     * the error is logged, and `null` is returned.
     *
     * @param array $data Discount data including valid dates, product list, days, and discount plan IDs.
     * @return Discount|null The created discount instance if successful, otherwise null.
     */
    public function createDiscount(array $data): ?Discount
    {
        try {
            return DB::transaction(function () use ($data) {
                // Convert date formats to proper format
                $data['valid_from'] = date('Y-m-d', strtotime($data['valid_from']));
                $data['valid_till'] = date('Y-m-d', strtotime($data['valid_till']));

                // Convert product list and days to string format
                $data['product_list'] = isset($data['product_list']) ? implode(",", $data['product_list']) : null;
                $data['days'] = implode(",", $data['days']);

                // Create a new discount entry
                $discount = Discount::create($data);

                // Prepare discount plan mappings for bulk insert
                $discountPlans = array_map(fn($planId) => [
                    'discount_id' => $discount->id,
                    'discount_plan_id' => $planId
                ], $data['discount_plan_id']);

                // Insert discount plan mappings into the pivot table
                DiscountPlanDiscount::insert($discountPlans);

                return $discount;
            });
        } catch (\Exception $e) {
            // Rollback the transaction in case of failure
            DB::rollBack();

            // Log the error for debugging purposes
            Log::error('Failed to create discount: ' . $e->getMessage());

            return null; // Return null if creation fails
        }
    }

    /**
     * Retrieve discount details for editing.
     *
     * This method fetches the discount record by ID, retrieves associated discount plans,
     * and fetches all available discount plans for selection.
     *
     * @param int $id The ID of the discount to edit.
     * @return array Contains discount data, associated discount plans, and the list of available discount plans.
     */
    #[ArrayShape(['lims_discount_data' => "mixed", 'discount_plan_ids' => "mixed", 'lims_discount_plan_list' => "mixed"])]
    public function edit(int $id): array
    {
        return [
            'lims_discount_data' => Discount::find($id), // Fetch discount details by ID
            'discount_plan_ids' => DiscountPlanDiscount::where('discount_id', $id)
                ->pluck('discount_plan_id')
                ->toArray(), // Retrieve associated discount plan IDs
            'lims_discount_plan_list' => DiscountPlan::all(), // Get all available discount plans
        ];
    }

    /**
     * Update an existing discount.
     *
     * This method updates discount details, formats dates, handles product and day lists,
     * and ensures discount plans are synchronized. If an error occurs, it rolls back the transaction.
     *
     * @param int $id The ID of the discount to update.
     * @param array $data The updated discount data.
     * @return Discount The updated discount instance.
     * @throws \Exception If updating the discount fails.
     */
    public function updateDiscount(int $id, array $data): Discount
    {
        DB::beginTransaction();
        try {
            $discount = Discount::findOrFail($id); // Fetch the discount record

            // Convert date formats to proper format
            $data['valid_from'] = date('Y-m-d', strtotime(str_replace("/", "-", $data['valid_from'])));
            $data['valid_till'] = date('Y-m-d', strtotime(str_replace("/", "-", $data['valid_till'])));

            // Handle product list based on applicability
            $data['product_list'] = ($data['applicable_for'] === 'All') ? null :
                (isset($data['product_list']) ? implode(",", $data['product_list']) : null);

            // Convert days list to a string format
            $data['days'] = isset($data['days']) ? implode(",", $data['days']) : null;

            // Update discount details
            $discount->update($data);

            // Synchronize associated discount plans
            $this->syncDiscountPlans($discount->id, $data['discount_plan_id']);

            DB::commit();
            return $discount;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating discount: " . $e->getMessage());
            throw new \Exception("An error occurred while updating the discount.");
        }
    }

    /**
     * Synchronize discount plans for a given discount.
     *
     * This method ensures that only the selected discount plans are associated with the given discount.
     * It removes plans that are no longer associated and inserts new plans that are added.
     *
     * @param int $discountId The ID of the discount to update.
     * @param array $newPlanIds The list of selected discount plan IDs.
     */
    private function syncDiscountPlans(int $discountId, array $newPlanIds): void
    {
        // Retrieve existing associated discount plan IDs
        $existingPlanIds = DiscountPlanDiscount::where('discount_id', $discountId)
            ->pluck('discount_plan_id')
            ->toArray();

        // Remove plans that are no longer selected
        DiscountPlanDiscount::where('discount_id', $discountId)
            ->whereNotIn('discount_plan_id', $newPlanIds)
            ->delete();

        // Identify new plans to be added
        $plansToInsert = array_diff($newPlanIds, $existingPlanIds);
        $insertData = array_map(fn($planId) => [
            'discount_id' => $discountId,
            'discount_plan_id' => $planId
        ], $plansToInsert);

        // Insert new discount plans in bulk if any exist
        if (!empty($insertData)) {
            DiscountPlanDiscount::insert($insertData);
        }
    }




}

