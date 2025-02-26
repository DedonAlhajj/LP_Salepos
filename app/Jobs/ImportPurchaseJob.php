<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\Product_Warehouse;
use App\Models\Unit;
use App\Models\Tax;
use App\Models\Purchase;
use App\Models\ProductPurchase;
use App\Models\ProductWarehouse;
use App\Services\Tenant\PurchaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ImportPurchaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $purchaseData;

    /**
     * إنشاء الكلاس مع بيانات الشراء.
     */
    public function __construct(array $purchaseData)
    {
        $this->purchaseData = $purchaseData;
    }

    /**
     * تنفيذ المهمة.
     */
    public function handle(PurchaseService $purchaseService)
    {
        try {
            Log::info('🔄 بدء استيراد الشراء', ['data' => $this->purchaseData]);

            $data = $this->purchaseData;

            // البحث عن المنتج
            $product = Product::where('code', $data['product_code'])->first();
            if (!$product) {
                Log::error("❌ المنتج غير موجود: " . $data['product_code']);
                return;
            }

            // البحث عن الوحدة
            $unit = Unit::where('unit_code', $data['purchase_unit'])->first();
            if (!$unit) {
                Log::error("❌ وحدة الشراء غير موجودة: " . $data['purchase_unit']);
                return;
            }

            // البحث عن الضريبة
            $taxRate = 0;
            if (strtolower($data['tax_name']) !== "no tax") {
                $tax = Tax::where('name', $data['tax_name'])->first();
                if (!$tax) {
                    Log::error("❌ اسم الضريبة غير موجود: " . $data['tax_name']);
                    return;
                }
                $taxRate = $tax->rate;
            }
            // استدعاء خدمة `storePurchase` لمعالجة البيانات وإنشاء الشراء
            $purchase = $purchaseService->storePurchase($this->purchaseData);

            Log::info('✅ تم استيراد الشراء بنجاح', ['purchase_id' => $purchase->id]);
        } catch (\Exception $e) {
            Log::error('❌ فشل استيراد الشراء', ['error' => $e->getMessage()]);
        }
    }

}

