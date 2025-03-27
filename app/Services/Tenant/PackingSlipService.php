<?php

namespace App\Services\Tenant;

use App\DTOs\PackingSlipStoreDTO;
use App\DTOs\PackingSlipDTO;
use App\Models\Delivery;
use App\Models\PackingSlip;
use App\Models\PackingSlipProduct;
use App\Models\Product;
use App\Models\Product_Sale;
use App\Models\Product_Warehouse;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\Variant;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;

class PackingSlipService
{
    protected ProductsPackingSlipService $packingSlip;

    public function __construct(ProductsPackingSlipService $packingSlip)
    {
        $this->packingSlip = $packingSlip;
    }

    /**
     * Formats the products associated with a packing slip by appending variant names (if applicable).
     * The function uses caching to optimize repeated lookups for variant names.
     *
     * @param PackingSlip $packingSlip The packing slip containing products to be formatted.
     * @return string A comma-separated string of product names with variants (if any).
     */
    public static function formatProducts(PackingSlip $packingSlip): string
    {
        // Iterate over the products associated with the packing slip
        return $packingSlip->products->map(function ($product) use ($packingSlip) {
            // Use caching to store and retrieve variant names for optimization
            $variantName = Cache::remember("variant_{$packingSlip->id}_{$product->id}", now()->addMinutes(10), function () use ($packingSlip, $product) {
                return PackingSlipProduct::where('packing_slip_id', $packingSlip->id)
                    ->where('product_id', $product->id)
                    ->pluck('variant_id') // Get variant IDs related to the product
                    ->map(fn($variantId) => optional(Variant::find($variantId))->name) // Find variant names based on IDs
                    ->filter() // Filter out null values
                    ->implode(', '); // Combine variant names into a comma-separated string
            });

            // Append variant names to the product name if they exist; otherwise, use the product name only
            return $variantName ? "{$product->name} [{$variantName}]" : $product->name;
        })->implode(', '); // Combine all formatted product names into a single comma-separated string
    }


    /**
     * Retrieves all packing slips and maps them to DTO format for clean data handling.
     * Includes relationships like `sale`, `delivery`, and `products` for each packing slip.
     *
     * @return Collection|array An array of formatted packing slip data as DTOs.
     * @throws Exception If there is an error fetching packing slip data.
     */
    public function getPackingSlips(): Collection|array
    {
        try {
            // Fetch packing slips with related data and order them by descending ID
            $query = PackingSlip::with(['sale', 'delivery', 'products'])
                ->orderByDesc('id')
                ->get();

            // Transform packing slips into DTOs for clean representation
            return $query->map(fn($packingSlip) => PackingSlipDTO::fromModel($packingSlip)->toArray());
        } catch (Exception $e) {
            // Log the error for debugging purposes
            Log::error("Error fetching PackingSlips: {$e->getMessage()}");

            // Throw a more user-friendly exception
            throw new Exception('Error fetching PackingSlips');
        }
    }


    /**
     * Creates a new packing slip, processes products, delivery, and updates related entities.
     * Uses a database transaction to ensure data consistency in case of errors.
     *
     * @param PackingSlipStoreDTO $dto Data Transfer Object containing packing slip details.
     * @return PackingSlip The created packing slip object.
     * @throws Exception If any error occurs during the creation process.
     */
    public function createPackingSlip(PackingSlipStoreDTO $dto): PackingSlip
    {
        DB::beginTransaction(); // Begin a database transaction
        try {
            // Generate a new reference number for the packing slip
            $latestReference = PackingSlip::latest('id')->value('reference_no');
            $reference_no = $latestReference ? $latestReference + 1 : 1001;

            // Fetch the sale data associated with the packing slip
            $sale = Sale::with('customer')->findOrFail($dto->sale_id);

            // Create the packing slip record
            $packingSlip = PackingSlip::create([
                "reference_no" => $reference_no,
                "sale_id" => $dto->sale_id,
                "amount" => $dto->amount,
                "status" => "Pending"
            ]);

            // Handle associated products for the packing slip
            $this->handlePackingSlipProducts($packingSlip, $sale, $dto->is_packing);

            // Handle delivery logic for the packing slip
            $delivery = $this->handleDelivery($sale, $packingSlip);

            // Update packing slip with delivery details
            $packingSlip->update(['delivery_id' => $delivery->id]);

            // Update the sale status to reflect the packing slip creation
            $sale->update(['sale_status' => 5]);

            DB::commit(); // Commit the transaction
            return $packingSlip; // Return the created packing slip
        } catch (Exception $e) {
            DB::rollBack(); // Roll back the transaction in case of errors
            Log::error("Packing Slip Creation Failed: " . $e->getMessage());
            throw new Exception("An error occurred while creating Packing Slip. Please try again later.");
        }
    }


