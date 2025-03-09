<?php

namespace App\Jobs;

use App\Actions\SendMailAction;
use App\Models\Product;
use App\Models\Product_Warehouse;
use App\Models\ProductTransfer;
use App\Models\Transfer;
use App\Models\Unit;
use App\Models\Tax;
use App\Models\Purchase;
use App\Models\ProductPurchase;
use App\Models\ProductWarehouse;
use App\Services\Tenant\ProductTransferService;
use App\Services\Tenant\PurchaseService;
use App\Services\Tenant\StockService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ImportTransferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $transferData;

    public function __construct(array $data, array $transferData)
    {
        $this->data = $data;
        $this->transferData = $transferData;
    }

    public function handle(ProductTransferService $productTransferService, StockService $inventoryService)
    {
        try {
            $product = Product::with('warehouses')->where('code', $this->data['product_code'])->firstOrFail();
            $unit = Unit::where('unit_code', $this->data['purchase_unit'])->firstOrFail();
            $tax = $this->data['tax_name'] ? Tax::where('name', $this->data['tax_name'])->first() : null;

            // Create transfer and update transfer data
            $transfer = $productTransferService->createOrUpdateTransfer($this->transferData);

            // Handle inventory update
            $inventoryService->updateInventory($product, $unit, $this->data['quantity'], $this->transferData);

            // Handle product transfer details
            $productTransferService->saveProductTransferDetails($transfer, $product, $unit, $tax);

            // Queue email
            //SendMailAction::dispatch($this->data, Transfer::class);

        } catch (\Exception $e) {
            Log::error('حدث خطأ في تحديث المخزون', [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);
            Log::error("❌ فشل استيراد عملية التحويل: " . $e->getMessage(), [
                'data' => $this->data,
                'transferData' => $this->transferData
            ]);
        }
    }
}
