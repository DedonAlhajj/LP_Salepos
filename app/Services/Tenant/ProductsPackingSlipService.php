<?php

namespace App\Services\Tenant;

use App\Models\PackingSlipProduct;
use App\Models\Product;
use App\Models\Product_Sale;
use App\Models\Product_Warehouse;
use App\Models\ProductVariant;
use App\Models\Sale;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductsPackingSlipService
{
    /**
     * Processes the products associated with a packing slip.
     * This includes updating stock levels for each product and deleting the product records.
     *
     * @param mixed $packingSlipProducts A collection of packing slip products to process.
     * @param Sale $sale The sale object associated with the packing slip.
     * @return void
     */
    public function processPackingSlipProducts($packingSlipProducts, $sale): void
    {
        // Iterate over each product in the packing slip
        foreach ($packingSlipProducts as $product) {
            // Update the stock levels for the product
            $this->updateProductStock($product, $sale);

            // Delete the product record from the packing slip
            $product->delete();
        }
    }


    /**
     * Updates the stock levels for a product associated with a packing slip.
     * This includes updating the main product stock, variant stock, and warehouse stock.
     *
     * @param PackingSlipProduct $packingSlipProduct The packing slip product object to update stock for.
     * @param Sale $sale The sale object associated with the packing slip product.
     * @return void
     * @throws ModelNotFoundException If the product cannot be found.
     */
    private function updateProductStock(PackingSlipProduct $packingSlipProduct, $sale): void
    {
        // Fetch the main product information
        $product = Product::findOrFail($packingSlipProduct->product_id);

        // Retrieve the associated product sale information
        $productSale = Product_Sale::where([
            ['sale_id', $sale->id],
            ['product_id', $packingSlipProduct->product_id],
            ['variant_id', $packingSlipProduct->variant_id]
        ])->first();

        // Retrieve the product's warehouse stock information
        $productWarehouse = Product_Warehouse::where([
            ['product_id', $packingSlipProduct->product_id],
            ['warehouse_id', $sale->warehouse_id],
            ['variant_id', $packingSlipProduct->variant_id]
        ])->first();

        // Update stock for product variants, if applicable
        if ($packingSlipProduct->variant_id) {
            $this->updateProductVariantStock($packingSlipProduct, $productSale);
        }

        // Update stock in the warehouse if relevant information exists
        if ($productWarehouse && $productSale) {
            $productWarehouse->qty += $productSale->qty;
            $productWarehouse->save();
        }

        // Update the main product stock and product sale details if available
        if ($productSale) {
            $product->qty += $productSale->qty;
            $product->save();

            $productSale->is_packing = 0; // Mark the product as not packed
            $productSale->save();
        }
    }


    /**
     * Updates the stock levels for product variants associated with a packing slip product.
     *
     * @param PackingSlipProduct $packingSlipProduct The packing slip product object containing variant details.
     * @param Product_Sale $productSale The associated product sale object.
     * @return void
     */
    private function updateProductVariantStock($packingSlipProduct, $productSale): void
    {
        // Fetch the product variant information
        $productVariant = ProductVariant::where([
            ['product_id', $packingSlipProduct->product_id],
            ['variant_id', $packingSlipProduct->variant_id]
        ])->first();

        // Update the product variant stock if both productVariant and productSale exist
        if ($productVariant && $productSale) {
            $productVariant->qty += $productSale->qty;
            $productVariant->save();
        }
    }

}

