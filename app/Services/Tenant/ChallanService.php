<?php

namespace App\Services\Tenant;

use App\DTOs\ChallanDTO;
use App\DTOs\ChallanStoreDTO;
use App\DTOs\ChallanUpdateDTO;
use App\Models\Account;
use App\Models\CashRegister;
use App\Models\Challan;
use App\Models\PackingSlip;
use App\Models\Payment;
use App\Models\Product_Sale;
use App\Models\Sale;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;

class ChallanService
{

    protected CourierServices $courierServices;
    protected PackingSlipService $packingSlipService;

    public function __construct(
        CourierServices    $courierServices,
        PackingSlipService $packingSlipService)
    {
        $this->courierServices = $courierServices;
        $this->packingSlipService = $packingSlipService;
    }


    /**
     * Retrieve data for the Challan index page.
     *
     * This method fetches the list of available couriers, retrieves Challans based on the provided filters,
     * and returns the necessary data for rendering the index view.
     *
     * @param string|null $courier_id The ID of the selected courier (optional).
     * @param string|null $status The status filter for challans (optional).
     * @return array The structured data containing couriers and challans.
     * @throws Exception If an error occurs while fetching the data.
     */
    public function getIndexData(?string $courier_id, ?string $status)
    {
        try {
            return [
                'courier_id' => $courier_id,
                'status' => $status,
                'courier_list' => $this->courierServices->getCourier(), // Fetch the list of couriers.
                'challans' => $this->getChallans($courier_id, $status), // Fetch filtered challans.
            ];
        } catch (Exception $e) {
            Log::error("Error fetching modifications (Challan) getIndexData: " . $e->getMessage());
            throw new Exception("An error occurred while fetching the modification data (Challan) getIndexData.");
        }
    }


    /**
     * Retrieve Challans based on filters.
     *
     * This method fetches Challans with their related data and applies the given filters.
     * The result is transformed into `ChallanDTO` objects for structured data representation.
     *
     * @param string|null $courier_id The ID of the selected courier (optional).
     * @param string|null $status The status filter for challans (optional).
     * @return Collection A collection of ChallanDTO objects.
     */
    public function getChallans(?string $courier_id, ?string $status): Collection
    {
        return Challan::with(['courier', 'createdBy', 'closedBy', 'packingSlips.sale']) // Load necessary relationships.
        ->filter($courier_id, $status) // Apply filter scope.
        ->latest() // Order by latest Challans.
        ->get()
            ->map(fn($challan) => ChallanDTO::fromModel($challan)); // Transform data into DTO format.
    }


    /**
     * Prepare data for creating a new Challan.
     *
     * This method processes the provided packing slip IDs, validates them, generates a new reference number,
     * and retrieves the list of active couriers.
     *
     * @param string $ids Comma-separated list of packing slip IDs.

     * @throws Exception If an error occurs while fetching the data.
     */
    public function getCreateDate(string $ids)
    {
        try {
            // 🔹 Convert comma-separated IDs into an array.
            $packingSlipIds = explode(',', $ids);

            // 🔹 Fetch packing slips along with associated sale data.
            $packingSlips = $this->packingSlipService->getPackingSlipsWithSale($packingSlipIds);

            // 🔹 Validate packing slips to ensure a new Challan can be created.
            if (!$this->packingSlipService->validatePackingSlips($packingSlips)) {
                throw new Exception("Please close previous challan before creating a new one.");
            }

            // 🔹 Generate a new unique reference number for the Challan.
            $newReference = $this->generateReferenceNumber();

            // 🔹 Fetch the list of active couriers.
            $couriers = $this->courierServices->getCourier();

            return [
                'new_reference' => $newReference,
                'packing_slip_all' => $packingSlips,
                'courier_list' => $couriers
            ];
        } catch (Exception $e) {
            Log::error("Error fetching modifications (Challan) getCreateDate: " . $e->getMessage());
            throw new Exception("An error occurred while fetching the modification data (Challan).");
        }
    }


    /**
     * Create a new Challan record.
     *
     * This method handles the transactional process of creating a Challan, including:
     * - Generating a new reference number.
     * - Updating the associated packing slips.
     * - Storing the Challan details in the database.
     *
     * @param ChallanStoreDTO $challanDTO Data Transfer Object containing Challan details.
     * @return Challan The newly created Challan model instance.
     */
    public function createChallan(ChallanStoreDTO $challanDTO): Challan
    {
        return DB::transaction(function () use ($challanDTO) {
            // 🔹 Generate a new unique reference number.
            $referenceNo = $this->generateReferenceNumber();

            // 🔹 Bulk update packing slips with the associated courier.
            $this->packingSlipService->updatePackingSlips($challanDTO->packingSlipList, $challanDTO->courierId);

            // 🔹 Create a new Challan record in the database.
            return Challan::create([
                'reference_no' => $referenceNo,
                'status' => 'Active',
                'packing_slip_list' => implode(",", $challanDTO->packingSlipList), // Store as comma-separated string.
                'amount_list' => json_encode($challanDTO->amountList), // Convert to JSON format.
                'courier_id' => $challanDTO->courierId,
                'created_by_id' => Auth::id(), // Set the creator ID.
                'created_at' => $challanDTO->createdAt ?? now() // Use provided timestamp or default to current time.
            ]);
        });
    }


