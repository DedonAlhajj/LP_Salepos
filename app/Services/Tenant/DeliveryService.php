<?php

namespace App\Services\Tenant;


use App\Actions\SendMailAction;
use App\DTOs\DeliveryCreateDTO;
use App\DTOs\DeliveryDTO;
use App\DTOs\DeliveryEditDTO;
use App\Mail\DeliveryDetails;
use App\Models\Delivery;
use App\Models\Sale;
use App\Models\PackingSlip;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;

class DeliveryService
{
    protected SendMailAction $sendMailAction;
    private MediaService $mediaService;


    public function __construct(SendMailAction $sendMailAction, MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
        $this->sendMailAction = $sendMailAction;
    }


    /**
     * Retrieves all deliveries based on user permissions and filters them accordingly.
     * The delivery data includes customer, sales, products, courier, and other related details.
     * Returns the data in a well-structured format, either as a collection or an array.
     *
     * @return Collection|array
     * @throws Exception If an error occurs while fetching the deliveries.
     */
    public function getAllDeliveries(): Collection|array
    {
        try {
            // Retrieve the currently authenticated user.
            $user = Auth::guard('web')->user();

            // Initialize the query to fetch delivery data and preload related models.
            $query = Delivery::with([
                'sale.customer',  // Load the customer data associated with each sale.
                'sale.products' => function ($query) {
                    $query->with('product:id,name'); // Load product details for each sale product.
                },
                'courier:id,name',  // Load courier data (ID and name).
                'user'              // Load user data associated with the delivery.
            ])->orderBy('id', 'desc'); // Order deliveries in descending order by ID.

            // Apply filtering based on user roles and staff access configuration.
            if (!$user->hasRole(['Admin', 'Owner']) && config('staff_access') == 'own') {
                $query->where('user_id', Auth::id()); // Restrict data to the authenticated user.
            }

            // Fetch all matching delivery records.
            $deliveries = $query->get();

            // Process and format the delivery data for presentation.
            return $deliveries->map(function ($delivery) {
                return [
                    'id' => $delivery->id,
                    'reference_no' => $delivery->reference_no,
                    'sale_reference_no' => $delivery->sale->reference_no,
                    'customer_name' => $delivery->sale->customer->name,
                    'customer_phone' => $delivery->sale->customer->phone_number,
                    'address' => $delivery->address,
                    'grand_total' => $delivery->sale->grand_total,
                    'product_names' => $delivery->sale->products->pluck('product.name')->implode(', '), // Combine product names into a single string.
                    'packing_slip_references' => $this->getPackingSlipReferences($delivery->packing_slip_ids),
                    'status' => $this->getStatusText($delivery->status),
                    'courier_name' => $delivery->courier->name ?? 'N/A', // Default to 'N/A' if no courier is assigned.
                    'user_name' => $delivery->user->name ?? 'N/A',
                    'delivered_by' => $delivery->delivered_by ?? 'N/A',
                    'recieved_by' => $delivery->recieved_by ?? 'N/A',
                    'note' => $delivery->note,
                    'date' => $delivery->created_at->format(config('app.date_format', 'Y-m-d')), // Format creation date.
                ];
            });
        } catch (Exception $e) {
            // Log the error for debugging purposes and throw a general exception.
            Log::error("Error fetching deliveries (getAllDeliveries): " . $e->getMessage());
            throw new Exception("An error occurred while fetching the delivery data.");
        }
    }


    /**
     * Retrieves an array of packing slip references based on the provided IDs.
     *
     * @param string|null $packingSlipIds Comma-separated string of packing slip IDs.
     * @return array An array of packing slip reference numbers or ['N/A'] if no IDs are provided.
     */
    private function getPackingSlipReferences($packingSlipIds): array
    {
        if (!$packingSlipIds) {
            return ['N/A']; // Return 'N/A' if no packing slip IDs are provided.
        }

        // Fetch packing slip reference numbers based on the provided IDs.
        return PackingSlip::whereIn('id', explode(",", $packingSlipIds))->pluck('reference_no')->toArray();
    }


    /**
     * Converts a delivery status code into a human-readable status text.
     *
     * @param string $status The delivery status code.
     * @return string The corresponding status text.
     */
    private function getStatusText($status): string
    {
        return match ($status) {
            "1" => trans('file.Packing'),      // Status 1: Packing
            "2" => trans('file.Delivering'),  // Status 2: Delivering
            "3" => trans('file.Delivered'),   // Status 3: Delivered
            default => "Unknown",             // Default: Unknown status
        };
    }