    /**
     * Handles processing of products for a packing slip, updates their status,
     * and adjusts stock quantities.
     *
     * @param PackingSlip $packingSlip The packing slip object.
     * @param Sale $sale The associated sale object.
     * @param array $products Array of product identifiers to be processed.
     * @throws Exception If any error occurs during product processing.
     */
    private function handlePackingSlipProducts(PackingSlip $packingSlip, $sale, array $products)
    {
        foreach ($products as $productInfo) {
            // Split product and variant information
            [$product_id, $variant_id] = explode("|", $productInfo);
            $variant_id = $variant_id ?: ($sale->sale_type == 'online' ? 0 : null);

            // Create packing slip product record
            PackingSlipProduct::create([
                "packing_slip_id" => $packingSlip->id,
                "product_id" => $product_id,
                "variant_id" => $variant_id
            ]);

            // Fetch the associated product sale record
            $productSale = Product_Sale::where([
                ['sale_id', $sale->id],
                ['product_id', $product_id],
                ['variant_id', $variant_id]
            ])->firstOrFail();

            // Update product sale to indicate it's packed
            $productSale->update(['is_packing' => true]);

            // Update stock quantities for the product
            $this->updateStock($product_id, $variant_id, $productSale->qty, $sale->warehouse_id);
        }
    }


    /**
     * Updates stock quantities for a product, handling both combo and standard types.
     *
     * @param int $product_id ID of the product to be updated.
     * @param int|null $variant_id ID of the variant, if applicable.
     * @param int $qty Quantity to decrement from stock.
     * @param int $warehouse_id ID of the warehouse associated with the sale.
     */
    private function updateStock(int $product_id, ?int $variant_id, int $qty, int $warehouse_id)
    {
        // Fetch product details
        $product = Product::findOrFail($product_id);

        // Check if the product is a combo product
        if ($product->type == 'combo') {
            $this->updateComboStock($product, $qty, $warehouse_id);
        } else {
            $this->updateStandardStock($product, $variant_id, $qty, $warehouse_id);
        }
    }


    /**
     * Updates stock quantities for combo products by decrementing child product quantities.
     *
     * @param Product $product The parent product object.
     * @param int $qty Quantity to decrement from combo stock.
     * @param int $warehouse_id ID of the warehouse associated with the sale.
     */
    private function updateComboStock(Product $product, int $qty, int $warehouse_id)
    {
        $productList = explode(",", $product->product_list);
        $qtyList = explode(",", $product->qty_list);
        $variantList = $product->variant_list ? explode(",", $product->variant_list) : [];

        foreach ($productList as $index => $child_id) {
            $child = Product::findOrFail($child_id);
            $childQty = $qty * $qtyList[$index];

            // Decrement variant stock if applicable
            if (isset($variantList[$index]) && $variantList[$index]) {
                ProductVariant::where('product_id', $child_id)
                    ->where('variant_id', $variantList[$index])
                    ->decrement('qty', $childQty);
            }

            // Decrement stock in warehouse
            Product_Warehouse::where('product_id', $child_id)
                ->where('warehouse_id', $warehouse_id)
                ->decrement('qty', $childQty);

            // Decrement total product stock
            $child->decrement('qty', $childQty);
        }
    }


    /**
     * Updates stock quantities for standard products, including variants and warehouses.
     *
     * @param Product $product The product object.
     * @param int|null $variant_id ID of the variant, if applicable.
     * @param int $qty Quantity to decrement from stock.
     * @param int $warehouse_id ID of the warehouse associated with the sale.
     */
    private function updateStandardStock(Product $product, ?int $variant_id, int $qty, int $warehouse_id)
    {
        // Decrement variant stock if applicable
        if ($variant_id) {
            ProductVariant::where('product_id', $product->id)
                ->where('variant_id', $variant_id)
                ->decrement('qty', $qty);
        }

        // Decrement stock in warehouse
        Product_Warehouse::where('product_id', $product->id)
            ->where('warehouse_id', $warehouse_id)
            ->decrement('qty', $qty);

        // Decrement total product stock
        $product->decrement('qty', $qty);
    }


