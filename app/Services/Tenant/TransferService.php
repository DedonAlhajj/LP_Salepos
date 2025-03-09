<?php

namespace App\Services\Tenant;

use App\Actions\SendMailAction;
use App\DTOs\TransferData;
use App\DTOs\TransferDTO;
use App\Models\Product;
use App\Models\Product_Warehouse;
use App\Models\ProductTransfer;
use App\Models\Tax;
use App\Models\Transfer;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferService
{

    protected MediaService $mailService;
    protected ProductTransferService $productTransferService;

    public function __construct(MediaService $mailService, ProductTransferService $productTransferService)
    {
        $this->mailService = $mailService;
        $this->productTransferService = $productTransferService;
    }
    public function getFilters($request)
    {
        return [
            'from_warehouse_id' => $request->input('from_warehouse_id', 0),
            'to_warehouse_id' => $request->input('to_warehouse_id', 0),
            'starting_date' => $request->input('starting_date', date("Y-m-d", strtotime("-1 year"))),
            'ending_date' => $request->input('ending_date', date("Y-m-d")),
            'lims_warehouse_list' => Warehouse::all(),
        ];
    }

    public function getTransfers(array $filters)
    {
        return Transfer::with('fromWarehouse', 'toWarehouse', 'user')
            //->filterByDateRange($filters['starting_date'], $filters['ending_date'])
            ->filterByUserAccess()
            ->fromWarehouse($filters['from_warehouse_id'])
            ->toWarehouse($filters['to_warehouse_id'])
            ->get();

    }

    public function getCreateData()
    {
        return [
            'lims_warehouse_list' => Warehouse::all(),
        ];
    }


    /** Store **/

    public function storeTransferData(TransferData $data, $document)
    {
        return DB::transaction(function () use ($data, $document) {
            $transfer = Transfer::create($data->toArray());

            if ($document) {
                $test = [];
                //$transfer->addMedia($document)->toMediaCollection('transfers');
            }

            $this->productTransferService->processProducts($transfer, $data);
            // تجهيز بيانات البريد
            $mailData = $this->prepareMailData($transfer);

            // إرسال الإيميل وتحديث حالة `is_sent`
            $isSent = $this->mailService->sendTransferMail($mailData);
            $transfer->update(['is_sent' => $isSent]);

            return 'Transfer created successfully';
        });
    }


    private function prepareMailData(Transfer $transfer): array
    {
        return [
            'date'          => $transfer->created_at->toDateString(),
            'reference_no'  => $transfer->reference_no,
            'status'        => $transfer->status,
            'total_cost'    => $transfer->total_cost,
            'shipping_cost' => $transfer->shipping_cost,
            'grand_total'   => $transfer->grand_total,
            'from_email'    => optional($transfer->fromWarehouse)->email,
            'to_email'      => optional($transfer->toWarehouse)->email,
            'from_warehouse'=> optional($transfer->fromWarehouse)->name,
            'to_warehouse'  => optional($transfer->toWarehouse)->name,
            'products'      => $this->productTransferService->getProductTransferDataStore($transfer->id),
        ];
    }


    /** Delete */


    public function deleteTransfer(int $transferId): void
    {
        $transfer = Transfer::with('productTransfers')->findOrFail($transferId);

        DB::transaction(function () use ($transfer) {
            foreach ($transfer->productTransfers as $productTransfer) {
                $this->productTransferService->reverseProductTransfer($productTransfer, $transfer);
                $productTransfer->delete();
            }

            // حذف الملف باستخدام InteractsWithMedia
            if ($transfer->hasMedia('transfers')) {
                $transfer->clearMediaCollection('transfers');
            }
            $transfer->delete();
        });
    }

    public function deleteBySelection(array $transferIds)
    {
        DB::transaction(function () use ($transferIds) {
            foreach ($transferIds as $transferId) {
                $this->deleteTransfer($transferId);
            }
        });
    }

    /** Edit **/
    public function getTransferDetails(int $transferId): TransferDTO
    {
        $transfer = Transfer::with('fromWarehouse')->findOrFail($transferId);
        $warehouses = Warehouse::get(['id', 'name']);

        // جلب جميع المنتجات المرتبطة بعملية التحويل دفعة واحدة لتجنب N+1 Query Problem
        $productTransfers = ProductTransfer::where('transfer_id', $transferId)->with(['product.unit', 'variant', 'productBatch'])->get();

        $products = $productTransfers->map(fn ($productTransfer) => $this->mapProductTransfer($productTransfer));

        return new TransferDTO($transfer, $warehouses, $products);
    }

    private function mapProductTransfer(ProductTransfer $productTransfer): array
    {
        $product = $productTransfer->product;
        $variant = $productTransfer->variant;
        $batch = $productTransfer->batch;
        $tax = Tax::where('rate', $productTransfer->tax_rate)->first();
        $units = Unit::baseOrSelf($product->unit_id)->get(['id', 'unit_name', 'operator', 'operation_value']);
        $unitData = $this->processUnits($units, $productTransfer->purchase_unit_id);

        return [
            'id' => $product->id,
            'name' => $product->name,
            'code' => $variant?->item_code ?? $product->code,
            'variant_id' => $variant?->id,
            'batch_no' => $batch?->batch_no,
            'quantity' => $productTransfer->qty,
            'net_unit_cost' => $productTransfer->net_unit_cost,
            'tax' => $productTransfer->tax,
            'total' => $productTransfer->total,
            'cost' => $this->calculateCost($product, $productTransfer, $unitData['operation_value']),
            'units' => $unitData,
            'tax_name' => $tax?->name ?? 'No Tax',
            'tax_method' => $product->tax_method,
            'imei_number' => $productTransfer->imei_number,
        ];
    }

    private function processUnits($units, $purchaseUnitId)
    {
        $operationValue = null;

        foreach ($units as $unit) {
            if ($unit->id == $purchaseUnitId) {
                $operationValue = $unit->operation_value; // خذ فقط أول قيمة
                break;
            }
        }

        return [
            'name' => optional($units->firstWhere('id', $purchaseUnitId))->unit_name ?? '',
            'operator' => optional($units->firstWhere('id', $purchaseUnitId))->operator ?? '',
            'operation_value' => is_numeric($operationValue) ? (float) $operationValue : 1, // تأكد من أنه رقم صالح
        ];
    }

    private function calculateCost(Product $product, ProductTransfer $productTransfer, $operationValue)
    {
        // التحقق من أن $operationValue رقم صحيح وإلا تعيينه إلى 1 لتجنب القسمة على صفر أو خطأ في البيانات
        $operationValue = is_numeric($operationValue) && $operationValue > 0 ? (float) $operationValue : 1;

        // التحقق من أن net_unit_cost و total و qty كلها أرقام صحيحة
        $netUnitCost = is_numeric($productTransfer->net_unit_cost) ? (float) $productTransfer->net_unit_cost : 0;
        $total = is_numeric($productTransfer->total) ? (float) $productTransfer->total : 0;
        $qty = is_numeric($productTransfer->qty) && $productTransfer->qty > 0 ? (float) $productTransfer->qty : 1;

        return $product->tax_method == 1
            ? $netUnitCost / $operationValue
            : ($total / $qty) / $operationValue;
    }

    /** Update*/
    public function updateTransfer(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // تجهيز البيانات
            $data = $request->except('document');
            $data['created_at'] = date("Y-m-d", strtotime(str_replace("/", "-", $data['created_at'])));

            $transfer = Transfer::findOrFail($id);

            // التحقق من الملفات
            if ($request->hasFile('document')) {
                $transfer->clearMediaCollection('documents');
                $transfer->addMediaFromRequest('document')->toMediaCollection('documents');
            }

            // تحديث تفاصيل التحويل
            $this->updateProductTransfers($transfer, $data);

            // تحديث التحويل
            $transfer->update($data);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception('Update Transfer failed');
        }
    }

    private function updateProductTransfers(Transfer $transfer, array $data)
    {
        $existingTransfers = ProductTransfer::where('transfer_id', $transfer->id)->get()->keyBy('product_id');

        foreach ($data['product_id'] as $index => $productId) {
            $productTransfer = $existingTransfers[$productId] ?? new ProductTransfer();

            $productTransfer->fill([
                'transfer_id' => $transfer->id,
                'product_id' => $productId,
                'variant_id' => $data['product_variant_id'][$index] ?? null,
                'product_batch_id' => $data['product_batch_id'][$index] ?? null,
                'qty' => $data['qty'][$index],
                'purchase_unit' => $data['purchase_unit'][$index],
                'net_unit_cost' => $data['net_unit_cost'][$index],
                'tax_rate' => $data['tax_rate'][$index],
                'tax' => $data['tax'][$index],
                'subtotal' => $data['subtotal'][$index],
                'imei_number' => $data['imei_number'][$index] ?? null,
            ]);

            $productTransfer->save();

            // تحديث المخزون
            app(StockService::class)->updateStockTransfer($transfer, $productTransfer, $data['status']);
        }

        // حذف المنتجات غير المستخدمة
        ProductTransfer::where('transfer_id', $transfer->id)
            ->whereNotIn('product_id', $data['product_id'])
            ->delete();
    }



}
