<?php

namespace App\Services\Tenant;



// ProductPurchaseService.php
use App\Models\ProductPurchase;
use Illuminate\Support\Facades\Log;

class ProductPurchaseService
{
    protected $productService;
    protected $unitService;
    protected $productVariantService;
    protected $productBatchService;

    public function __construct(
        ProductService $productService,
        UnitService $unitService,
        ProductVariantService $productVariantService,
        ProductBatchService $productBatchService
    ) {
        $this->productService = $productService;
        $this->unitService = $unitService;
        $this->productVariantService = $productVariantService;
        $this->productBatchService = $productBatchService;
    }

    public function getProductPurchaseData($purchaseId)
    {
        try {
            $productPurchases = ProductPurchase::with([
                'product',
                'unit',
                'productVariant',
                'productBatch'
            ])->where('purchase_id', $purchaseId)->get();

            return $productPurchases->map(function ($productPurchaseData) {
                // Get product data via product service
                $product = $this->productService->getProductData($productPurchaseData->product_id);
                // Get unit data via unit service
                $unit = $this->unitService->getUnit($productPurchaseData->purchase_unit_id);

                $productPurchase = [];
                $productPurchase[0] = $product->name . ' [' . $product->code . ']';

                // If there is an IMEI number
                if ($productPurchaseData->imei_number) {
                    $productPurchase[0] .= '<br>IMEI or Serial Number: ' . $productPurchaseData->imei_number;
                }

                $productPurchase[1] = $productPurchaseData->qty;
                $productPurchase[2] = $unit->unit_code;
                $productPurchase[3] = $productPurchaseData->tax;
                $productPurchase[4] = $productPurchaseData->tax_rate;
                $productPurchase[5] = $productPurchaseData->discount;
                $productPurchase[6] = $productPurchaseData->total;

                // Fetch batch data via batch service
                $productPurchase[7] = $this->productBatchService->getBatchNo($productPurchaseData);

                return $productPurchase;
            });
        } catch (\Exception $e) {
            // Improved error handling
            Log::error("Error Purchase fetching modifications11: " . $e->getMessage());
            throw new \Exception('Something went wrong while fetching product purchase data: ' . $e->getMessage());
        }
    }
}

