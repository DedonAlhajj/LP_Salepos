<?php

namespace App\Services\Tenant;

use App\DTOs\ReturnSaleDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\{Sale,
    Returns,
    Customer,
    Product,
    ProductVariant,
    Product_Warehouse,
    ProductBatch,
    ProductReturn,
    CashRegister,
    Account,
    Unit};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class SaleReturnService
{

    /** Start Process Sale Return "Store"*/
    public function processSaleReturn(Request $request, $document): Returns
    {
        return DB::transaction(function () use ($request, $document) {
            try {
                $data = $this->prepareReturnData($request)->toArray();

                // Create return record
                $return = Returns::create($data);

                // Add document if uploaded
//                if ($document) {
//                    $return->addMedia($document)->toMediaCollection('transfers');
//                }

                // Update inventory based on return data
                $this->updateProductStock($data);

                // Update sale status if necessary.
                $this->updateSaleStatusIfRequired($data);

                return $return;

            } catch (\Exception $e) {
                throw new \Exception("An error occurred while processing the sale return.");
            }
        });
    }

    private function prepareReturnData(Request $request)
    {
        try {
            $sale = Sale::select('id', 'warehouse_id', 'customer_id', 'biller_id', 'currency_id', 'exchange_rate', 'sale_status')
                ->findOrFail($request->input('sale_id'));
        } catch (\Exception $e) {
            throw new \Exception("Sale not found or invalid sale_id");
        }

        return ReturnSaleDTO::fromRequest($sale, $request);
    }

    private function updateProductStock(array $data)
    {
        $products = collect($data['product_id'])->map(function ($productId, $index) use ($data) {
            return [
                'product_id' => $productId,
                'product_code' => $data['product_code'][$index],
                'qty' => $data['qty'][$index],
                'sale_unit' => $data['sale_unit'][$index],
                'warehouse_id' => $data['warehouse_id'],
                'product_batch_id' => $data['product_batch_id'][$index],
                'actual_qty' => $data['actual_qty'][$index],
            ];
        });

        foreach ($products as $product) {
            $this->adjustStock($product);
        }
    }

    private function adjustStock(array $product)
    {
        $productModel = Product::findOrFail($product['product_id']);
        $unit = Unit::where('unit_name', $product['sale_unit'])->first();
        $quantity = $unit && $unit->operator === '*' ? $product['qty'] * $unit->operation_value : $product['qty'];

        if ($productModel->is_variant) {
            $variant = ProductVariant::where('product_id', $product['product_id'])
                ->whereHas('product', fn($q) => $q->where('code', $product['product_code']))
                ->first();
            $variant->increment('qty', $quantity);
        }

        Product_Warehouse::where([
            ['product_id', $product['product_id']],
            ['warehouse_id', $product['warehouse_id']]
        ])->increment('qty', $quantity);

        $productModel->increment('qty', $quantity);
    }

    private function updateSaleStatusIfRequired(array $data)
    {
        if ($data['change_sale_status']) {
            Sale::where('id', $data['sale_id'])->update(['sale_status' => 4]);
        }
    }

    /** End Process Sale Return "Store"*/


}


