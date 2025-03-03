<?php

namespace App\Services\Tenant;


use App\DTOs\QuotationDTO;
use App\Models\Product;
use App\Models\ProductQuotation;
use App\Models\Tax;

class ProductQuotationService
{

    protected UnitService $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    public function prepareProductData($quotation)
    {
        return $quotation->productQuotations->map(fn($productQuotation) => [
            'id'               => $productQuotation->product->id,
            'name'             => $productQuotation->product->name,
            'code'             => optional($productQuotation->variant)->item_code ?? $productQuotation->product->code,
            'variant_id'       => optional($productQuotation->variant)->id,
            'batch_no'         => optional($productQuotation->productBatch)->batch_no,
            'qty'              => $productQuotation->qty,
            'net_unit_price'   => $productQuotation->net_unit_price,
            'discount'         => $productQuotation->discount,
            'tax'              => $productQuotation->tax,
            'tax_rate'         => $productQuotation->tax_rate,
            'tax_name'         => optional(Tax::where('rate', $productQuotation->tax_rate)->first())->name ?? 'No Tax',
            'subtotal'         => $productQuotation->total,
            'product_price'    => $this->calculate($productQuotation->product, $productQuotation),
            'units'            => $this->unitService->getUnits($productQuotation->product)
        ]);

    }

    public function calculate(Product $product, ProductQuotation $productQuotation): float
    {
        return ($product->tax_method == 1)
            ? $productQuotation->net_unit_price + ($productQuotation->discount / $productQuotation->qty)
            : ($productQuotation->total / $productQuotation->qty) + ($productQuotation->discount / $productQuotation->qty);
    }

    public function updateProductQuotations(int $quotationId, QuotationDTO $dto): void
    {
        // جلب المنتجات الموجودة
        $existingProducts = ProductQuotation::where('quotation_id', $quotationId)->get()->keyBy(fn($p) => $p->product_id . '-' . ($p->variant_id ?? 'null')); // تعديل هنا
        $oldProductIds = [];
        $oldProductVariantIds = [];

        // تخزين معرفات المنتجات القديمة
        foreach ($existingProducts as $product) {
            $oldProductIds[] = $product->product_id;
            if ($product->variant_id) {  // تعديل هنا
                $oldProductVariantIds[] = $product->variant_id;  // تعديل هنا
            }
        }

        // التعامل مع المنتجات الجديدة
        foreach ($dto->products as $productData) {
            $productKey = $productData['product_id'] . '-' . ($productData['variant_id'] ?? 'null'); // تعديل هنا

            if (isset($existingProducts[$productKey])) {
                // تحديث المنتج الموجود
                $existingProducts[$productKey]->update($productData);
                unset($existingProducts[$productKey]);
            } else {
                // إضافة منتج جديد
                ProductQuotation::create(array_merge(['quotation_id' => $quotationId], $productData));
            }
        }

        // حذف المنتجات القديمة التي لم تعد موجودة
        foreach ($existingProducts as $toDelete) {
            $toDelete->delete();
        }
    }

}

