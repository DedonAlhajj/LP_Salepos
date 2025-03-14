<?php

namespace App\Services\Tenant;

use App\DTOs\ProductSaleDTO;
use App\DTOs\ReturnProductDTO;
use App\DTOs\ReturnProductPurchaseDTO;
use App\DTOs\ReturnUpdateDTO;
use App\Models\Account;
use App\Models\Biller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Product_Sale;
use App\Models\Product_Warehouse;
use App\Models\ProductBatch;
use App\Models\ProductReturn;
use App\Models\ProductVariant;
use App\Models\Returns;
use App\Models\Sale;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;

class ReturnService
{

    protected WarehouseService $warehouseService;
    protected UnitService $unitService;
    protected TaxCalculatorService $taxService;
    protected MediaService $mediaService;

    public function __construct(
        WarehouseService $warehouseService,
        UnitService $unitService,
        TaxCalculatorService $taxService,
        MediaService $mediaService,
    )
    {
        $this->warehouseService = $warehouseService;
        $this->unitService = $unitService;
        $this->taxService = $taxService;
        $this->mediaService = $mediaService;
    }
    public function getReturnsData($warehouse_id, $starting_date, $ending_date)
    {
        // إعداد الاستعلام مع التحميل المسبق للعلاقات وتحديد الأعمدة المطلوبة فقط
        return Returns::with([
            'biller', // تحميل أعمدة معينة من جدول 'biller'
            'customer', // تحميل أعمدة معينة من جدول 'customer'
            'warehouse:id,name', // تحميل أعمدة معينة من جدول 'warehouse'
            'user:id,name', // تحميل أعمدة معينة من جدول 'user'
            'sale:id,reference_no', // تحميل أعمدة معينة من جدول 'sale'
            'currency:id,code' // تحميل أعمدة معينة من جدول 'currency'
        ])->forUserAccessWarehouse($warehouse_id)
            ->whereBetween('created_at', [$starting_date, $ending_date])->get();
    }

    public function getWarehouseList()
    {
        return $this->warehouseService->getWarehouses();
    }


    /** Start create*/
    public function getSaleData(string $referenceNo)
    {
        $sale = Sale::whereCompletedReference($referenceNo)
            ->select('id', 'sale_status')
            ->first();

        if (!$sale) {
            return null;
        }

        $productSales = Product_Sale::where('sale_id', $sale->id)
            ->with(['product', 'variant', 'batch', 'tax'])
            ->get();

        $productSaleDTOs = $productSales->map(function ($productSale) {
            return new ProductSaleDTO($productSale);
        });

        return [
            'sale' => $sale,
            'products' => $productSaleDTOs,
            'taxes' => $this->taxService->getTaxes(),
            'warehouses' => $this->warehouseService-> getWarehouses(),
        ];
    }

    /** Start Edit */
    public function getReturnDetails(int $id): array
    {
        try {
            $return = Returns::findOrFail($id);
            $customers = Customer::select('id', 'name','phone_number')->get();
            $warehouses = Warehouse::select('id', 'name')->get();
            $billers = Biller::select('id', 'name','company_name')->get();
            $taxes = Tax::select('id', 'name', 'rate')->get();

            $products = ProductReturn::where('return_id', $id)
                ->with([
                    'product:id,name,code,tax_method,unit_id',
                    'productVariant:id,item_code,product_id',
                    'tax:id,name,rate',
                    'productBatch:id,batch_no'
                ])
                ->get()
                ->filter(fn ($productReturn) => $productReturn->product !== null) // 👈 تجاهل المنتجات التي ليس لها Product
                ->map(fn ($productReturn) => $this->formatReturnProduct($productReturn))
                ->toArray();

            return compact('customers', 'warehouses', 'billers', 'taxes', 'return', 'products');
        } catch (\Exception $e) {
            Log::error("Error in Return Edit: " . $e->getMessage());
            throw new \Exception("An error occurred while fetching data. : " . $e->getMessage());
        }
    }

