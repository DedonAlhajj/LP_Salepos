<?php

namespace App\Services\Tenant;

use App\DTOs\ProductReturnPurchaseDTO;
use App\Models\ProductReturn;
use App\Models\PurchaseProductReturn;
use App\Repositories\Tenant\TransactionRepository;
use Illuminate\Support\Collection;

class ProductReturnService
{


    private TransactionRepository $productRepository;


    public function __construct(
        TransactionRepository $productRepository,
    )
    {
        $this->productRepository = $productRepository;
    }

    public function getProductsByWarehouse(int $warehouseId): array
    {
        // جلب المنتجات من المستودع
        $products = $this->productRepository->getProductsInWarehouse($warehouseId);

        // تحويل البيانات إلى DTOs
        return array_map(fn($product) => ProductReturnPurchaseDTO::fromModel(
            product: $product['product'],
            warehouse: $product['warehouse'],
            variant: $product['variant'] ?? null
        ), $products);
    }

    public function getProductReturnData($id)
    {
        $productReturnData = ProductReturn::with([
            'product:id,name,code',
            'productVariant:id,product_id,item_code',
            'productBatch:id,product_id,batch_no',
            'unit:id,unit_code'
        ])
            ->where('return_id', $id)
            ->get(['return_id', 'product_id', 'sale_unit_id', 'variant_id', 'product_batch_id', 'imei_number', 'qty', 'tax', 'tax_rate', 'discount', 'total']);

        return $productReturnData->map(function($item) {
            return [
                'product' => $item->product->name . ' [' . $item->product->code . ']',
                'imei_number' => $item->imei_number ? 'IMEI or Serial Number: ' . $item->imei_number : null,
                'quantity' => $item->qty,
                'unit' => $item->unit->unit_code,
                'tax' => $item->tax,
                'tax_rate' => $item->tax_rate,
                'discount' => $item->discount,
                'total' => $item->total,
                'batch_no' => $item->productBatch ? $item->productBatch->batch_no : 'N/A'
            ];
        });
    }

    public function getProductReturnPurchaseData(int $returnId): Collection
    {
        $returnProducts = PurchaseProductReturn::where('return_id', $returnId)
            ->with([
                'product:id,name,code',
                'unit:id,unit_code',
                'variant:id,item_code,product_id',
                'productBatch:id,batch_no,product_id'
            ])
            ->get();

        return $returnProducts->map(function ($productReturn) {
            return [
                'name_code'   =>  "{$productReturn->product->name} ["
                    . ($productReturn->variant_id ? $productReturn->variant?->item_code : $productReturn->product->code)
                    . "]",
                'imei_number' => $productReturn->imei_number ? "<br>IMEI or Serial Number: {$productReturn->imei_number}" : '',
                'qty'         => $productReturn->qty,
                'unit_code'   => $productReturn->unit?->unit_code ?? '',
                'tax'         => $productReturn->tax,
                'tax_rate'    => $productReturn->tax_rate,
                'discount'    => $productReturn->discount,
                'subtotal'    => $productReturn->total,
                'batch_no'    => $productReturn->productBatch?->batch_no ?? 'N/A',
            ];
        });
    }

}
