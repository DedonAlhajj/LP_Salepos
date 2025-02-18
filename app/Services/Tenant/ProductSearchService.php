<?php

namespace App\Services\Tenant;


use App\Models\Product;
use Illuminate\Support\Facades\Config;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ProductSearchService
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function searchByCode(string $productCode): array
    {
        // البحث عن المنتج الأساسي
        $product = Product::with('variants')
            ->where('code', $productCode)
            ->first();

        if (!$product) {
            // البحث عن المنتج كمتغير (Variant)
            $product = Product::whereHas('variants', function ($query) use ($productCode) {
                $query->where('item_code', $productCode);
            })->with('variants')->first();
        }

        if (!$product) {
            return [];
        }

        return $this->formatProductData($product);
    }

    private function formatProductData(Product $product): array
    {
        $products = [];

        foreach ($product->variants ?? [$product] as $variant) {
            $variantId = $variant->id !== $product->id ? $variant->id : null;
            $additionalPrice = $variantId ? $variant->additional_price : 0;

            $barcodeGenerator = new BarcodeGeneratorPNG();

            $products[] = [
                'name' => $product->name,
                'code' => $variantId ? $variant->item_code : $product->code,
                'price' => $product->price + $additionalPrice,
                'barcode' => $barcodeGenerator->getBarcode($product->code, BarcodeGeneratorPNG::TYPE_CODE_128),
                'promotion_price' => $product->promotion_price,
                'currency' => Config::get('currency', 'USD'), // القيمة الافتراضية USD
                'currency_position' => Config::get('currency_position', 'left'), // القيمة الافتراضية left
                'quantity' => $product->qty,
                'product_id' => $product->id,
                'variant_id' => $variantId,
            ];
        }

        return $products;
    }

    public function searchProduct(string $data): array
    {
        $product_code = explode(" (", $data)[0];

        $product = $this->productService->getProductWhere($product_code);

        return [
            'name' => $product->name,
            'code' => $product->code,
            'qty' => $product->qty,
            'price' => $product->price,
            'id' => $product->id,
        ];
    }
}