    private function formatReturnProduct(ProductReturn $productReturn): ReturnProductDTO
    {
        $product = $productReturn->product;
        $variant = $productReturn->productVariant;
        $tax = $productReturn->tax;
        $batch = $productReturn->productBatch;

        $productPrice = $this->calculateProductPrice($product, $productReturn);
        $unitDetails = $this->unitService->getProductUnits($product, $productReturn) ?? [
                'unit_name' => 'N/A',
                'unit_operator' => 'N/A',
                'unit_operation_value' => 'N/A'
            ];

        return new ReturnProductDTO(
            product_id: $product->id,
            product_name: $product->name,
            product_code: $variant?->item_code ?? $product->code ?? 'N/A',
            product_variant_id: $variant?->id ?? null,
            product_price: $productPrice,
            qty: $productReturn->qty ?? 0,
            net_unit_price: $productReturn->net_unit_price ?? 0.0,
            discount: $productReturn->discount ?? 0.0,
            tax: $productReturn->tax ?? 0.0,
            total: $productReturn->total ?? 0.0,
            batch_no: $batch?->batch_no ?? 'No Batch',
            unit_name: $unitDetails['unit_name'],
            unit_operator: $unitDetails['unit_operator'],
            unit_operation_value: $unitDetails['unit_operation_value'],
            tax_name: $tax?->name ?? 'No Tax',
            tax_method: $product->tax_method ?? 1,
            tax_rate: $productReturn->tax_rate ?? 0.0,
            imei_number: $productReturn->imei_number ?? ''
        );
    }

    private function calculateProductPrice($product, ProductReturn $productReturn): float
    {
        $qty = max($productReturn->qty ?? 1, 1); // ✅ تجنب القسمة على صفر

        if (($product->tax_method ?? 1) == 1) {
            return ($productReturn->net_unit_price ?? 0) + (($productReturn->discount ?? 0) / $qty);
        }
        return (($productReturn->total ?? 0) / $qty) + (($productReturn->discount ?? 0) / $qty);
    }

    /** Start Update*/

    public function update(ReturnUpdateDTO $dto): bool
    {
        DB::beginTransaction();
        try {
            $return = Returns::findOrFail($dto->returnId);

            // تحديث الوثيقة إذا وُجدت
            if ($dto->document) {
                $documentPath = $this->mediaService->uploadDocumentWithClear($return, $dto->document, 'returns');
                $dto->data['document'] = $documentPath;
            }

            $return->update($dto->data);

            // تحديث المنتجات المرتبطة
            $this->updateProductReturns($return, $dto->data);

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Return Update Failed: " . $e->getMessage(), ['stack' => $e->getTraceAsString()]);
            throw new Exception("Return Update Failed");
        }
    }

    private function updateProductReturns(Returns $return, array $data): void
    {
        $productReturns = ProductReturn::where('return_id', $return->id)->get();
        $existingProductIds = [];

        foreach ($productReturns as $productReturn) {
            $existingProductIds[] = $productReturn->product_id;
            $this->updateProductStock($productReturn, $return->warehouse_id);
        }

        // حذف المنتجات غير الموجودة في التحديث الجديد
        if (isset($data['product_id'])) {
            $newProductIds = $data['product_id'];
            ProductReturn::where('return_id', $return->id)
                ->whereNotIn('product_id', $newProductIds)
                ->delete();
        }
    }

    private function updateProductStock(ProductReturn $productReturn, int $warehouseId): void
    {
        $product = Product::find($productReturn->product_id);

        if (!$product) {
            Log::warning("Product not found: " . $productReturn->product_id);
            return;
        }

        match ($product->type) {
            'combo' => $this->updateComboProductStock($product, $productReturn, $warehouseId),
            default => $this->updateSingleProductStock($product, $productReturn, $warehouseId)
        };
    }

    private function updateComboProductStock(Product $product, ProductReturn $productReturn, int $warehouseId): void
    {
        $productList = explode(",", $product->product_list);
        $variantList = explode(",", $product->variant_list);
        $qtyList = explode(",", $product->qty_list);

        foreach ($productList as $index => $childId) {
            $childProduct = Product::find($childId);
            if (!$childProduct) continue;

            $quantity = $productReturn->qty * ($qtyList[$index] ?? 1);

            if (isset($variantList[$index])) {
                $childProductVariant = ProductVariant::where([
                    ['product_id', $childId],
                    ['variant_id', $variantList[$index]]
                ])->first();
                if ($childProductVariant) {
                    $childProductVariant->decrement('qty', $quantity);
                    $childProductVariant->save();
                }
            }

            $childProduct->decrement('qty', $quantity);
            $this->updateProductWarehouseStock($childId, $warehouseId, -$quantity);
        }
    }

