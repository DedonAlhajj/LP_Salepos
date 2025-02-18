<?php

namespace App\Jobs;

use App\Models\Product_Warehouse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\ProductVariant;
use App\Models\Variant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $tenantId;

    public function __construct(array $data, $tenantId)
    {
        $this->data = $data;
        $this->tenantId = $tenantId;
    }

    public function handle()
    {
        try {
            // البحث عن العلامة التجارية أو إنشاؤها
            $brand = null;
            if (!empty($this->data['brand'])) {
                $brand = Brand::firstOrCreate(['title' => $this->data['brand']], ['is_active' => true]);
            }

            // البحث عن الفئة أو إنشاؤها
            $category = Category::firstOrCreate(['name' => $this->data['category']], ['is_active' => true]);

            // البحث عن الوحدة
            $unit = Unit::where('unit_code', $this->data['unit_code'])->first();
            if (!$unit) {
                Log::error("❌ كود الوحدة غير موجود: " . $this->data['unit_code']);
                return;
            }

            // إنشاء المنتج
            $product = Product::firstOrNew(['code' => $this->data['code']]);
            $product->fill([
                'name'          => $this->data['name'],
                'image'         => $this->data['image'] ?? 'zummXD2dvAtI.png',
                'type'          => $this->data['type'],
                'brand_id'      => $brand ? $brand->id : null,
                'category_id'   => $category->id,
                'unit_id'       => $unit->id,
                'purchase_unit_id' => $unit->id,
                'sale_unit_id'  => $unit->id,
                'cost'          => $this->data['cost'],
                'price'         => $this->data['price'],
                'product_details' => $this->data['product_details'],
                'tenant_id'     => $this->tenantId,
            ]);

            if (config('addons.ecommerce')) {
                $product->slug = Str::slug($this->data['name'], '-');
            }

            $product->save();

            // التعامل مع المتغيرات (إن وجدت)
            if (!empty($this->data['variant_value']) && !empty($this->data['variant_name'])) {
                $variantValues = explode(",", $this->data['variant_value']);
                $variantNames = explode(",", $this->data['variant_name']);
                $itemCodes = explode(",", $this->data['item_code'] ?? '');
                $additionalCosts = explode(",", $this->data['additional_cost'] ?? '');
                $additionalPrices = explode(",", $this->data['additional_price'] ?? '');

                foreach ($variantNames as $key => $variantName) {
                    $variant = Variant::firstOrCreate(['name' => $variantName]);

                    ProductVariant::create([
                        'product_id'       => $product->id,
                        'variant_id'       => $variant->id,
                        'position'         => $key + 1,
                        'item_code'        => $itemCodes[$key] ?? ($variantName . '-' . $this->data['code']),
                        'additional_cost'  => $additionalCosts[$key] ?? 0,
                        'additional_price' => $additionalPrices[$key] ?? 0,
                        'qty'              => 0,
                    ]);
                }
            }

            // تحديث المخزون في المستودعات
            $warehouses = Warehouse::where('is_active', true)->pluck('id');
            foreach ($warehouses as $warehouseId) {
                Product_Warehouse::updateOrCreate(
                    ['product_id' => $product->id, 'warehouse_id' => $warehouseId],
                    ['qty' => 0]
                );
            }

            Log::info("✅ تم استيراد المنتج بنجاح: " . $this->data['name']);

        } catch (\Exception $e) {
            Log::error("❌ خطأ أثناء استيراد المنتج: " . $e->getMessage());
        }
    }
}
