<?php

namespace App\Services\Tenant;


use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Unit;
use Illuminate\Support\Collection;

class ProductVariantService
{

    public function getProductVariant($productId, $variantId)
    {
        return ProductVariant::FindExactProduct($productId, $variantId)->first();
    }

    public function getProductVariants(array $variantIds): Collection
    {
        return ProductVariant::whereIn('id', $variantIds)
            ->select('id', 'product_id', 'item_code')
            ->get()
            ->keyBy('id');
    }


    /**
     * Search for product by code and return relevant data --- Quotation---.
     */
    public function limsProductSearch($request): array
    {
        $todayDate = now()->toDateString();
        $productCode = trim(explode("(", $request->input('data'))[0]);

        // البحث عن المنتج ومتغيراته في استعلام واحد
        $productData = Product::with('tax')
            ->withVariantCode($productCode)
            ->where('is_active', true)
            ->first();

        if (!$productData) {
            return [];
        }

        // تحديد السعر الصحيح في حالة وجود متغير
        $productVariantId = $productData->product_variant_id;
        if ($productVariantId) {
            $productData->code = $productData->item_code;
            $productData->price += $productData->additional_price;
        }

        // تجهيز بيانات المنتج للإرجاع
        $product = [
            $productData->name,
            $productData->code,
            ($productData->promotion && $todayDate <= $productData->last_date)
                ? $productData->promotion_price
                : $productData->price,
            $productData->tax->rate ?? 0,
            $productData->tax->name ?? 'No Tax',
            $productData->tax_method,
        ];

        // إذا كان المنتج من النوع "standard"، نجلب الوحدات المرتبطة به
        if ($productData->type === 'standard') {
            $units = Unit::getUnitsForProduct($productData->unit_id, $productData->sale_unit_id);

            $product[] = $units->pluck('unit_name')->implode(",") . ',';
            $product[] = $units->pluck('operator')->implode(",") . ',';
            $product[] = $units->pluck('operation_value')->implode(",") . ',';
        } else {
            $product = array_merge($product, ['n/a,', 'n/a,', 'n/a,']);
        }

        // باقي بيانات المنتج
        array_push($product,
            $productData->id,
            $productVariantId,
            $productData->promotion,
            $productData->is_batch,
            $productData->is_imei
        );

        return $product;
    }

}