    private function updateSingleProductStock(Product $product, ProductReturn $productReturn, int $warehouseId): void
    {
        $quantity = $this->calculateReturnQuantity($productReturn);

        if ($productReturn->variant_id) {
            $productVariant = ProductVariant::where([
                ['product_id', $product->id],
                ['variant_id', $productReturn->variant_id]
            ])->first();
            if ($productVariant) {
                $productVariant->decrement('qty', $quantity);
                $productVariant->save();
            }
        } elseif ($productReturn->product_batch_id) {
            $productBatch = ProductBatch::find($productReturn->product_batch_id);
            if ($productBatch) {
                $productBatch->decrement('qty', $quantity);
                $productBatch->save();
            }
        }

        $product->decrement('qty', $quantity);
        $this->updateProductWarehouseStock($product->id, $warehouseId, -$quantity);

        // حذف أرقام IMEI إذا وُجدت
        if ($productReturn->imei_number) {
            $this->removeImeiNumbers($productReturn, $warehouseId);
        }
    }

    private function calculateReturnQuantity(ProductReturn $productReturn): int
    {
        if ($productReturn->sale_unit_id != 0) {
            $saleUnit = Unit::find($productReturn->sale_unit_id);
            if ($saleUnit) {
                return $saleUnit->operator == '*' ?
                    $productReturn->qty * $saleUnit->operation_value :
                    $productReturn->qty / $saleUnit->operation_value;
            }
        }
        return $productReturn->qty;
    }

    private function updateProductWarehouseStock(int $productId, int $warehouseId, int $quantity): void
    {
        $productWarehouse = Product_Warehouse::where([
            ['product_id', $productId],
            ['warehouse_id', $warehouseId]
        ])->first();

        if ($productWarehouse) {
            $productWarehouse->increment('qty', $quantity);
            $productWarehouse->save();
        }
    }

    private function removeImeiNumbers(ProductReturn $productReturn, int $warehouseId): void
    {
        $productWarehouse = Product_Warehouse::where([
            ['product_id', $productReturn->product_id],
            ['warehouse_id', $warehouseId]
        ])->first();

        if ($productWarehouse && $productWarehouse->imei_number) {
            $allImeiNumbers = explode(",", $productWarehouse->imei_number);
            $returnedImeiNumbers = explode(",", $productReturn->imei_number);

            $updatedImeiNumbers = array_diff($allImeiNumbers, $returnedImeiNumbers);
            $productWarehouse->imei_number = implode(",", $updatedImeiNumbers);
            $productWarehouse->save();
        }
    }

    /** Delete */

    public function deleteReturn(int $returnId)
    {
        DB::beginTransaction();
        try {
            $return = Returns::findOrFail($returnId);
            $productReturns = ProductReturn::where('return_id', $returnId)->get();

            foreach ($productReturns as $productReturn) {
                $product = Product::findOrFail($productReturn->product_id);

                // تحديث المخزون وفقًا لنوع المنتج
                $this->updateStock($product, $productReturn, $return->warehouse_id);

                // تحديث المبيعات إذا كانت مرتبطة
                if ($return->sale_id) {
                    $this->updateSaleData($return->sale_id, $productReturn);
                }

                // حذف بيانات الإرجاع
                $productReturn->delete();
            }

            // تحديث حالة المبيعات
            if ($return->sale_id) {
                Sale::where('id', $return->sale_id)->update(['sale_status' => 1]);
            }

            // حذف سجل الإرجاع والملف المرتبط به
            $return->delete();
            $this->mediaService->deleteDocument($return, 'returns');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \RuntimeException("فشل حذف الإرجاع: " . $e->getMessage());
        }
    }

