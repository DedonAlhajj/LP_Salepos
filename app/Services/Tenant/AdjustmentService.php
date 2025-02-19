<?php

namespace App\Services\Tenant;


use App\Events\AdjustmentDeleted;
use App\Events\AdjustmentUpdated;
use App\Models\Adjustment;
use App\Models\Product;
use App\Models\Product_Warehouse;
use App\Models\ProductAdjustment;
use App\Models\ProductVariant;
use App\Models\StockCount;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdjustmentService
{

    protected WarehouseService $warehouseService;
    protected ProductService $productService;

    public function __construct(WarehouseService $warehouseService,ProductService $productService)
    {
        $this->warehouseService = $warehouseService;
        $this->productService = $productService;
    }

    public function authorize($ability)
    {
        if (!Auth::guard('web')->user()->can($ability)) {
            throw new AuthorizationException(__('Sorry! You are not allowed to access this module.'));
        }
    }

    public function getAllAdjustments(): Collection
    {
        $this->authorize('adjustment');
        try {
            return Adjustment::withRelations()
                ->orderByDesc('id')
                ->get();
        } catch (Exception $e) {
            Log::error("Error fetching modifications: " . $e->getMessage());
            throw new \RuntimeException("An error occurred while fetching the modification data..");
        }
    }

    public  function getCreateData(){

        return $this->warehouseService->getWarehouses();
    }


    /** Start Store */
    public function storeAdjustment(array $request)
    {
        DB::beginTransaction();

        try {
            $this->updateStockCount($request);
            $adjustment = $this->createAdjustment($request);
            $this->handleDocuments($adjustment, $request);
            $this->processProductAdjustments($adjustment, $request);

            DB::commit();
            return ['message' => 'Added successfully', 'status' => true];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('An error occurred while saving data.: ' . $e->getMessage());
            throw ValidationException::withMessages(['error' => 'An error occurred while saving data.']);
        }
    }

    private function updateStockCount(array $request)
    {
        if (isset($request['stock_count_id'])) {
            StockCount::where('id', $request['stock_count_id'])
                ->update(['is_adjusted' => true]);
        }
    }

    private function createAdjustment(array $request)
    {
        $request['reference_no'] = 'adr-' . now()->format('Ymd-His');
        return Adjustment::create($request);
    }

    private function handleDocuments($adjustment, array $request)
    {
        if (isset($request['document'])) {
            $adjustment->addMedia($request['document'])->toMediaCollection('adjustment_doc');
        }
    }

    private function processProductAdjustments($adjustment, array $request)
    {
        $productAdjustments = [];

        foreach ($request['product_id'] as $key => $productId) {
            $product = Product::findOrFail($productId);
            $variantId = $this->handleVariant($product, $request, $key);
            $productWarehouse = $this->findOrCreateProductWarehouse($productId, $variantId, $request['warehouse_id']);

            $this->updateProductQuantities($product, $productWarehouse, $request, $key);

            $productAdjustments[] = [
                'product_id'     => $productId,
                'variant_id'     => $variantId,
                'adjustment_id'  => $adjustment->id,
                'qty'            => $request['qty'][$key],
                'unit_cost'      => $request['unit_cost'][$key],
                'action'         => $request['action'][$key],
                'created_at'     => now(),
                'updated_at'     => now()
            ];
        }

        ProductAdjustment::insert($productAdjustments);
    }

    private function handleVariant($product, array $request, $key)
    {
        if (!$product->is_variant) {
            return null;
        }

        $variant = ProductVariant::FindExactProductWithCode($product->id, $request['product_code'][$key])->firstOrFail();
        $variant->update([
            'qty' => $request['action'][$key] === '-'
                ? $variant->qty - $request['qty'][$key]
                : $variant->qty + $request['qty'][$key]
        ]);

        return $variant->variant_id;
    }

    private function findOrCreateProductWarehouse($productId, $variantId, $warehouseId)
    {
        $productWarehouseQuery = Product_Warehouse::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId);

        if (!is_null($variantId)) {
            $productWarehouseQuery->where('variant_id', $variantId);
        }
        return $productWarehouseQuery;
    }

    private function updateProductQuantities($product, $productWarehouse, array $request, $key)
    {
        $adjustmentValue = $request['action'][$key] === '-' ? -$request['qty'][$key] : $request['qty'][$key];

        $product->increment('qty', $adjustmentValue);
        $productWarehouse->increment('qty', $adjustmentValue);
    }
    /** End Store */

    /** Start Update */
    public function edit($id)
    {
        return [
            "lims_adjustment_data" => Adjustment::find($id),
            "lims_product_adjustment_data" => ProductAdjustment::where('adjustment_id', $id)->get(),
            "lims_warehouse_list" => $this->warehouseService->getWarehouses(),
        ];
    }

    public function updateAdjustment($request, $id)
    {
        DB::beginTransaction();

        try {
            $data = $request;
            unset($data['document']);
            $adjustment = Adjustment::findOrFail($id);

            // ✅ Update documents using Spatie Media Library
            $this->updateDocument($adjustment, $request);

            // ✅ Restore old products related to the modification
            $oldAdjustments = ProductAdjustment::where('adjustment_id', $id)->get();

            // ✅ Retrieve quantities before deletion
            $this->restoreOldQuantities($oldAdjustments, $adjustment->warehouse_id);

            // ✅ Delete old data after retrieving quantities
            ProductAdjustment::where('adjustment_id', $id)->delete();

            // ✅ Create new modifications
            $newAdjustments = $this->createNewAdjustments($request, $id);

            // ✅ Enter new data in one go
            ProductAdjustment::insert($newAdjustments);

            // ✅ Update `Adjustment` data
            $adjustment->update($data);

            // 🔥 Event launch after update
            event(new AdjustmentUpdated($adjustment));

            DB::commit();
            return ['message' => 'Updating Successfully', 'status' => true];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('An error occurred while updating data.: ' . $e->getMessage());
            throw ValidationException::withMessages(['error' => 'An error occurred while updating data.']);
        }
    }

    private function updateDocument($adjustment, $request)
    {
        if (isset($request['document'])) {
            $adjustment->clearMediaCollection('adjustment_doc');
            $adjustment->addMedia($request['document'])->toMediaCollection('adjustment_doc');
        }
    }

    private function restoreOldQuantities($oldAdjustments, $warehouseId)
    {
        foreach ($oldAdjustments as $old) {
            $product = Product::find($old->product_id);
            $warehouseProduct = Product_Warehouse::byProductAndWarehouse($old->product_id, $warehouseId)->first();

            if ($old->variant_id) {
                $variant = ProductVariant::where([
                    'product_id' => $old->product_id,
                    'variant_id' => $old->variant_id
                ])->first();

                if ($old->action === '-') {
                    $variant->increment('qty', $old->qty);
                } else {
                    $variant->decrement('qty', $old->qty);
                }
            }

            if ($old->action === '-') {
                $product->increment('qty', $old->qty);
                $warehouseProduct->increment('qty', $old->qty);
            } else {
                $product->decrement('qty', $old->qty);
                $warehouseProduct->decrement('qty', $old->qty);
            }
        }
    }

    private function createNewAdjustments($request, $adjustmentId)
    {
        $newAdjustments = [];
        foreach ($request['product_id'] as $key => $productId) {
            $product = Product::findOrFail($productId);
            $variantId = null;

            if ($product->is_variant) {
                $variant = ProductVariant::FindExactProductWithCode($productId, $request['product_code'][$key])->firstOrFail();
                $variant->update([
                    'qty' => $request['action'][$key] === '-'
                        ? $variant->qty - $request['qty'][$key]
                        : $variant->qty + $request['qty'][$key]
                ]);
                $variantId = $variant->variant_id;
            }

            $warehouseProduct = Product_Warehouse::byProductAndWarehouse($productId, $request['warehouse_id'])->firstOrFail();

            $product->update([
                'qty' => $request['action'][$key] === '-'
                    ? $product->qty - $request['qty'][$key]
                    : $product->qty + $request['qty'][$key]
            ]);

            $warehouseProduct->update([
                'qty' => $request['action'][$key] === '-'
                    ? $warehouseProduct->qty - $request['qty'][$key]
                    : $warehouseProduct->qty + $request['qty'][$key]
            ]);

            $newAdjustments[] = [
                'product_id'    => $productId,
                'variant_id'    => $variantId,
                'adjustment_id' => $adjustmentId,
                'qty'           => $request['qty'][$key],
                'unit_cost'     => $request['unit_cost'][$key],
                'action'        => $request['action'][$key],
                'created_at'    => now(),
                'updated_at'    => now()
            ];
        }

        return $newAdjustments;
    }
    /** End Update */

    /** Start Delete */
    public function deleteBySelection(array $adjustmentIds)
    {
        DB::beginTransaction();

        try {
            foreach ($adjustmentIds as $id) {
                $adjustment = Adjustment::findOrFail($id);

                // ✅ Delete document using Spatie Media Library
                $this->deleteDocument($adjustment);

                // ✅ Retrieve products related to the modification
                $productAdjustments = ProductAdjustment::where('adjustment_id', $id)->get();

                // ✅ Retrieve quantities before deletion
                $this->restoreStockQuantities($productAdjustments, $adjustment->warehouse_id);

                // ✅ Delete edits
                ProductAdjustment::where('adjustment_id', $id)->delete();

                // ✅ Delete adjustment
                $adjustment->delete();

                // 🔥 Launch event after deletion
                event(new AdjustmentDeleted($adjustment));
            }

            DB::commit();
            return ['message' => 'Deleted successfully', 'status' => true];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting multi: ' . $e->getMessage());
            return ['message' => 'Error deleting multi.', 'status' => false];
        }
    }

    private function deleteDocument($adjustment)
    {
        $adjustment->clearMediaCollection('adjustment_doc');
    }

    private function restoreStockQuantities($productAdjustments, $warehouseId)
    {
        foreach ($productAdjustments as $adjustment) {
            $product = Product::findOrFail($adjustment->product_id);
            $warehouseProduct = Product_Warehouse::byProductAndWarehouse($adjustment->product_id, $warehouseId)->firstOrFail();

            if ($adjustment->variant_id) {
                $variant = ProductVariant::FindExactProductWithCode($adjustment->product_id, $adjustment->variant_id)->firstOrFail();

                $variant->update([
                    'qty' => $adjustment->action === '-'
                        ? $variant->qty + $adjustment->qty
                        : $variant->qty - $adjustment->qty
                ]);
            }

            $product->update([
                'qty' => $adjustment->action === '-'
                    ? $product->qty + $adjustment->qty
                    : $product->qty - $adjustment->qty
            ]);

            $warehouseProduct->update([
                'qty' => $adjustment->action === '-'
                    ? $warehouseProduct->qty + $adjustment->qty
                    : $warehouseProduct->qty - $adjustment->qty
            ]);
        }
    }



    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $adjustment = Adjustment::findOrFail($id);
            $productAdjustments = ProductAdjustment::where('adjustment_id', $id)->get();

            // ✅ Retrieve quantities before deletion
            $this->restoreStockQuantities($productAdjustments, $adjustment->warehouse_id);

            // ✅ Delete edits
            ProductAdjustment::where('adjustment_id', $id)->delete();

            // ✅ Delete the modification itself
            $adjustment->delete();

            // ✅ Delete document using Spatie Media Library
            $this->deleteDocument($adjustment);

            DB::commit();
            return ['message' => 'Deleted successfully', 'status' => true];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting edit: ' . $e->getMessage());
            return ['message' => 'Error deleting', 'status' => false];
        }
    }
    /** End Delete */

}
