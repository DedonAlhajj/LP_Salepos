<?php

namespace App\Services\Tenant;


use App\DTOs\ProductSearchDTO;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tax;
use App\Models\Unit;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProductVariantService
{

    protected UnitService $unitService;


    public function __construct(
        UnitService $unitService,
    )
    {
        $this->unitService = $unitService;
    }
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


    public function searchProduct(string $productCode): ProductSearchDTO
    {
        try {
            $todayDate = now()->toDateString();
            $productCode = trim(explode("(", $productCode)[0]);

            // البحث عن المنتج سواء كان أساسيًا أو متغيرًا
            $product = Product::where('code', $productCode)->first();
            $productVariantId = null;

            if (!$product) {
                $product = Product::join('product_variants', 'products.id', '=', 'product_variants.product_id')
                    ->select('products.*', 'product_variants.id as product_variant_id', 'product_variants.item_code', 'product_variants.additional_price')
                    ->where('product_variants.item_code', $productCode)
                    ->firstOrFail();

                $product->code = $product->item_code;
                $product->price += $product->additional_price;
                $productVariantId = $product->product_variant_id;
            }

            // حساب السعر بناءً على الترويج
            $price = ($product->promotion && $todayDate <= $product->last_date)
                ? $product->promotion_price
                : $product->price;

            // جلب بيانات الضريبة
            $tax = $product->tax_id ? Tax::find($product->tax_id) : null;
            $taxRate = $tax?->rate ?? 0;
            $taxName = $tax?->name ?? 'No Tax';

            // جلب بيانات الوحدات
            if ($product->type === 'standard') {
                $unitDetails = $this->unitService->getUnitDetails($product); // استخراج منطق الوحدات في دالة منفصلة
            } else {
                $unitDetails = [
                    'unitName' => ['n/a'],
                    'unitOperator' => ['n/a'],
                    'unitOperationValue' => ['n/a']
                ];
            }

            return new ProductSearchDTO(
                name: $product->name,
                code: $product->code,
                price: $price,
                taxRate: $taxRate,
                taxName: $taxName,
                taxMethod: $product->tax_method,
                unitName: implode(",", $unitDetails['unitName']) . ',',
                unitOperator: implode(",", $unitDetails['unitOperator']) . ',',
                unitOperationValue: implode(",", $unitDetails['unitOperationValue']) . ',',
                productId: $product->id,
                productVariantId: $productVariantId,
                promotion: $product->promotion,
                isImei: $product->is_imei
            );

        } catch (Exception $e) {
            Log::error("Product not found: " . $e->getMessage());
            throw new Exception("Product not found.");
        }
    }


}