    /**
     * Retrieves the delivery details required for creating a new delivery record.
     * If an existing delivery is linked to the given sale, its details are returned; otherwise, new delivery details are initialized.
     *
     * @param int $saleId The ID of the sale associated with the delivery.
     * @return array An array containing the delivery details.
     * @throws \Exception If an error occurs during the operation.
     */
    public function getDeliveryDetailsCreate(int $saleId): array
    {
        try {
            // Fetch the sale data along with the customer details efficiently.
            $sale = Sale::with('customer')->findOrFail($saleId);

            // Prepare the customer and sale data in an object for easy manipulation.
            $customerSale = (object) [
                'reference_no' => $sale->reference_no,
                'name' => $sale->customer->name,
                'address' => $sale->customer->address,
                'city' => $sale->customer->city,
                'country' => $sale->customer->country,
            ];

            // Check if there is an existing delivery associated with the sale.
            $delivery = Delivery::where('sale_id', $saleId)->first();

            // Generate delivery data based on whether an existing delivery is found.
            $deliveryData = $delivery
                ? DeliveryCreateDTO::fromExistingDelivery($delivery, $customerSale)
                : DeliveryCreateDTO::fromNewDelivery($customerSale);

            // Return the prepared delivery data as an array.
            return [
                $deliveryData->referenceNo,
                $deliveryData->saleReference,
                $deliveryData->status,
                $deliveryData->deliveredBy,
                $deliveryData->recievedBy,
                $deliveryData->customerName,
                $deliveryData->address,
                $deliveryData->note,
                $deliveryData->courierId,
            ];
        } catch (\Exception $e) {
            // Log the error for debugging and throw a generic exception message for users.
            Log::error("Error fetching delivery details: " . $e->getMessage());
            throw new \Exception("Failed to load delivery details.");
        }
    }


    /**
     * Stores a new delivery or updates an existing one based on the provided delivery data.
     * Handles file uploads and sends an approval notification email upon successful creation.
     *
     * @param DeliveryDTO $dto The data transfer object containing delivery details.
     * @return string A success message indicating the result of the operation.
     * @throws \Exception If an error occurs during the operation.
     */
    public function storeDelivery(DeliveryDTO $dto): string
    {
        try {
            // Find an existing delivery by reference number or create a new one.
            $delivery = Delivery::firstOrNew(['reference_no' => $dto->reference_no]);

            // Save the file using Spatie Media Library if a file is provided in the DTO.
            if ($dto->file) {
                $this->mediaService->addDocument($delivery, $dto->file, "delivery_documents");
            }

            // Fill in the delivery details and save the record in the database.
            $delivery->fill([
                'sale_id' => $dto->sale_id,
                'user_id' => Auth::id(),
                'courier_id' => $dto->courier_id,
                'address' => $dto->address,
                'delivered_by' => $dto->delivered_by,
                'recieved_by' => $dto->recieved_by,
                'status' => $dto->status,
                'note' => $dto->note,
            ])->save();

            // Prepare email data and send an approval notification email.
            $mailData = $this->prepareEmailData($dto->sale_id, $delivery);
            $message = $this->sendMailAction->sendMail($mailData, DeliveryDetails::class);

            // Return the success message.
            return $message;
        } catch (\Exception $e) {
            // Log the error and throw a generic exception message for users.
            Log::error("Error creating delivery: " . $e->getMessage());
            throw new \Exception("An error occurred while processing the request, please try again later.");
        }
    }


    /**
     * Retrieves the details of a specific delivery by its ID.
     * Includes related data such as sales and customer details to reduce unnecessary queries.
     * Returns an array with the delivery's information.
     *
     * @param int $id The ID of the delivery.
     * @return array An array containing the delivery details.
     * @throws \Exception If an error occurs while fetching delivery data.
     */
    public function getDeliveryDetails(int $id): array
    {
        try {
            // Fetch delivery details along with related sale and customer information.
            $delivery = Delivery::with(['sale.customer:id,name']) // Use eager loading to improve performance.
            ->findOrFail($id);

            // Prepare and return delivery data in the required format.
            return [
                $delivery->reference_no,
                $delivery->sale->reference_no,
                $delivery->status,
                $delivery->delivered_by,
                $delivery->recieved_by,
                $delivery->sale->customer->name,
                $delivery->address,
                $delivery->note,
                $delivery->courier_id,
            ];
        } catch (\Exception $e) {
            // Log the error and throw a user-friendly exception.
            Log::error("Error fetching delivery details: " . $e->getMessage());
            throw new \Exception("Failed to retrieve delivery details.");
        }
    }