    private function updateStock(Product $product, ProductReturn $productReturn, int $warehouseId)
    {
        if ($product->type == 'combo') {
            $this->updateComboProductStockDelete($product, $productReturn, $warehouseId);
        } else {
            $this->updateRegularProductStock($product, $productReturn, $warehouseId);
        }
    }

    private function updateComboProductStockDelete(Product $product, ProductReturn $productReturn, int $warehouseId)
    {
        $productList = explode(",", $product->product_list);
        $variantList = explode(",", $product->variant_list);
        $qtyList = explode(",", $product->qty_list);

        foreach ($productList as $index => $childId) {
            $childProduct = Product::findOrFail($childId);
            $variantId = $variantList[$index] ?? null;
            $quantity = $productReturn->qty * $qtyList[$index];

            // تحديث المخزون بناءً على المتغيرات
            if ($variantId) {
                ProductVariant::where([
                    ['product_id', $childId],
                    ['variant_id', $variantId]
                ])->decrement('qty', $quantity);

                Product_Warehouse::where([
                    ['product_id', $childId],
                    ['variant_id', $variantId],
                    ['warehouse_id', $warehouseId]
                ])->decrement('qty', $quantity);
            } else {
                Product_Warehouse::where([
                    ['product_id', $childId],
                    ['warehouse_id', $warehouseId]
                ])->decrement('qty', $quantity);
            }

            // تحديث الكمية العامة للمنتج
            $childProduct->decrement('qty', $quantity);
        }
    }

    private function updateRegularProductStock(Product $product, ProductReturn $productReturn, int $warehouseId)
    {
        $quantity = $this->calculateReturnQuantity($productReturn);

        if ($productReturn->variant_id) {
            ProductVariant::FindExactProduct($productReturn->product_id, $productReturn->variant_id)
                ->first()
                ->decrement('qty', $quantity);

            Product_Warehouse::FindProductWithVariant($productReturn->product_id, $productReturn->variant_id, $warehouseId)
                ->first()
                ->decrement('qty', $quantity);
        } elseif ($productReturn->product_batch_id) {
            ProductBatch::where('id', $productReturn->product_batch_id)->decrement('qty', $quantity);
            Product_Warehouse::where([
                ['product_batch_id', $productReturn->product_batch_id],
                ['warehouse_id', $warehouseId]
            ])->decrement('qty', $quantity);
        } else {
            Product_Warehouse::FindProductWithoutVariant($productReturn->product_id, $warehouseId)
                ->first()
                ->decrement('qty', $quantity);
        }

        $product->decrement('qty', $quantity);
    }

    private function updateSaleData(int $saleId, ProductReturn $productReturn)
    {
        Product_Sale::where([
            ['sale_id', $saleId],
            ['product_id', $productReturn->product_id]
        ])->decrement('return_qty', $productReturn->qty);
    }


    /***/
    public function deleteBySelection(array $returnIds)
    {

        DB::beginTransaction();
        try {
            foreach ($returnIds as $returnId) {
                $return = Returns::findOrFail($returnId);
                $productReturns = ProductReturn::where('return_id', $returnId)->get();

                foreach ($productReturns as $productReturn) {
                    $product = Product::findOrFail($productReturn->product_id);

                    // تحديث المخزون وفقًا لنوع المنتج
                    $this->updateStock($product, $productReturn, $return->warehouse_id);

                    // تحديث المبيعات إذا كانت مرتبطة
                    if ($return->sale_id) {
                        $this->updateSaleData($return->sale_id, $productReturn);
                    }

                    // حذف بيانات الإرجاع
                    $productReturn->delete();
                }

                // تحديث حالة المبيعات
                if ($return->sale_id) {
                    Sale::where('id', $return->sale_id)->update(['sale_status' => 1]);
                }

                // حذف سجل الإرجاع والملف المرتبط به
                $return->delete();
                $this->mediaService->deleteDocument($return, 'returns');
            }

            DB::commit();
            return 'Return(s) deleted successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Return delete Failed: " . $e->getMessage());
            throw new \RuntimeException("فشل حذف الإرجاع: " . $e->getMessage());
        }
    }




}
