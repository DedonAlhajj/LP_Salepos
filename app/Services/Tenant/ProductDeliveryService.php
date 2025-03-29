<?php

namespace App\Services\Tenant;


use App\Models\Delivery;
use App\Models\Product_Sale;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductDeliveryService
{

    /**
     * Retrieves product delivery data for a specific delivery ID.
     * Fetches related sale and product information, including variants and batch details,
     * and returns the data in a structured format.
     *
     * @param int $id The ID of the delivery.
     * @return array An array containing product delivery data.
     */
    public function getProductDeliveryData(int $id): array
    {
        try {
            // Fetch delivery details along with the associated sale data.
            $delivery = Delivery::with('sale')->find($id);
            if (!$delivery || !$delivery->sale) {
                // Log a warning if the delivery or its associated sale is missing.
                Log::warning("Delivery not found or sale missing for ID: {$id}");
                return []; // Return an empty array if data is missing.
            }

            // Fetch all product sales related to the sale.
            $productSales = Product_Sale::where('sale_id', $delivery->sale->id)->get();
            if ($productSales->isEmpty()) {
                return []; // Return an empty array if there are no product sales.
            }

            // Gather unique product IDs from the product sales for batch fetching.
            $productIds = $productSales->pluck('product_id')->unique()->toArray();

            // Fetch product data including variants and batches for the unique product IDs.
            $products = Product::whereIn('id', $productIds)
                ->with([
                    'variants', // Preload variant information for products.
                    'batches:batch_no,expired_date,id' // Preload batch details with specific attributes.
                ])
                ->get()
                ->keyBy('id'); // Index products by their IDs for efficient access.

            // Initialize an array to store structured product data.
            $productData = [];

            // Iterate through the product sales to compile detailed product information.
            foreach ($productSales as $productSale) {
                $product = $products->get($productSale->product_id);

                if (!$product) {
                    // Log a warning if a product is not found for the given ID.
                    Log::warning("Product not found for ID: {$productSale->product_id}");
                    continue; // Skip this product sale if no product data is available.
                }

                // Update the product code if the product has a variant associated with it.
                $code = $product->code;
                if ($productSale->variant_id) {
                    $variant = $product->variants->firstWhere('variant_id', $productSale->variant_id);
                    if ($variant) {
                        $code = $variant->item_code; // Use the variant's item code if available.
                    }
                }

                // Fetch batch details for the product, if applicable.
                $batch = $product->batches->firstWhere('id', $productSale->product_batch_id);
                $batchNo = $batch ? $batch->batch_no : 'N/A'; // Use 'N/A' if no batch is found.
                $expiredDate = $batch
                    ? date(config('app.date_format', 'Y-m-d'), strtotime($batch->expired_date))
                    : 'N/A'; // Format the expiry date or use 'N/A'.

                // Store the structured product data.
                $productData[] = [
                    'code' => $code,
                    'name' => $product->name,
                    'batch_no' => $batchNo,
                    'expired_date' => $expiredDate,
                    'quantity' => $productSale->qty,
                ];
            }

            return $productData; // Return the compiled product delivery data.
        } catch (\Exception $e) {
            // Log the error and return an empty array in case of an exception.
            Log::error("Error fetching product delivery data: " . $e->getMessage());
            return [];
        }
    }

}