    /**
     * Handles the delivery creation for a given sale and associates the packing slip IDs with the delivery.
     * If a delivery already exists for the sale, it updates the delivery with the new packing slip ID.
     *
     * @param Sale $sale The sale object associated with the packing slip.
     * @param PackingSlip $packingSlip The packing slip object to be linked to the delivery.
     * @return Delivery The created or updated delivery object.
     */
    private function handleDelivery($sale, PackingSlip $packingSlip): Delivery
    {
        // Attempt to find an existing delivery for the given sale
        $delivery = Delivery::where('sale_id', $sale->id)->first();

        if (!$delivery) {
            // Create a new delivery record if none exists
            $delivery = Delivery::create([
                'reference_no' => 'dr-' . now()->format("Ymd-His"), // Generate a unique reference number
                'sale_id' => $sale->id, // Link the delivery to the sale
                'user_id' => auth()->id(), // Set the user creating the delivery
                'address' => $sale->shipping_address ?: $sale->customer->address ?: 'No address available', // Determine delivery address
                'recieved_by' => $sale->shipping_name ?? null, // Set recipient name if available
                'status' => 1, // Set delivery status (e.g., pending)
                'packing_slip_ids' => $packingSlip->id, // Link packing slip ID to delivery
            ]);
        } else {
            // Update existing delivery with additional packing slip ID
            $delivery->update(['packing_slip_ids' => $delivery->packing_slip_ids . ',' . $packingSlip->id]);
        }

        // Return the created or updated delivery object
        return $delivery;
    }


    /**
     * Generates invoice data for a given packing slip.
     * Includes packing slip details, sale details, associated products, and a barcode for the sale reference.
     *
     * @param int $packingSlipId ID of the packing slip to retrieve invoice data for.
     * @return array An array containing invoice data (packing slip, sale, products, and barcode).
     * @throws Exception If the packing slip is not found or any error occurs during data generation.
     */
    #[ArrayShape(['packing_slip' => "mixed", 'sale' => "\Illuminate\Database\Eloquent\HigherOrderBuilderProxy|\Illuminate\Support\HigherOrderCollectionProxy|mixed", 'products' => "array", 'barcode' => "string"])]
    public function generateInvoiceData(int $packingSlipId): array
    {
        try {
            // Fetch packing slip with related sale, customer, and warehouse data
            $packingSlip = PackingSlip::with(['sale.customer', 'sale.warehouse'])->find($packingSlipId);

            if (is_null($packingSlip)) {
                // Throw an exception if the packing slip is not found
                throw new \Exception('Invalid Packing Slip ID');
            }

            $sale = $packingSlip->sale; // Get the associated sale object
            $packingSlipProducts = PackingSlipProduct::where('packing_slip_id', $packingSlipId)->get(); // Fetch related products

            // Map the product data to prepare for invoice generation
            $products = $this->mapProductsData($packingSlipProducts, $sale);

            // Return formatted invoice data as an array
            return [
                'packing_slip' => $packingSlip,
                'sale' => $sale,
                'products' => $products,
                'barcode' => $this->generateBarcode($sale->reference_no), // Generate barcode for sale reference
            ];
        } catch (\Exception $e) {
            // Log the error and throw a user-friendly exception
            Log::error("Error generating invoice data: " . $e->getMessage());
            throw new \Exception("Error generating invoice data.");
        }
    }


    /**
     * Generates a barcode based on the provided reference number.
     *
     * @param string $referenceNo The sale reference number to generate a barcode for.
     * @return string A barcode image string (currently empty placeholder).
     */
    private function generateBarcode(string $referenceNo): string
    {
        // Placeholder for barcode generation; replace with actual implementation if needed
        return ""; // Example: DNS1D::getBarcodePNG($referenceNo, 'C128');
    }


