<?php

namespace App\DTOs;

use App\Models\Product_Sale;

class ProductSaleDTO
{
    public int $id; // ✅ إضافة id هنا
    public string $name;
    public string $code;
    public ?string $batch_no;
    public int $qty;
    public float $net_unit_price;
    public float $discount;
    public float $tax;
    public float $total;
    public ?string $unit_name;
    public ?string $tax_name;
    public string $tax_method;
    public float $unit_tax_value;
    public float $tax_value;
    public float $subtotal;
    public ?string $imei_number;
    public ?int $variant_id;
    public float $product_price;
    public float $unit_price;

    public function __construct(Product_Sale $productSale)
    {
        $product = $productSale->product;
        $variant = $productSale->variant;
        $batch = $productSale->batch;
        $tax = $productSale->tax;

        $this->id = $product->id; // ✅ تعيين id هنا
        $this->name = $product->name;
        $this->code = $variant ? $variant->item_code : $product->code;
        $this->batch_no = $batch?->batch_no ?? 'N/A';
        $this->qty = $productSale->qty - $productSale->return_qty;
        $this->net_unit_price = $productSale->net_unit_price;
        $this->discount = $productSale->discount;
        $this->tax = $productSale->tax;
        $this->total = $productSale->total;
        $this->unit_name = $product->type === 'standard' ? $productSale->unit->unit_name : 'n/a';
        $this->tax_name = $tax?->name ?? 'No Tax';
        $this->tax_method = $product->tax_method;
        $this->unit_tax_value = $productSale->tax / $productSale->qty;
        $this->tax_value = $productSale->tax;
        $this->subtotal = $productSale->total;
        $this->imei_number = $productSale->imei_number;
        $this->variant_id = $variant?->id;
        $this->product_price = $this->calculateProductPrice($product, $productSale);
        $this->unit_price = $this->calculateUnitPrice($productSale);
    }

    private function calculateProductPrice($product, $productSale)
    {
        return ($product->tax_method == 1)
            ? ($productSale->net_unit_price + ($productSale->discount / $productSale->qty))
            : (($productSale->total / $productSale->qty) + ($productSale->discount / $productSale->qty));
    }

    private function calculateUnitPrice($productSale)
    {
        return $productSale->total / $productSale->qty; // ✅ حساب `unit_price`
    }
}
