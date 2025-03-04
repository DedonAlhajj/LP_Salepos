<?php

namespace App\Services\Tenant;


use App\Models\Product;
use App\Models\Product_Warehouse;
use App\Models\ProductBatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductWarehouseService
{

    /**
     * Quotation
     * Retrieve products by warehouse id
     *
     * @param int $id
     * @return array
     */
    public function getProduct($id)
    {
        // Initializing product data arrays
        $product_code = [];
        $product_name = [];
        $product_qty = [];
        $product_price = [];
        $product_type = [];
        $product_id = [];
        $product_list = [];
        $qty_list = [];
        $batch_no = [];
        $product_batch_id = [];

        // Disable strict mode temporarily for MySQL
        $this->setStrictMode(false);

        // Fetch all products (without variants, batches, and digital/ combo products)
        $products = $this->fetchProductWarehouseData($id);

        // Restore strict mode
        $this->setStrictMode(true);

        // Process product data and group results
        foreach ($products as $product_warehouse) {
            $this->addProductData($product_code, $product_name, $product_qty, $product_price,
                $product_type, $product_id, $product_list, $qty_list,
                $batch_no, $product_batch_id, $product_warehouse);
        }

        // Fetch and process digital and combo products
        $this->addDigitalAndComboProducts($id, $product_code, $product_name, $product_qty,
            $product_price, $product_type, $product_id,
            $product_list, $qty_list, $batch_no, $product_batch_id);

        // Return the data as required in the original format
        return [
            $product_code,
            $product_name,
            $product_qty,
            $product_type,
            $product_id,
            $product_list,
            $qty_list,
            $product_price,
            $batch_no,
            $product_batch_id
        ];
    }

    /**
     * Fetch product warehouse data.
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function fetchProductWarehouseData($id)
    {
        return Product_Warehouse::with(['product', 'batch', 'variant'])
            ->where('warehouse_id', $id)
            ->where(function ($query) {
                $query->where('variant_id', 0)
                    ->where('product_batch_id', 0)
                    ->orWhere('product_batch_id', '!=', 0)
                    ->orWhere('variant_id', '!=', 0);
            })
            ->get();
    }

    /**
     * Add product data to the data arrays
     *
     * @param array $product_code
     * @param array $product_name
     * @param array $product_qty
     * @param array $product_price
     * @param array $product_type
     * @param array $product_id
     * @param array $product_list
     * @param array $qty_list
     * @param array $batch_no
     * @param array $product_batch_id
     * @param Product_Warehouse $product_warehouse
     */
    private function addProductData(&$product_code, &$product_name, &$product_qty, &$product_price,
                                    &$product_type, &$product_id, &$product_list, &$qty_list,
                                    &$batch_no, &$product_batch_id, $product_warehouse)
    {
        $product = $product_warehouse->product;

        // Common data
        $product_code[] = $product->code;
        $product_name[] = $product->name;
        $product_qty[] = $product_warehouse->qty;
        $product_price[] = $product_warehouse->price;
        $product_type[] = $product->type;
        $product_id[] = $product->id;
        $product_list[] = $product_warehouse->product_list ?? null;
        $qty_list[] = $product_warehouse->qty_list ?? null;

        // Batch or Variant-specific data
        $batch_no[] = $product_warehouse->batch ? $product_warehouse->batch->batch_no : null;
        $product_batch_id[] = $product_warehouse->product_batch_id ?? null;
    }

    /**
     * Add digital and combo products to the data arrays.
     *
     * @param int $id
     * @param array $product_code
     * @param array $product_name
     * @param array $product_qty
     * @param array $product_price
     * @param array $product_type
     * @param array $product_id
     * @param array $product_list
     * @param array $qty_list
     * @param array $batch_no
     * @param array $product_batch_id
     */
    private function addDigitalAndComboProducts($id, &$product_code, &$product_name, &$product_qty,
                                                &$product_price, &$product_type, &$product_id,
                                                &$product_list, &$qty_list, &$batch_no, &$product_batch_id)
    {
        $digitalAndComboProducts = Product::whereNotIn('type', ['standard'])->get();

        foreach ($digitalAndComboProducts as $product) {
            $product_code[] = $product->code;
            $product_name[] = $product->name;
            $product_qty[] = $product->qty;
            $product_price[] = null; // Digital/ Combo price not applicable
            $product_type[] = $product->type;
            $product_id[] = $product->id;
            $product_list[] = $product->product_list;
            $qty_list[] = $product->qty_list;
            $batch_no[] = null;
            $product_batch_id[] = null;
        }
    }

    /**
     * Set strict mode for database connection.
     *
     * @param bool $strict
     * @return void
     */
    private function setStrictMode($strict)
    {
        config()->set('database.connections.mysql.strict', $strict);
        DB::reconnect();
    }

    /** end Quotation */

    /***/

    public function getProductsByWarehouse(int $warehouseId): array
    {
        $productsWithoutVariant = $this->getProductsWithoutVariant($warehouseId);
        $productsWithBatch = $this->getProductsWithBatch($warehouseId);
        $productsWithVariant = $this->getProductsWithVariant($warehouseId);

        return [
            array_merge($productsWithoutVariant['codes'], $productsWithBatch['codes'], $productsWithVariant['codes']),
            array_merge($productsWithoutVariant['names'], $productsWithBatch['names'], $productsWithVariant['names']),
            array_merge($productsWithoutVariant['quantities'], $productsWithBatch['quantities'], $productsWithVariant['quantities']),
        ];
    }

    private function getProductsWithoutVariant(int $warehouseId): array
    {
        return Product_Warehouse::with('product')
            ->inWarehouse($warehouseId)
            ->where('variant_id', 0)
            ->where('product_batch_id', 0)
            ->get()
            ->reduce(fn($carry, $item) => $this->formatProductData($carry, $item), $this->initializeProductArray());
    }

    private function getProductsWithBatch(int $warehouseId): array
    {
        $products = Product_Warehouse::selectRaw('product_id, MIN(id) as id, SUM(qty) as qty')
            ->where('warehouse_id', $warehouseId)
            ->where('variant_id', 0)
            ->where('product_batch_id', '!=', 0)
            ->groupBy('product_id')
            ->with(['product:id,name,code']) // جلب البيانات الضرورية فقط
            ->get();

        return $products->reduce(fn($carry, $item) => $this->formatProductData($carry, $item), $this->initializeProductArray());
    }


    private function getProductsWithVariant(int $warehouseId): array
    {
        return Product_Warehouse::with(['product', 'variant'])
            ->inWarehouse($warehouseId)
            ->Where('variant_id', '!=', 0)
            ->get()
            ->reduce(fn($carry, $item) => $this->formatProductDataWithVariant($carry, $item), $this->initializeProductArray());
    }

    private function formatProductData(array $carry, Product_Warehouse $productWarehouse): array
    {
        $carry['codes'][] = $productWarehouse->product->code;
        $carry['names'][] = $productWarehouse->product->name;
        $carry['quantities'][] = $productWarehouse->qty;
        return $carry;
    }

    private function formatProductDataWithVariant(array $carry, Product_Warehouse $productWarehouse): array
    {
        if ($productWarehouse->variant) {
            $carry['codes'][] = $productWarehouse->variant->item_code;
            $carry['names'][] = $productWarehouse->product->name;
            $carry['quantities'][] = $productWarehouse->qty;
        }
        return $carry;
    }

    private function initializeProductArray(): array
    {
        return ['codes' => [], 'names' => [], 'quantities' => []];
    }

}

