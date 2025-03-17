<?php

namespace App\Services\Tenant;

use App\Actions\SendMailAction;
use App\DTOs\ProductSaleDTO;
use App\DTOs\PurchaseProductDTO;
use App\DTOs\PurchaseReturnDTO;
use App\DTOs\ReturnProductPurchaseDTO;
use App\DTOs\ReturnPurchaseEditDTO;
use App\DTOs\ReturnUpdateDTO;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\ReturnPurchaseNotFoundException;
use App\Mail\ReturnDetails;
use App\Models\Account;
use App\Models\Biller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Product_Sale;
use App\Models\Product_Warehouse;
use App\Models\ProductBatch;
use App\Models\ProductPurchase;
use App\Models\ProductReturn;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\PurchaseProductReturn;
use App\Models\ReturnPurchase;
use App\Models\Returns;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReturnPurchaseService
{

    protected WarehouseService $warehouseService;
    protected UnitService $unitService;
    protected TaxCalculatorService $taxService;
    protected MediaService $mediaService;
    protected SendMailAction $sendMailAction;
    protected AccountService $accountService;
    protected SupplierService $supplierService;
    protected ProductBatchService $batchService;


    public function __construct(
        WarehouseService     $warehouseService,
        UnitService          $unitService,
        TaxCalculatorService $taxService,
        MediaService         $mediaService,
        SendMailAction       $sendMailAction,
        AccountService       $accountService,
        SupplierService      $supplierService,
        ProductBatchService  $batchService
    )
    {
        $this->warehouseService = $warehouseService;
        $this->unitService = $unitService;
        $this->taxService = $taxService;
        $this->mediaService = $mediaService;
        $this->sendMailAction = $sendMailAction;
        $this->accountService = $accountService;
        $this->supplierService = $supplierService;
        $this->batchService = $batchService;
    }

    /**  ---------------------------- Start index --------------------------------- */
    /**
     * Retrieves a list of return purchases based on the given warehouse ID and date range.
     * The results are filtered based on the user’s role and access settings.
     *
     * @param int $warehouse_id The warehouse ID to filter the return purchases by.
     * @param string $starting_date The start date for filtering the return purchases.
     * @param string $ending_date The end date for filtering the return purchases.
     * @return \Illuminate\Database\Eloquent\Collection A collection of ReturnPurchase objects.
     * @throws \Exception If there is an error in retrieving the return purchases.
     */
    public function getReturnPurchases($warehouse_id, $starting_date, $ending_date)
    {
        try {
            $user = Auth::guard('web')->user();

            // Fetch return purchases with related models, filtered by date range, warehouse, and user access.
            return ReturnPurchase::with(['supplier', 'warehouse', 'user', 'currency', 'purchase'])
                ->whereBetween('created_at', [$starting_date, $ending_date])
                ->when(!$user->hasRole(['Admin', 'Owner']), function ($query, $user) {
                    // Role-based filtering for non-admin users based on configuration settings.
                    if (config('staff_access') === 'own') {
                        $query->where('user_id', $user->id);
                    } elseif (config('staff_access') === 'warehouse') {
                        $query->where('warehouse_id', $user->warehouse_id);
                    }
                })
                ->when($warehouse_id != 0, fn($query) => $query->where('warehouse_id', $warehouse_id))
                ->get();
        } catch (\Exception $e) {
            Log::error("Error in Return Purchase index: " . $e->getMessage());
            throw new \Exception("Error get Return Purchase 'index' ");
        }
    }

    /**
     * Formats a collection of return purchases into a simplified array with necessary details.
     *
     * @param \Illuminate\Support\Collection $returnPurchases The collection of ReturnPurchase objects.
     * @return \Illuminate\Support\Collection A collection of formatted return purchase data.
     */
    public function formatReturnPurchases(Collection $returnPurchases): Collection
    {
        return $returnPurchases->map(function ($returns, $key) {
            return [
                'id'                => $returns->id,
                'key'               => $key,
                'date'              => $returns->created_at->format(config('date_format')),
                'reference_no'      => $returns->reference_no,
                'warehouse'         => $returns->warehouse->name ?? 'N/A',
                'purchase_reference'=> $returns->purchase?->reference_no ?? 'N/A',
                'supplier'          => $returns->supplier?->name ?? 'N/A',
                'grand_total'       => number_format($returns->grand_total, config('decimal')),
                'return'            => [
                    'id'             => $returns->id,
                    'date'           => $returns->created_at->format(config('date_format')),
                    'exchange_rate'  => $returns->exchange_rate,
                    'reference_no'   => $returns->reference_no,
                    'warehouse'      => [
                        'name'      => $returns->warehouse->name ?? 'N/A',
                        'phone'     => $returns->warehouse->phone ?? 'N/A',
                        'address'   => nl2br($returns->warehouse->address ?? 'N/A'),
                    ],
                    'supplier'       => [
                        'name'         => $returns->supplier?->name ?? 'N/A',
                        'company_name' => $returns->supplier?->company_name ?? 'N/A',
                        'email'        => $returns->supplier?->email ?? 'N/A',
                        'phone'        => $returns->supplier?->phone_number ?? 'N/A',
                        'address'      => nl2br($returns->supplier?->address ?? 'N/A'),
                        'city'         => $returns->supplier?->city ?? 'N/A',
                    ],
                    'total_tax'      => $returns->total_tax,
                    'total_discount' => $returns->total_discount,
                    'total_cost'     => $returns->total_cost,
                    'order_tax'      => [
                        'value' => $returns->order_tax,
                        'rate'  => $returns->order_tax_rate,
                    ],
                    'grand_total'    => $returns->grand_total,
                    'return_note'    => nl2br($returns->return_note ?? ''),
                    'staff_note'     => nl2br($returns->staff_note ?? ''),
                    'user'           => [
                        'name'  => $returns->user->name ?? 'N/A',
                        'email' => $returns->user->email ?? 'N/A',
                    ],
                    'currency'       => $returns->currency?->code ?? 'N/A',
                ]
            ];
        });
    }

    /** Start Create */
    /**
     * Fetches data required to create a return purchase, including related purchase products and other necessary data.
     *
     * @param string $referenceNo The reference number of the purchase for which the return is to be created.
     * @return array An array containing the purchase, warehouses, taxes, accounts, and products related to the return purchase.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the purchase with the given reference number is not found.
     */
    public function getCreateData(string $referenceNo): array
    {
        // Retrieve purchase based on reference number.
        $purchase = Purchase::select('id')->where('reference_no', $referenceNo)->first();

        if (!$purchase) {
            throw new ModelNotFoundException("Reference No: {$referenceNo} not found.");
        }

        // Fetch related product purchase details.
        $productPurchases = ProductPurchase::where('purchase_id', $purchase->id)
            ->with(['product', 'productVariant', 'productBatch'])
            ->get()
            ->map(fn($productPurchase) => PurchaseProductDTO::fromModel($productPurchase));

        return [
            'purchase'   => $purchase,
            'warehouses' => Warehouse::limit(50)->get(), // Limit the warehouses to 50.
            'taxes'      => $this->taxService->getTaxes(),
            'accounts'   => $this->accountService->getActiveAccounts(),
            'products'   => $productPurchases
        ];
    }




    /** ------------------- Start Store  -----------------------*/
    /**
     * Creates a return purchase based on the provided data and purchase details.
     *
     * This method handles the full process of creating a return purchase, including:
     * 1. Retrieving purchase details using the provided purchase ID.
     * 2. Preparing the return purchase data based on the original purchase data.
     * 3. Creating a new return purchase record in the database.
     * 4. Updating the returned products and adjusting the stock accordingly.
     * 5. Sending a return purchase email notification to the appropriate recipient.
     *
     * The method uses a transaction to ensure atomicity, and if any exception occurs, the transaction will be rolled back.
     *
     * @param PurchaseReturnDTO $dto The data transfer object containing information for the return purchase.
     *
     * @return ReturnPurchase The created return purchase record.
     *
     * @throws \Exception If an error occurs during the return purchase creation process.
     */
    public function createReturn(PurchaseReturnDTO $dto): ReturnPurchase
    {
        DB::beginTransaction();
        try {
            // Get purchase details using the purchase ID
            $purchaseData = $this->getPurchaseDetails($dto->data['purchase_id']);

            // Prepare return data by merging with additional details
            $returnData = $this->prepareReturnData($dto->data, $purchaseData);

            // Create the return purchase record
            $returnPurchase = ReturnPurchase::create($returnData);

            // Save document if provided (commented out for now)
            //if ($dto->data['document']) {
            //    $this->mediaService->addDocument($returnPurchase, $dto->data['document'], 'returns');
            //}

            // Update the returned products and adjust stock
            $this->updateReturnedProducts($dto->data, $returnData['warehouse_id'], $dto->data['purchase_id'], $returnPurchase->id);

            // Commit the transaction
            DB::commit();

            // Send email notification for the return purchase
            $this->sendReturnPurchaseEmail($returnPurchase, $purchaseData);

            return $returnPurchase;
        } catch (\Exception $e) {
            // Roll back transaction in case of error
            DB::rollBack();
            Log::error('Error creating return purchase: ' . $e->getMessage());
            throw new \Exception('Failed to process return purchase.');
        }
    }


    /**
     * Prepares the return purchase data by merging original purchase details.
     *
     * This method combines the provided data with details from the original purchase,
     * such as supplier ID, warehouse ID, currency ID, and exchange rate.
     *
     * @param array $data The input data for the return purchase.
     * @param Purchase $purchaseData The original purchase data.
     *
     * @return array The prepared return purchase data.
     */
    private function prepareReturnData(array $data, Purchase $purchaseData): array
    {
        return array_merge($data, [
            'supplier_id'   => $purchaseData->supplier_id,
            'warehouse_id'  => $purchaseData->warehouse_id,
            'currency_id'   => $purchaseData->currency_id,
            'exchange_rate' => $purchaseData->exchange_rate,
        ]);
    }


    /**
     * Sends an email notification for the created return purchase.
     *
     * This method sends an email containing details about the return purchase,
     * such as the supplier's name, reference number, total quantity, total cost,
     * and warehouse information. It uses an action to execute the email sending process.
     *
     * @param ReturnPurchase $returnPurchase The created return purchase record.
     * @param Purchase $purchaseData The original purchase data.
     *
     * @return void
     */
    private function sendReturnPurchaseEmail(ReturnPurchase $returnPurchase, Purchase $purchaseData)
    {
        $emailData = [
            'supplier_name' => $purchaseData->supplier->name ?? 'N/A',
            'reference_no' => $returnPurchase->reference_no,
            'total_qty' => $returnPurchase->total_qty,
            'total_cost' => $returnPurchase->total_cost,
            'warehouse' => $purchaseData->warehouse->name ?? 'N/A',
            'date' => now()->toDateTimeString(),
        ];

        if (!$this->sendMailAction->execute($emailData, ReturnDetails::class)) {
            Log::warning('Failed to send return purchase email.');
        }
    }


    /**
     * Retrieves the purchase details for a given purchase ID.
     *
     * This method fetches the purchase record and loads its related supplier and warehouse
     * information. If the purchase record is not found, it throws an exception.
     *
     * @param int $purchaseId The ID of the purchase to retrieve.
     *
     * @return Purchase The purchase record.
     *
     * @throws \Exception If the purchase with the provided ID is not found.
     */
    private function getPurchaseDetails($purchaseId)
    {
        try {
            // Retrieve the purchase details without loading relationships initially
            $purchaseData = Purchase::select('id', 'warehouse_id', 'supplier_id', 'currency_id', 'exchange_rate', 'reference_no')
                ->find($purchaseId);

            // If no purchase found, throw an exception
            if (!$purchaseData) {
                throw new \Exception("Purchase with ID $purchaseId not found.");
            }

            // Load relationships after retrieving the record
            $purchaseData->load('supplier', 'warehouse');

            return $purchaseData;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw $e; // Re-throw the exception after logging
        }
    }


    /**
     * Updates the returned products by processing each product and updating related records.
     *
     * This function handles the entire process of updating returned products, including:
     * 1. Loading the products in bulk using their IDs.
     * 2. Validating the existence of each product.
     * 3. Handling the purchase unit and calculating the quantity of returned products.
     * 4. Managing product variants (if any) and decrementing their quantity.
     * 5. Registering the returned product details in the database.
     * 6. Updating the stock for each product based on the warehouse ID.
     *
     * @param array $data The input data containing product details such as product ID, quantity,
     *                    product code, unit, etc.
     * @param int $warehouseId The ID of the warehouse where the stock should be updated.
     * @param int $purchaseId The ID of the associated purchase record.
     * @param int $returnPurchaseId The ID of the return purchase record.
     *
     * @return void
     *
     * @throws ProductNotFoundException If a product is not found based on its ID.
     * @throws \Exception If a product variant is not found for the given product ID and product code.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the related unit or product is not found.
     */
    private function updateReturnedProducts(array $data, int $warehouseId, int $purchaseId, int $returnPurchaseId)
    {
        $productIds = $data['product_id'];

        // Load products in bulk
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // Process each product
        foreach ($productIds as $index => $productId) {
            $product = $products[$productId] ?? null;

            // Ensure the product exists
            if (!$product) {
                throw new ProductNotFoundException("Product with ID $productId not found.");
            }

            // Fetch the appropriate purchase unit
            $purchaseUnit = Unit::where('unit_name', $data['purchase_unit'][$index])->firstOrFail();

            // Calculate the returned quantity using the CalculationsService
            $quantity = CalculationsService::calculate($data['qty'][$index], $purchaseUnit);

            // Handle variants (if any)
            $variantId = $this->handleProductVariants($product, $data, $index, $quantity);

            // Register the returned product
            $this->registerReturnedProduct($data, $index, $purchaseId, $returnPurchaseId, $productId, $purchaseUnit, $variantId, $quantity);

            // Update stock for the product
            $this->updateStock($product, $variantId, $data, $index, $warehouseId, $quantity);
        }
    }


    /**
     * Handles product variants by decrementing their stock quantity if applicable.
     *
     * This function checks if the product has variants, finds the exact variant using the product code,
     * decrements its quantity, and returns the variant ID. If no variant is found, an exception is thrown.
     *
     * @param Product $product The product for which the variant is to be processed.
     * @param array $data The input data containing product details.
     * @param int $index The index of the current product in the input data.
     * @param int $quantity The quantity of the returned product.
     *
     * @return int|null The variant ID if a variant is found, null otherwise.
     *
     * @throws \Exception If the variant is not found for the given product ID and product code.
     */
    private function handleProductVariants($product, array $data, int $index, int $quantity)
    {
        $variantId = null;
        if ($product->is_variant) {
            // Find the variant based on product code
            $variant = ProductVariant::FindExactProductWithCode($product->id, $data['product_code'][$index])->first();
            if (!$variant) {
                throw new \Exception("Variant not found for product ID {$product->id} and code {$data['product_code'][$index]}");
            }
            $variantId = $variant->variant_id;
            // Decrease the quantity of the variant
            $variant->decrement('qty', $quantity);
        }
        return $variantId;
    }


    /**
     * Registers a returned product in the purchase return records.
     *
     * This function creates a new entry in the PurchaseProductReturn table,
     * recording the details of the returned product such as quantity, batch ID,
     * variant ID, IMEI number, and other financial details like net unit cost,
     * discount, tax rate, and total amount. It is called when a product is returned
     * as part of a purchase return process.
     *
     * @param array $data The input data containing product details such as batch ID,
     *                    IMEI number, unit cost, discount, tax rate, etc.
     * @param int $index The index of the current product in the input data.
     * @param int $purchaseId The ID of the associated purchase record.
     * @param int $returnPurchaseId The ID of the return purchase record.
     * @param int $productId The ID of the product being returned.
     * @param mixed $purchaseUnit The purchase unit associated with the returned product.
     * @param int|null $variantId The ID of the product variant, if applicable, null otherwise.
     * @param int $quantity The quantity of the returned product.
     *
     * @return void
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the related product or purchase record is not found.
     */
    private function registerReturnedProduct(array $data, int $index, int $purchaseId, int $returnPurchaseId, int $productId, $purchaseUnit, $variantId, int $quantity)
    {
        PurchaseProductReturn::create([
            'return_id'        => $returnPurchaseId,
            'purchase_id'      => $purchaseId,
            'product_id'       => $productId,
            'product_batch_id' => $data['product_batch_id'][$index] ?? null,
            'variant_id'       => $variantId,
            'imei_number'      => $data['imei_number'][$index] ?? null,
            'qty'              => $quantity,
            'purchase_unit_id' => $purchaseUnit->id,
            'net_unit_cost'    => $data['net_unit_cost'][$index],
            'discount'         => $data['discount'][$index],
            'tax_rate'         => $data['tax_rate'][$index],
            'tax'              => $data['tax'][$index],
            'total'            => $data['subtotal'][$index],
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }


    /**
     * Updates the stock quantity of a product or variant in the warehouse.
     *
     * This function handles the reduction in stock based on the type of product:
     * - If the product has variants, it will decrement the stock of the variant.
     * - If the product is part of a batch, it will decrement the stock of the corresponding batch.
     * - If neither a variant nor a batch is involved, it will decrement the stock of the product directly in the warehouse.
     *
     * @param Product $product The product whose stock is to be updated.
     * @param int|null $variantId The ID of the variant if applicable, null otherwise.
     * @param array $data The input data containing relevant fields like product batch IDs.
     * @param int $index The index of the current product in the data array.
     * @param int $warehouseId The ID of the warehouse where the stock is being updated.
     * @param int $quantity The quantity to decrement from the stock.
     *
     * @return void
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the related product, variant, or batch is not found.
     */
    private function updateStock(Product $product, ?int $variantId, array $data, int $index, int $warehouseId, int $quantity)
    {
        if ($variantId) {
            // Decrement stock for the specific product variant
            ProductVariant::where('variant_id', $variantId)
                ->where('product_id', $product->id)
                ->decrement('qty', $quantity);
        } elseif (!empty($data['product_batch_id'][$index])) {
            // Decrement stock for the specific product batch
            ProductBatch::where('id', $data['product_batch_id'][$index])->decrement('qty', $quantity);
        } else {
            // Decrement stock for the product in the warehouse
            Product_Warehouse::FindProductWithoutVariant($product->id, $warehouseId)
                ->firstOrFail()
                ->decrement('qty', $quantity);
        }
    }



    /** ------------------ Start Edite ---------------------------*/
    /**
     * Retrieves detailed return purchase data for a specific return ID.
     *
     * This method fetches the return purchase details along with related data such as:
     * - Supplier information
     * - Warehouse information
     * - Account details
     * - Product returns, including related product, variant, tax, unit, and batch data.
     *
     * It maps the product return data into a structured ReturnProductPurchaseDTO to provide a more organized and manageable output.
     *
     * The method handles exceptions to ensure appropriate error handling and logging for failed retrievals or missing data.
     *
     * @param int $id The ID of the return purchase to retrieve.
     * @return array The array containing all relevant return purchase data, including:
     * - 'lims_supplier_list' - The list of suppliers.
     * - 'lims_warehouse_list' - The list of warehouses.
     * - 'lims_tax_list' - The list of tax rates.
     * - 'lims_account_list' - The list of active accounts.
     * - 'lims_return_data' - The return purchase details.
     * - 'lims_product_return_data' - The processed product return data as DTO.
     *
     * @throws ReturnPurchaseNotFoundException If the return purchase with the specified ID is not found.
     * @throws \Exception If there is an error retrieving the return purchase data.
     */
    public function getReturnData(int $id): array
    {
        try {
            $returnPurchase = ReturnPurchase::with(['supplier', 'warehouse', 'account'])->findOrFail($id);

            $productReturns = PurchaseProductReturn::where('return_id', $id)->get();

            if (!$returnPurchase) {
                throw new ReturnPurchaseNotFoundException("Return purchase ID {$id} not found.");
            }

            $productIds = $productReturns->pluck('product_id')->unique();
            $variantIds = $productReturns->pluck('variant_id')->unique()->filter();
            $taxRates   = $productReturns->pluck('tax_rate')->unique();
            $batchIds   = $productReturns->pluck('product_batch_id')->unique()->filter();

            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
            $variants = ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');
            $taxes    = Tax::whereIn('rate', $taxRates)->get()->keyBy('rate');
            $batches  = ProductBatch::whereIn('id', $batchIds)->get()->keyBy('id');
            $units    = Unit::whereIn('id', $products->pluck('unit_id')->unique())->get()->keyBy('id');

            $processedReturns = $productReturns->map(fn($productReturn) => ReturnProductPurchaseDTO::fromModel($productReturn, $products, $variants, $taxes, $units, $batches)
            );

            return [
                'lims_supplier_list' => $this->supplierService->getSupplier(),
                'lims_warehouse_list' => $this->warehouseService->getWarehouses(),
                'lims_tax_list' => $this->taxService->getTaxes(),
                'lims_account_list' => $this->accountService->getActiveAccounts(),
                'lims_return_data' => $returnPurchase,
                'lims_product_return_data' => $processedReturns
            ];
        } catch (ReturnPurchaseNotFoundException $e) {
            Log::warning("Return purchase not found: " . $e->getMessage());
            throw $e;
        } catch (\Throwable $e) {
            Log::error("Error in getReturnData: " . $e->getMessage());
            throw new \Exception("Failed to fetch return purchase data.");
        }
    }



    /** ------------------ Start Update --------------------------- */
    /**
     * Updates an existing return purchase along with the associated product returns.
     *
     * This method handles the update of the return purchase details as well as the individual
     * product returns. It first restores the stock of any previously returned products, then
     * processes the new or modified product returns by either updating existing records or
     * creating new ones.
     *
     * After updating the product returns, it proceeds to update the general return purchase
     * details such as warehouse, supplier, total quantities, and costs.
     *
     * The method is wrapped in a database transaction to ensure data integrity. In case of any
     * failure, the transaction is rolled back and the error is logged for further investigation.
     *
     * @param ReturnPurchaseEditDTO $data The data transfer object containing the updated information
     *                                   for the return purchase and its associated products.
     *
     * @return ReturnPurchase The updated return purchase instance.
     *
     * @throws \Exception If an error occurs during the update process.
     */
    public function update(ReturnPurchaseEditDTO $data)
    {
        DB::beginTransaction();

        try {
            // Fetch the existing return purchase to update
            $returnPurchase = ReturnPurchase::findOrFail($data->id);

            // Restore the stock for the old return products and delete them
            $oldReturnProducts = PurchaseProductReturn::where('return_id', $data->id)->get();

            foreach ($oldReturnProducts as $oldProduct) {
                $this->restoreStock($oldProduct, $returnPurchase->warehouse_id);
                $oldProduct->delete();
            }

            // Process new or updated product returns
            foreach ($data->products as $product) {
                $existingProductReturn = PurchaseProductReturn::where('return_id', $data->id)
                    ->where('product_id', $product['product_id'])
                    ->where('variant_id', $product['variant_id'])
                    ->where('product_batch_id', $product['batch_id'])
                    ->first();

                if ($existingProductReturn) {
                    // Update product return if it already exists
                    $this->updateProductReturn($existingProductReturn, $product, $data->warehouse_id);
                } else {
                    // Create new product return if it does not exist
                    $this->processProductReturn($product, $data->warehouse_id, $data->id);
                }
            }

            // Update the return purchase record with the new data
            $returnPurchase->update([
                'warehouse_id' => $data->warehouse_id,
                'supplier_id' => $data->supplier_id,
                'account_id' => $data->account_id,
                'total_qty' => $data->total_qty,
                'total_discount' => $data->total_discount,
                'total_tax' => $data->total_tax,
                'total_cost' => $data->total_cost,
                'order_tax' => $data->order_tax,
                'grand_total' => $data->grand_total,
                'return_note' => $data->return_note,
                'staff_note' => $data->staff_note,
            ]);

            DB::commit();

            return $returnPurchase;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating return purchase: ' . $e->getMessage());
            throw new \Exception('An error occurred while updating the return purchase: ' . $e->getMessage());
        }
    }

    /**
     * Updates the product return record in the PurchaseProductReturn table.
     *
     * This method calculates the difference between the old and new quantity for a product return
     * and updates the return record accordingly. It also updates the stock based on the difference
     * in quantities.
     *
     * @param PurchaseProductReturn $existingProductReturn The existing product return record to update.
     * @param array $product The new product return data.
     * @param int $warehouse_id The warehouse ID where the product stock should be updated.
     *
     * @return void
     */
    private function updateProductReturn($existingProductReturn, $product, $warehouse_id)
    {
        // Calculate the difference between the old and new quantity
        $oldQuantity = $existingProductReturn->qty;
        $newQuantity = $product['qty'];
        $quantityDifference = $newQuantity - $oldQuantity;

        // Update the product return record with new data
        $existingProductReturn->update([
            'qty' => $newQuantity,
            'purchase_unit_id' => Unit::where('unit_name', $product['purchase_unit'])->value('id'),
            'net_unit_cost' => $product['net_unit_cost'],
            'discount' => $product['discount'],
            'tax_rate' => $product['tax_rate'],
            'tax' => $product['tax'],
            'total' => $product['subtotal'],
            'imei_number' => $product['imei_number'],
        ]);

        // Update stock based on the quantity difference
        $this->updateEditStock($existingProductReturn, $quantityDifference, $warehouse_id);
    }

    /**
     * 📌 Updates stock based on the difference between old and new quantities.
     *
     * This method adjusts the inventory based on the quantity difference (positive or negative) for a given product return.
     * It also handles the restoration of IMEI numbers and updates the product batch if the product belongs to a batch.
     *
     * @param  \App\Models\PurchaseProductReturn  $productReturn  The product return record to update.
     * @param  int  $quantityDifference  The difference in quantity (can be positive or negative).
     * @param  int  $warehouse_id  The ID of the warehouse where the stock will be adjusted.
     * @return void
     */
    private function updateEditStock($productReturn, $quantityDifference, $warehouse_id)
    {
        $product = Product::find($productReturn->product_id);
        if (!$product) return;

        $quantity = $this->unitService->convertToBaseUnit(abs($quantityDifference), $productReturn->purchase_unit_id);

        // If the difference is positive (quantity increased)
        if ($quantityDifference > 0) {
            $product->increment('qty', $quantity);
            Product_Warehouse::FindProductWithoutVariant($productReturn->product_id, $warehouse_id)
                ->increment('qty', $quantity);

            // Update IMEI numbers if the product has IMEI and the IMEI numbers are available
            if ($product->is_imei && $productReturn->imei_number) {
                $this->restoreIMEINumbers(Product_Warehouse::FindProductWithoutVariant($productReturn->product_id, $warehouse_id), $productReturn->imei_number);
            }

            // Update product batch if the product belongs to a batch
            if ($productReturn->product_batch_id) {
                $this->batchService->updateProductBatchQuantity($productReturn->product_batch_id, $warehouse_id, $quantity, 'increment');
            }
        }
        // If the difference is negative (quantity decreased)
        else if ($quantityDifference < 0) {
            $product->decrement('qty', $quantity);
            Product_Warehouse::FindProductWithoutVariant($productReturn->product_id, $warehouse_id)
                ->decrement('qty', $quantity);

            // Update product batch if the product belongs to a batch
            if ($productReturn->product_batch_id) {
                $this->batchService->updateProductBatchQuantity($productReturn->product_batch_id, $warehouse_id, $quantity, 'decrement');
            }
        }
    }

    /**
     * 📌 Restores stock when an old return is deleted.
     *
     * This method restores the stock for a product when an old return is deleted. It increases the stock in the product table,
     * product warehouse, and the product variant if applicable. Additionally, it restores IMEI numbers and updates the
     * product batch if the product is part of a batch.
     *
     * @param  \App\Models\PurchaseProductReturn  $productReturn  The product return to restore stock for.
     * @param  int  $warehouse_id  The ID of the warehouse to adjust stock in.
     * @return void
     */
    private function restoreStock($productReturn, $warehouse_id)
    {
        $product = Product::find($productReturn->product_id);
        if (!$product) return;

        $quantity = $this->unitService->convertToBaseUnit($productReturn->qty, $productReturn->purchase_unit_id);
        $product->increment('qty', $quantity);

        if ($productReturn->variant_id) {
            ProductVariant::find($productReturn->variant_id)?->increment('qty', $quantity);
        }

        $productWarehouse = Product_Warehouse::FindProductWithoutVariant($productReturn->product_id, $warehouse_id);
        $productWarehouse->increment('qty', $quantity);

        // Restore IMEI numbers to the stock if available
        if ($product->is_imei && $productReturn->imei_number) {
            $this->restoreIMEINumbers($productWarehouse, $productReturn->imei_number);
        }

        // Update the product batch quantity if applicable
        if ($productReturn->product_batch_id) {
            $this->batchService->updateProductBatchQuantity($productReturn->product_batch_id, $warehouse_id, $quantity, 'increment');
        }
    }

    /**
     * 📌 Handles processing a product return and adds the product to the returns table.
     *
     * This method handles the creation of a product return record in the `PurchaseProductReturn` table. It also decreases
     * the stock of the product in the warehouse and updates the product batch if the product belongs to a batch.
     * The quantity is converted to base unit and stock is adjusted accordingly.
     *
     * @param  array  $product  The product data for the return (including product ID, quantity, etc.).
     * @param  int  $warehouse_id  The warehouse where the stock will be adjusted.
     * @param  int  $return_id  The ID of the return purchase record.
     * @return void
     */
    private function processProductReturn($product, $warehouse_id, $return_id)
    {
        $productModel = Product::findOrFail($product['product_id']);
        $quantity = $this->unitService->convertToBaseUnit($product['qty'], $product['purchase_unit']);

        // Create a new record in PurchaseProductReturn
        $productReturn = new PurchaseProductReturn([
            'return_id' => $return_id,
            'product_id' => $product['product_id'],
            'variant_id' => $product['variant_id'],
            'product_batch_id' => $product['batch_id'],
            'qty' => $product['qty'],
            'purchase_unit_id' => Unit::where('unit_name', $product['purchase_unit'])->value('id'),
            'net_unit_cost' => $product['net_unit_cost'],
            'discount' => $product['discount'],
            'tax_rate' => $product['tax_rate'],
            'tax' => $product['tax'],
            'total' => $product['subtotal'],
            'imei_number' => $product['imei_number'],
        ]);
        $productReturn->save();

        // Decrease stock for the product
        $productModel->decrement('qty', $quantity);
        Product_Warehouse::FindProductWithoutVariant($product['product_id'], $warehouse_id)
            ->decrement('qty', $quantity);

        // Update the product batch quantity if applicable
        if ($product['batch_id']) {
            $this->batchService->updateProductBatchQuantity($product['batch_id'], $warehouse_id, $quantity, 'decrement');
        }
    }

    /**
     * 📌 Restores IMEI Numbers to stock when a product is returned.
     *
     * This method updates the IMEI numbers in the product warehouse record. It ensures that the returned IMEI numbers are
     * added to the warehouse record and any duplicates are removed.
     *
     * @param  \App\Models\Product_Warehouse  $productWarehouse  The warehouse record for the product.
     * @param  string  $imeiNumbers  The IMEI numbers being returned (comma-separated).
     * @return void
     */
    private function restoreIMEINumbers($productWarehouse, $imeiNumbers)
    {
        $existingIMEIs = $productWarehouse->imei_number ? explode(',', $productWarehouse->imei_number) : [];
        $returnedIMEIs = explode(',', $imeiNumbers);

        // Merge existing and returned IMEI numbers, ensuring no duplicates
        $mergedIMEIs = array_unique(array_merge($existingIMEIs, $returnedIMEIs));

        // Update the IMEI numbers in the warehouse record
        $productWarehouse->update(['imei_number' => implode(',', $mergedIMEIs)]);
    }


    /** ------------------------- Delete ------------------------*/

    /**
     * Delete a single return purchase transactionally.
     *
     * @param int $returnIds The ID of the return purchase to be deleted.
     * @return string Success message.
     * @throws \Exception If an error occurs during deletion.
     */
    public function destroy(int $returnIds)
    {
        DB::beginTransaction();
        try {
            // Delete the return purchase and restore stock
            $this->deleteReturnPurchase($returnIds);

            DB::commit();
            return 'Return deleted successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting return purchase "destroy": ' . $e->getMessage());
            throw new \Exception('There was an error processing your request. Please try again.');
        }
    }

    /**
     * Delete multiple return purchases transactionally.
     *
     * @param array $returnIds An array of return purchase IDs to be deleted.
     * @return string Success message.
     * @throws \Exception If an error occurs during deletion.
     */
    public function deleteBySelection(array $returnIds)
    {
        DB::beginTransaction();
        try {
            // Loop through each return ID and delete it
            foreach ($returnIds as $id) {
                $this->deleteReturnPurchase($id);
            }

            DB::commit();
            return 'Return deleted successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting return purchase "deleteBySelection": ' . $e->getMessage());
            throw new \Exception('There was an error processing your request. Please try again.');
        }
    }

    /**
     * Delete a return purchase and restore stock for the returned products.
     *
     * @param int $id The ID of the return purchase to be deleted.
     * @throws \Exception If the product is not found.
     */
    public function deleteReturnPurchase(int $id)
    {
        // Retrieve the return purchase record
        $lims_return_data = ReturnPurchase::findOrFail($id);

        // Get all returned products associated with the return purchase
        $lims_product_return_data = PurchaseProductReturn::where('return_id', $id)->get();

        // Process each returned product
        foreach ($lims_product_return_data as $product_return_data) {
            $this->restoreDeleteStock($product_return_data, $lims_return_data->warehouse_id);
            $product_return_data->delete(); // Delete the return record
        }

        // Delete the return purchase record
        $lims_return_data->delete();

        // Delete associated files (Uncomment if needed)
        // $this->mediaService->deleteDocument($lims_return_data, 'returns');
    }

    /**
     * Restore stock when a return purchase is deleted.
     *
     * @param object $product_return_data The returned product data.
     * @param int $warehouse_id The warehouse ID where the stock should be restored.
     * @throws \Exception If the product is not found.
     */
    private function restoreDeleteStock($product_return_data, $warehouse_id)
    {
        // Retrieve the product data
        $lims_product_data = Product::find($product_return_data->product_id);

        // Throw an exception if the product is not found
        if (!$lims_product_data) {
            throw new \Exception('Product not found for return');
        }

        // Calculate the quantity to be restored
        $quantity = $this->calculateQuantity($product_return_data);

        // Restore stock based on product type (variant, batch, or standard)
        if ($product_return_data->variant_id) {
            $this->restoreProductVariant($product_return_data, $warehouse_id, $quantity);
        } elseif ($product_return_data->product_batch_id) {
            $this->restoreProductBatch($product_return_data, $warehouse_id, $quantity);
        } else {
            $this->restoreProductWithoutVariant($product_return_data, $warehouse_id, $quantity);
        }

        // Update product stock quantity
        $lims_product_data->increment('qty', $quantity);
        $lims_product_data->save();
    }

    /**
     * Calculate the stock quantity to be restored based on the unit's operation.
     *
     * @param object $product_return_data The returned product data.
     * @return float The calculated quantity.
     * @throws \Exception If the purchase unit is not found.
     */
    private function calculateQuantity($product_return_data)
    {
        // Retrieve purchase unit data
        $lims_purchase_unit_data = Unit::find($product_return_data->purchase_unit_id);

        // Throw an exception if the purchase unit is not found
        if (!$lims_purchase_unit_data) {
            throw new \Exception('Purchase unit not found');
        }

        // Calculate the quantity based on unit operation
        if ($lims_purchase_unit_data->operator == '*') {
            return $product_return_data->qty * $lims_purchase_unit_data->operation_value;
        } elseif ($lims_purchase_unit_data->operator == '/') {
            return $product_return_data->qty / $lims_purchase_unit_data->operation_value;
        }

        // Return the original quantity if no operation is defined
        return $product_return_data->qty;
    }

    /**
     * Restore stock for a product with a variant.
     *
     * @param object $product_return_data The returned product data.
     * @param int $warehouse_id The warehouse ID where the stock should be restored.
     * @param float $quantity The quantity to be restored.
     */
    private function restoreProductVariant($product_return_data, $warehouse_id, $quantity)
    {
        // Retrieve product variant and warehouse data
        $lims_product_variant_data = ProductVariant::findExactProduct($product_return_data->product_id, $product_return_data->variant_id);
        $lims_product_warehouse_data = Product_Warehouse::findProductWithVariant($product_return_data->product_id, $product_return_data->variant_id, $warehouse_id);

        // Increment stock quantity in both variant and warehouse records
        $lims_product_variant_data->increment('qty', $quantity);
        $lims_product_variant_data->save();

        $lims_product_warehouse_data->increment('qty', $quantity);
        $lims_product_warehouse_data->save();
    }

    /**
     * Restore stock for a product that has a batch.
     *
     * @param object $product_return_data The returned product data.
     * @param int $warehouse_id The warehouse ID where the stock should be restored.
     * @param float $quantity The quantity to be restored.
     */
    private function restoreProductBatch($product_return_data, $warehouse_id, $quantity)
    {
        // Retrieve product batch data
        $lims_product_batch_data = ProductBatch::find($product_return_data->product_batch_id);

        // Retrieve product warehouse data for the specific batch
        $lims_product_warehouse_data = Product_Warehouse::where([
            ['product_batch_id', $product_return_data->product_batch_id],
            ['warehouse_id', $warehouse_id]
        ])->first();

        // Increment the stock quantity in both batch and warehouse records
        $lims_product_batch_data->increment('qty', $quantity);
        $lims_product_batch_data->save();

        $lims_product_warehouse_data->increment('qty', $quantity);
        $lims_product_warehouse_data->save();
    }

    /**
     * Restore stock for a product without a variant or batch.
     *
     * @param object $product_return_data The returned product data.
     * @param int $warehouse_id The warehouse ID where the stock should be restored.
     * @param float $quantity The quantity to be restored.
     */
    private function restoreProductWithoutVariant($product_return_data, $warehouse_id, $quantity)
    {
        // Retrieve the product warehouse data without variant
        $lims_product_warehouse_data = Product_Warehouse::findProductWithoutVariant($product_return_data->product_id, $warehouse_id);

        // Increment the stock quantity in the warehouse
        $lims_product_warehouse_data->increment('qty', $quantity);
        $lims_product_warehouse_data->save();
    }



}
