<?php

namespace App\Services\Tenant;

use App\DTOs\ProductTransferDTO;
use App\Models\Product;
use App\Models\Product_Warehouse;
use App\Models\ProductTransfer;
use App\Models\ProductVariant;
use App\Models\Tax;
use App\Models\Transfer;
use App\Models\Unit;

class ProductTransferService
{

    protected WarehouseService $warehouseService;
    public function __construct(WarehouseService $warehouseService) {
        $this->warehouseService = $warehouseService;
    }


    public function getProductTransferData(int $transferId): array
    {
        $productTransfers = ProductTransfer::with([
            'product:id,name,code',
            'purchaseUnit:id,unit_code',
            'productBatch:id,batch_no'
        ])->where('transfer_id', $transferId)->get();

        // تحويل البيانات
        $mappedData = $productTransfers->map(fn ($transfer) => $this->mapProductTransfer($transfer))->toArray();

        // إعادة ترتيب البيانات باستخدام `array_map()`
        return array_map(null, ...$mappedData);
    }

    private function mapProductTransfer(ProductTransfer $transfer): array
    {
        $product = $transfer->product;
        $unit = $transfer->purchaseUnit;
        $batch = $transfer->productBatch;

        // معالجة الكود في حال وجود `variant_id`
        if ($transfer->variant_id) {
            $variant = ProductVariant::select('item_code')
                ->FindExactProduct($transfer->product_id, $transfer->variant_id)
                ->first();
            $product->code = $variant->item_code ?? $product->code;
        }

        $productName = "{$product->name} [{$product->code}]";
        if ($transfer->imei_number) {
            $productName .= "<br>IMEI or Serial Number: {$transfer->imei_number}";
        }

        return [
            $productName,
            $transfer->qty,
            $unit->unit_code ?? 'N/A',
            $transfer->tax,
            $transfer->tax_rate,
            $transfer->total,
            $batch->batch_no ?? 'N/A'
        ];
    }

    /*** STore   */

    public function processProducts($transfer, $data)
    {
        // تحويل كائن TransferData إلى مصفوفة
        $dataArray = $data->toArray();

        foreach ($dataArray['product_id'] as $index => $product_id) {
            // تمرير البيانات كمصفوفة بدلاً من كائن
            $productData = ProductTransferDTO::fromRequest($dataArray, $index);
            $this->updateProductWarehouse($productData);
            $this->createProductTransfer($transfer, $productData);
        }
    }

    private function updateProductWarehouse(ProductTransferDTO $productData)
    {
        Product_Warehouse::where('product_id', $productData->product_id)
            ->decrement('qty', $productData->qty);
    }

    private function createProductTransfer($transfer, ProductTransferDTO $productData)
    {
        ProductTransfer::create(array_merge(
            $productData->toArray(),
            ['transfer_id' => $transfer->id]
        ));
    }

    public function getProductTransferDataStore(int $transferId): array
    {
        // جلب كل بيانات `ProductTransfer` مع العلاقات اللازمة لتجنب استعلامات متكررة
        $productTransfers = ProductTransfer::with([
            'product',
            'unit',
            'variant:id,product_id,item_code',
            'productBatch:id,batch_no'
        ])->where('transfer_id', $transferId)->get();

        return $productTransfers->map(fn($productTransfer) => $this->mapProductTransferData($productTransfer))->toArray();
    }

    private function mapProductTransferData(ProductTransfer $productTransfer): array
    {
        $product = $productTransfer->product;
        $unit = $productTransfer->unit;
        $variant = $productTransfer->variant;
        $productBatch = $productTransfer->productBatch;

        return [
            'product' => $this->formatProductName($product, $variant),
            'imei_number' => $this->formatImeiNumber($productTransfer->imei_number),
            'qty' => $productTransfer->qty,
            'unit' => $unit->unit_code ?? 'N/A',
            'tax' => $productTransfer->tax,
            'tax_rate' => $productTransfer->tax_rate,
            'total' => $productTransfer->total,
            'batch_no' => $productBatch->batch_no ?? 'N/A',
        ];
    }

    private function formatProductName(Product $product, ?ProductVariant $variant): string
    {
        $code = $variant?->item_code ?? $product->code;
        return "{$product->name} [{$code}]";
    }

    private function formatImeiNumber(?string $imeiNumber): string
    {
        return $imeiNumber ? "IMEI or Serial Number: $imeiNumber" : '';
    }


    /** Delete*/

    public function reverseProductTransfer(ProductTransfer $productTransfer, $transfer): void
    {
        $quantity = $this->calculateQuantity($productTransfer);

        if ($transfer->status === 1) {
            // استرجاع الكمية للمستودع المصدر وخصمها من المستودع المستلم
            $this->warehouseService->updateWarehouseStock($productTransfer, $transfer->from_warehouse_id, $quantity, 'increase');
            $this->warehouseService->updateWarehouseStock($productTransfer, $transfer->to_warehouse_id, $quantity, 'decrease');
        } elseif ($transfer->status === 3) {
            // استرجاع الكمية فقط للمستودع المصدر
            $this->warehouseService->updateWarehouseStock($productTransfer, $transfer->from_warehouse_id, $quantity, 'increase');
        }
    }

    private function calculateQuantity(ProductTransfer $productTransfer): float
    {
        $unit = Unit::find($productTransfer->purchase_unit_id);

        return ($unit->operator === '*')
            ? $productTransfer->qty * $unit->operation_value
            : $productTransfer->qty / $unit->operation_value;
    }


    public function createOrUpdateTransfer(array $transferData)
    {
        // تحديث أو إنشاء عملية التحويل
        $transfer = Transfer::updateOrCreate(
            [
                'reference_no' => $transferData['reference_no'],
                'from_warehouse_id' => $transferData['from_warehouse_id'],
                'to_warehouse_id' => $transferData['to_warehouse_id']
            ],
            [
                'status' => $transferData['status'],
                'total_qty' => $transferData['total_qty'],
                'total_tax' => $transferData['total_tax'],
                'total_cost' => $transferData['total_cost'],
                'item' => $transferData['item'],
                'grand_total' => $transferData['grand_total'],
                'shipping_cost' => $transferData['shipping_cost'],
                'note' => $transferData['note'],
            ]
        );

        return $transfer;
    }

    public function saveProductTransferDetails(Transfer $transfer,$product, Unit $unit, ?Tax $tax)
    {
        // حساب الضرائب والتكلفة الإجمالية
        $netUnitCost = $product->net_unit_cost;  // افترض أن المنتج يحتوي على هذا الحقل
        $productTax = $tax ? ($netUnitCost * ($tax->rate / 100) * $unit->quantity) : 0;
        $total = ($netUnitCost * $unit->quantity) + $productTax;

        // حفظ تفاصيل التحويل للمنتج
        ProductTransfer::create([
            'transfer_id' => $transfer->id,
            'product_id' => $product->id,
            'qty' => $unit->quantity,
            'purchase_unit_id' => $unit->id,
            'net_unit_cost' => $netUnitCost,
            'tax_rate' => $tax ? $tax->rate : 0,
            'tax' => $productTax,
            'total' => $total,
        ]);

        // تحديث بيانات التحويل
        $transfer->update([
            'total_qty' => $transfer->total_qty + $unit->quantity,
            'total_tax' => $transfer->total_tax + $productTax,
            'total_cost' => $transfer->total_cost + $total,
            'item' => $transfer->item + 1,
            'grand_total' => $transfer->total_cost + $transfer->shipping_cost,
        ]);
    }


}


