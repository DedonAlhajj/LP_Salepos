<?php

namespace App\Services\Tenant;


use App\DTOs\ProductReturnDTO;
use App\DTOs\ProductSearchReturnPurchaseDTO;
use App\Models\Product;
use App\Models\Unit;
use App\Repositories\Tenant\TransactionRepository;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ProductSearchService
{
    protected ProductService $productService;
    protected TransactionRepository $productRepository;
    protected UnitService $unitService;
    protected TaxCalculatorService $taxCalculatorService;

    public function __construct(
        ProductService        $productService,
        TransactionRepository $productRepository,
        UnitService           $unitService,
        TaxCalculatorService  $taxCalculatorService)
    {
        $this->productService = $productService;
        $this->productRepository = $productRepository;
        $this->unitService = $unitService;
        $this->taxCalculatorService = $taxCalculatorService;
    }

    /**
     * Search for a product by its code.
     *
     * Retrieves a product based on the provided code and returns essential product details.
     * If no product is found, returns null instead of causing an error.
     *
     * @param string $code
     * @return array|null
     */
    public function productSearch(string $code): ?array
    {
        try {
            // Find the product by code and retrieve only necessary fields
            $product = Product::where('code', $code)
                ->select('id', 'name', 'code')
                ->first();

            // If product is not found, return null
            if (!$product) {
                return null;
            }

            // Return structured product data
            return [
                $product->id,
                $product->name,
                $product->code,
            ];
        } catch (\Exception $e) {
            // Log the error and return null for graceful failure
            Log::error("Product search failed: " . $e->getMessage());
            return null;
        }
    }


    public function searchReturnProduct(string $input): array
    {
        try {
            // استخراج كود المنتج من المدخلات
            $product_code = trim(explode("(", $input)[0]);

            // البحث عن المنتج أو المتغير
            $product = $this->productRepository->findByCode($product_code);

            // الحصول على الضريبة
            $tax = $product->tax_id ? $this->taxCalculatorService->getTaxById($product->tax_id) : null;

            // الحصول على الوحدات المرتبطة بالمنتج
            $units = $this->unitService->getUnitsByProduct($product);

            // إرجاع نفس التنسيق السابق
            return ProductSearchReturnPurchaseDTO::fromModel($product, $tax, $units, $product->variant_id ?? null);

        } catch (Exception $e) {
            Log::error('Error searching for product "Return Purchase"', ['error' => $e->getMessage()]);
            throw new Exception($e);
        }
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

    public function searchProductByCodeOrVariant(string $data): ?ProductReturnDTO
    {
        try {
            $product_code = explode("|", $data);
            $product_code[0] = trim($product_code[0]);

            // البحث عن المنتج مع بيانات الضرائب والمتغيرات
            $product = Product::searchByCodeOrVariant($product_code[0])->first();

            if (!$product) {
                return null;
            }

            // تحديث السعر إذا كان المنتج متغيرًا
            if ($product->is_variant && $product->additional_cost) {
                $product->cost += $product->additional_cost;
            }

            // جلب بيانات الوحدات دفعة واحدة
            $units = Unit::whereIn('id', [$product->unit_id, $product->purchase_unit_id])
                ->get()
                ->keyBy('id');

            return new ProductReturnDTO($product, $units);
        } catch (\Exception $e) {
            Log::error("Error searching for product: " . $e->getMessage());
            return null;
        }
    }
}