    /**
     * Updates the details of a specific delivery record based on the provided data transfer object.
     * Handles transactions to ensure data consistency and reliability during the update process.
     *
     * @param DeliveryEditDTO $dto The data transfer object containing updated delivery details.
     * @return string A success message indicating the outcome of the update operation.
     * @throws \Exception If an error occurs while updating the delivery.
     */
    public function updateDelivery(DeliveryEditDTO $dto): string
    {
        DB::beginTransaction(); // Start a database transaction.
        try {
            // Fetch the delivery record and preload related sale and customer data.
            $delivery = Delivery::with('sale.customer')->findOrFail($dto->deliveryId);

            // Handle file uploads using Spatie Media Library, replacing any existing document.
//            if ($dto->file) {
//                $this->mediaService->uploadDocumentWithClear($delivery, $dto->file, "delivery_documents");
//            }

            // Update the delivery record with new details from the DTO.
            $delivery->update([
                'reference_no' => $dto->referenceNo,
                'status' => $dto->status,
                'courier_id' => $dto->courierId,
                'delivered_by' => $dto->deliveredBy,
                'recieved_by' => $dto->recievedBy,
                'address' => $dto->address,
                'note' => $dto->note,
            ]);

            // Prepare and send an approval notification email after the update.
            $mailData = $this->prepareEmailData(null, $delivery);
            $message = $this->sendMailAction->sendMail($mailData, DeliveryDetails::class);

            DB::commit(); // Commit the transaction after a successful update.
            return 'Delivery updated successfully'; // Return success message.
        } catch (\Exception $e) {
            DB::rollBack(); // Roll back the transaction if an error occurs.
            Log::error("Error updating delivery: " . $e->getMessage());
            throw new \Exception("Failed to update delivery details."); // Throw a user-friendly exception.
        }
    }


    /**
     * Prepares the email data required for sending an approval notification.
     * Fetches customer and sale data either from the provided sale ID or the delivery object.
     *
     * @param int|null $saleId The ID of the sale. If null, the sale is fetched from the delivery.
     * @param object $delivery The delivery object containing related data.
     * @return array An associative array with email data including customer, sale, and delivery details.
     */
    #[ArrayShape([
        'email' => "\Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed",
        'customer' => "\Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed",
        'sale_reference' => "\Illuminate\Database\Eloquent\HigherOrderBuilderProxy|\Illuminate\Support\HigherOrderCollectionProxy|mixed",
        'delivery_reference' => "string",
        'status' => "string",
        'address' => "string",
        'delivered_by' => "string"
    ])]
    private function prepareEmailData($saleId, $delivery): array
    {
        // Fetch sale and customer data efficiently using eager loading.
        if ($saleId !== null) {
            // Fetch sale by ID if provided, with customer data preloaded.
            $sale = Sale::with('customer')->findOrFail($saleId);
        } else {
            // Use the sale associated with the delivery if sale ID is null.
            $sale = $delivery->sale;
        }

        // Retrieve customer details from the sale.
        $customer = $sale->customer;

        // Prepare and return the email data array.
        return [
            'email' => $customer->email,
            'customer' => $customer->name,
            'sale_reference' => $sale->reference_no,
            'delivery_reference' => $delivery->reference_no,
            'status' => $delivery->status,
            'address' => $delivery->address,
            'delivered_by' => $delivery->delivered_by,
        ];
    }


    /**
     * Deletes multiple deliveries based on the provided array of delivery IDs.
     * Handles related documents before deletion and ensures proper error handling.
     *
     * @param array $deliveryId An array of delivery IDs to delete.
     * @return bool True if the operation succeeds, otherwise throws an exception.
     * @throws Exception If an error occurs during deletion.
     */
    public function deleteDelivers(array $deliveryId): bool
    {
        try {
            // Fetch all deliveries matching the provided IDs.
            $deliveries = Delivery::whereIn('id', $deliveryId)->get();

            // Iterate through each delivery to delete associated documents.
            foreach ($deliveries as $delivery) {
                $this->mediaService->deleteDocument($delivery, 'delivery_documents');
            }

            // Delete the deliveries from the database.
            Delivery::whereIn('id', $deliveryId)->delete();

            return true; // Return true upon successful deletion.
        } catch (ModelNotFoundException $e) {
            // Log an error if any delivery is not found and rethrow the exception.
            Log::error('Delivery not found: ' . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            // Log any general errors and rethrow the exception.
            Log::error('Error deleting Delivery: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Deletes a single delivery based on its ID.
     * Ensures proper error handling and logging for missing or failed deletions.
     *
     * @param int $id The ID of the delivery to delete.
     * @return bool True if the operation succeeds, otherwise throws an exception.
     * @throws Exception If an error occurs during deletion.
     */
    public function deleteDelivery(int $id): bool
    {
        try {
            // Fetch the delivery by its ID.
            $delivery = Delivery::findOrFail($id);

            // Optional: Delete associated documents (commented out for now).
            // $this->mediaService->deleteDocument($delivery, 'delivery_documents');

            // Delete the delivery record from the database.
            $delivery->delete();

            return true; // Return true upon successful deletion.
        } catch (ModelNotFoundException $e) {
            // Log an error if the delivery is not found and rethrow the exception.
            Log::error('Delivery not found: ' . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            // Log any general errors and rethrow the exception.
            Log::error('Error deleting Delivery: ' . $e->getMessage());
            throw $e;
        }
    }

}