    /**
     * Generate a unique reference number for a new Challan.
     *
     * The reference number is determined by finding the highest existing reference number
     * and incrementing it by 1. If no Challans exist, it starts from 1001.
     *
     * @return int The newly generated reference number.
     */
    private function generateReferenceNumber(): int
    {
        return (int) (Challan::max('reference_no') ?? 1000) + 1;
    }


    /**
     * Retrieves invoice data for a given Challan ID.
     *
     * @param int $challanId The ID of the Challan.
     * @return array The Challan details along with formatted packing slip data.
     * @throws Exception If the Challan is not found or any error occurs during data retrieval.
     */
    #[ArrayShape(['challan' => "mixed", 'packing_slips' => "mixed"])]
    public function getInvoiceData(int $challanId): array
    {
        try {
            // Fetch Challan with associated Courier details
            $challan = Challan::with(['courier'])->findOrFail($challanId);

            // Convert stored comma-separated IDs into an array
            $packingSlipIds = explode(',', $challan->packing_slip_list);
            $amountList = json_decode($challan->amount_list, true);

            // Retrieve Packing Slips in bulk with related Sale and Customer data
            $packingSlips = PackingSlip::with(['sale.customer'])
                ->whereIn('id', $packingSlipIds)
                ->get();

            // Process and format packing slip details for the invoice
            $formattedPackingSlips = $packingSlips->map(function ($packingSlip, $key) use ($amountList) {
                $sale = $packingSlip->sale;
                $customer = $sale->customer;

                // Determine shipping address, city, and phone (fallback to customer details if missing)
                $address = $sale->shipping_address ?: $customer->address;
                $city = $sale->shipping_city ?: $customer->city;
                $phone = $sale->shipping_phone ?: $customer->phone_number;

                return [
                    'index' => $key + 1,
                    'sale_reference_no' => $sale->reference_no,
                    'address' => "{$address}, {$city}",
                    'phone' => $phone,
                    'amount' => $amountList[$key] ?? 0, // Retrieve the corresponding amount or default to 0
                ];
            });

            return [
                'challan' => $challan,
                'packing_slips' => $formattedPackingSlips
            ];
        } catch (Exception $e) {
            Log::error("Error fetching Invoice Data: " . $e->getMessage());
            throw new Exception("An error occurred while fetching the Invoice.");
        }
    }


    /**
     * Finds and retrieves a Challan record by its ID.
     *
     * @param int|string $id The ID of the Challan.
     * @return Challan|null The Challan instance if found, otherwise null.
     */
    public function getFindChallan($id): ?Challan
    {
        return Challan::find($id);
    }


    /**
     * Updates an existing Challan with provided data.
     *
     * @param ChallanUpdateDTO $dto Data Transfer Object containing updated Challan details.
     * @throws Exception If an error occurs during the update process.
     */
    public function updateChallan(ChallanUpdateDTO $dto)
    {
        DB::beginTransaction();
        try {
            // Retrieve the Challan with associated Packing Slips and related Sales & Customers
            $challan = Challan::with('packingSlips.sale.customer')->findOrFail($dto->challanId);

            // Update Challan details with new transaction data
            $challan->update([
                'cash_list' => implode(",", $dto->cashList),
                'cheque_list' => implode(",", $dto->chequeList),
                'online_payment_list' => implode(",", $dto->onlinePaymentList),
                'delivery_charge_list' => implode(",", $dto->deliveryChargeList),
                'status_list' => implode(",", $dto->statusList),
                'status' => 'Close', // Mark Challan as closed
                'closing_date' => $dto->closingDate,
                'closed_by_id' => $dto->closedById
            ]);

            // Update related Packing Slips
            $this->updatePackingSlips($challan, $dto);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating Challan: " . $e->getMessage());
            throw new \Exception("Error updating Challan: " . $e->getMessage());
        }
    }


    /**
     * Updates the status and payment details of all Packing Slips associated with a Challan.
     *
     //* @param Challan $challan The Challan instance being updated.
     * @param ChallanUpdateDTO $dto Data Transfer Object containing the updated status and payment details.
     * @throws Exception
     */
    private function updatePackingSlips($challan, ChallanUpdateDTO $dto)
    {
        foreach ($challan->packingSlips as $key => $packingSlip) {
            // Update the status of the Packing Slip
            $this->updatePackingSlipStatus($packingSlip, $dto->statusList[$key]);

            // Process payments related to the Packing Slip
            $this->processPayments($packingSlip, $dto->cashList[$key], $dto->chequeList[$key], $dto->onlinePaymentList[$key]);

            // Update the Sale status based on the delivery progress
            $this->updateSaleStatus($packingSlip);
        }
    }


