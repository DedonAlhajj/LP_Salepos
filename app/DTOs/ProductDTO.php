<?php

namespace App\DTOs;
use App\Models\Product;
use App\Models\Unit;

class ProductDTO
{
    public string $name;
    public string $code;
    public float $cost;
    public int $tax_rate;
    public string $tax_name;
    public string $tax_method;
    public string $unit_names;
    public string $unit_operators;
    public string $unit_values;
    public int $id;
    public bool $is_batch;
    public bool $is_imei;

    public function __construct(Product $product, $units)
    {
        $this->name = $product->name;
        $this->code = $product->is_variant ? $product->item_code : $product->code;
        $this->cost = $product->cost;
        $this->tax_rate = $product->tax_rate ?? 0;
        $this->tax_name = $product->tax_name ?? 'No Tax';
        $this->tax_method = $product->tax_method;
        $this->id = $product->id;
        $this->is_batch = $product->is_batch;
        $this->is_imei = $product->is_imei;

        $this->setUnits($product, $units);
    }

    private function setUnits(Product $product, $units)
    {
        $unit_names = [];
        $unit_operators = [];
        $unit_values = [];

        foreach ($units as $unit) {
            if ($product->purchase_unit_id == $unit->id) {
                array_unshift($unit_names, $unit->unit_name);
                array_unshift($unit_operators, $unit->operator);
                array_unshift($unit_values, $unit->operation_value);
            } else {
                $unit_names[] = $unit->unit_name;
                $unit_operators[] = $unit->operator;
                $unit_values[] = $unit->operation_value;
            }
        }

        $this->unit_names = implode(",", $unit_names) . ',';
        $this->unit_operators = implode(",", $unit_operators) . ',';
        $this->unit_values = implode(",", $unit_values) . ',';
    }

    public function toArray()
    {
        return [
            $this->name,
            $this->code,
            $this->cost,
            $this->tax_rate,
            $this->tax_name,
            $this->tax_method,
            $this->unit_names,
            $this->unit_operators,
            $this->unit_values,
            $this->id,
            $this->is_batch,
            $this->is_imei
        ];
    }
}