    /**
     * Maps product data for packing slip products, including name, code, quantity, and total price.
     *
     * @param mixed $packingSlipProducts Collection of packing slip products to be processed.
     * @param Sale $sale The associated sale object.
     * @return array An array of mapped product data.
     * @throws Exception If product data cannot be retrieved.
     */
    private function mapProductsData($packingSlipProducts, $sale): array
    {
        return $packingSlipProducts->map(function ($packingSlipProduct) use ($sale) {
            // Fetch product details
            $product = Product::select(['name', 'code'])->find($packingSlipProduct->product_id);

            if (!$product) {
                throw new \Exception('Product not found');
            }

            // If the product has a variant, map the variant details
            if ($packingSlipProduct->variant_id) {
                $this->mapVariantData($packingSlipProduct, $product);
            }

            // Fetch sale product details like quantity and total price
            $saleProduct = Product_Sale::select(['qty', 'total'])
                ->where('sale_id', $sale->id)
                ->where('product_id', $packingSlipProduct->product_id)
                ->first();

            // Return structured product data for invoice generation
            return [
                'name' => $product->name,
                'code' => $product->code,
                'qty' => $saleProduct->qty ?? 0,
                'total' => $saleProduct->total ?? 0,
            ];
        })->toArray();
    }


    /**
     * Updates the product details by appending variant names and updating product codes.
     * This function processes the variant and product variant data for a given packing slip product.
     *
     * @param mixed $packingSlipProduct The packing slip product object to process.
     * @param mixed &$product Reference to the product object, which will be updated with variant data.
     */
    private function mapVariantData($packingSlipProduct, &$product): void
    {
        // Fetch the variant name associated with the packing slip product
        $variant = Variant::select('name')->find($packingSlipProduct->variant_id);

        // Fetch the product variant details (e.g., item code) if variant ID exists
        $productVariant = ProductVariant::select('item_code')
            ->where('product_id', $packingSlipProduct->product_id)
            ->where('variant_id', $packingSlipProduct->variant_id)
            ->first();

        // Append the variant name to the product name if available
        if ($variant) {
            $product->name .= ' [' . $variant->name . ']';
        }

        // Update the product code with the item code from the variant, if applicable
        if ($productVariant) {
            $product->code = $productVariant->item_code;
        }
    }


    /**
     * Deletes a packing slip and performs cleanup of related data.
     * This includes product associations, updating the sale status, and removing delivery records.
     *
     * @param int $id The ID of the packing slip to delete.
     * @throws Exception If any errors occur during the deletion process.
     */
    public function deletePackingSlip(int $id): void
    {
        try {
            // Retrieve the packing slip with associated sale data
            $packingSlip = PackingSlip::with(['sale'])->findOrFail($id);

            // Fetch all products associated with the packing slip
            $packingSlipProducts = PackingSlipProduct::where('packing_slip_id', $id)->get();

            // Throw an exception if no products are found for the packing slip
            if ($packingSlipProducts->isEmpty()) {
                throw new \Exception('No products found for the provided Packing Slip.');
            }

            // Process and delete associated packing slip products
            $this->packingSlip->processPackingSlipProducts($packingSlipProducts, $packingSlip->sale);

            // Update the sale status to reflect the deletion of the packing slip
            $this->updateSaleStatus($packingSlip->sale);

            // Delete the delivery record associated with the sale, if it exists
            Delivery::where('sale_id', $packingSlip->sale_id)->delete();

            // Finally, delete the packing slip record
            $packingSlip->delete();
        } catch (\Exception $e) {
            // Log the error for debugging and throw a user-friendly exception
            Log::error("Error while deleting Packing Slip: " . $e->getMessage());
            throw new \Exception('Error while deleting Packing Slip: ' . $e->getMessage());
        }
    }


    /**
     * Updates the status of a sale, typically used to indicate cancellation or status changes
     * after associated packing slips are deleted.
     *
     * @param mixed $sale The sale object to update.
     */
    private function updateSaleStatus($sale): void
    {
        $sale->sale_status = 2; // Set the sale status to "Cancelled" (2)
        $sale->save(); // Save the updated sale status to the database
    }

    /**
     * الحصول على تفاصيل الـ Packing Slips المرتبطة بـ Challan جديد
     */
    public function getPackingSlipsWithSale(array $packingSlipIds): Collection
    {
        return PackingSlip::with('sale')
            ->where('status', 'Pending')
            ->whereIn('id', $packingSlipIds)
            ->get();
    }

    /**
     * التحقق من وجود Packing Slips صالحة لإنشاء Challan جديد
     */
    public function validatePackingSlips(Collection $packingSlips): bool
    {
        return $packingSlips->isNotEmpty();
    }


    public function updatePackingSlips(array $packingSlipIds, int $courierId)
    {
        PackingSlip::whereIn('id', $packingSlipIds)
            ->update(['status' => 'In Transit']);

        DB::table('deliveries')
            ->whereIn('packing_slip_ids', $packingSlipIds)
            ->update(['status' => 2, 'courier_id' => $courierId]);
    }

}