    /**
     * Updates the status of a given Packing Slip and checks if all related Packing Slips are delivered.
     *
     * @param PackingSlip $packingSlip The Packing Slip being updated.
     * @param string $status The new status of the Packing Slip.
     */
    private function updatePackingSlipStatus(PackingSlip $packingSlip, string $status)
    {
        if ($status === 'Delivered') {
            // Mark associated products as delivered in the sale
            Product_Sale::whereIn('product_id', $packingSlip->products->pluck('id'))
                ->where('sale_id', $packingSlip->sale_id)
                ->update(['is_delivered' => true]);

            // Update the Packing Slip status to 'Delivered'
            $packingSlip->update(['status' => 'Delivered']);

            // Check if all Packing Slips in the Delivery are delivered
            $delivery = $packingSlip->delivery;
            if ($delivery) {
                $packingSlipIds = explode(",", $delivery->packing_slip_ids);
                $pendingCount = PackingSlip::whereIn("id", $packingSlipIds)
                    ->where('status', 'Pending')
                    ->count();

                // If all Packing Slips in the Delivery are delivered, update Delivery status
                if ($pendingCount === 0) {
                    $delivery->update(['status' => 3]); // Status 3 indicates completion
                }
            }
        }
    }


    /**
     * Processes payments for a given Packing Slip based on the provided payment methods.
     *
     * @param PackingSlip $packingSlip The Packing Slip associated with the payment.
     * @param float|null $cash The cash amount paid (if any).
     * @param float|null $cheque The cheque amount paid (if any).
     * @param float|null $onlinePayment The online payment amount paid (if any).
     * @throws Exception
     */
    private function processPayments(PackingSlip $packingSlip, $cash, $cheque, $onlinePayment)
    {
        // Create payments based on available payment methods
        if ($cash) {
            $this->createPayment($cash, $packingSlip->sale, 'Cash');
        }
        if ($cheque) {
            $this->createPayment($cheque, $packingSlip->sale, 'Cheque');
        }
        if ($onlinePayment) {
            $this->createPayment($onlinePayment, $packingSlip->sale, 'Credit Card');
        }
    }


    /**
     * Updates the Sale status based on the delivery progress of associated products.
     *
     * @param PackingSlip $packingSlip The Packing Slip associated with the Sale.
     */
    private function updateSaleStatus(PackingSlip $packingSlip)
    {
        $sale = $packingSlip->sale;

        // Count delivered and non-delivered products for the sale
        $deliveredCount = Product_Sale::where('sale_id', $sale->id)
            ->where('is_delivered', true)
            ->count();

        $nonDeliveredCount = Product_Sale::where('sale_id', $sale->id)
            ->where('is_delivered', false)
            ->count();

        // Update Sale status based on product delivery progress
        if ($deliveredCount > 0 && $nonDeliveredCount === 0) {
            $sale->update(['sale_status' => 1]); // 1 indicates sale is fully delivered
        } elseif ($nonDeliveredCount > 0) {
            $packingSlip->update(['status' => 'Pending']); // Set packing slip back to pending
        }

        // Check if the Sale has been fully paid and update payment status
        if ($sale->grand_total - $sale->paid_amount == 0) {
            $sale->update(['payment_status' => 4]); // 4 indicates fully paid
        }
    }


    /**
     * Creates a payment entry for a Sale.
     *
     * @param float $amount The amount paid.
     * @param Sale $sale The Sale associated with the payment.
     * @param string $paying_method The method of payment (Cash, Cheque, Credit Card).
     * @throws Exception If no default account is found.
     */
    public function createPayment($amount, $sale, $paying_method)
    {
        // Retrieve the default account for payment transactions
        $accountId = Account::where('is_default', 1)->value('id');
        if (!$accountId) {
            throw new Exception('No default account found.');
        }

        // Retrieve the active cash register for the authenticated user in the current warehouse
        $cashRegisterId = CashRegister::where([
            ['user_id', Auth::id()],
            ['warehouse_id', $sale->warehouse_id],
            ['status', 1] // Ensure the register is active
        ])->value('id'); // Fetch the value directly to optimize query performance

        // Create a new payment record
        Payment::create([
            'payment_reference' => 'spr-' . date("Ymd") . '-' . date("his"), // Generate unique payment reference
            'sale_id' => $sale->id,
            'user_id' => Auth::id(),
            'cash_register_id' => $cashRegisterId,
            'account_id' => $accountId,
            'amount' => $amount,
            'change' => 0,
            'paying_method' => $paying_method,
        ]);

        // Increment the paid amount of the Sale
        $sale->increment('paid_amount', $amount);
    }






}
