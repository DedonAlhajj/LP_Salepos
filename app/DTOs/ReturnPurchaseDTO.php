<?php

namespace App\DTOs;


use App\Models\ReturnPurchase;

class ReturnPurchaseDTO
{
    public int $id;
    public string $reference_no;
    public string $date;
    public string $warehouse;
    public string $supplier;
    public string $qty;
    public float $unit_cost;
    public float $sub_total;

    public function __construct(ReturnPurchase $returnPurchase)
    {
        $this->id = $returnPurchase->id;
        $this->reference_no = $returnPurchase->reference_no;
        $this->date = $returnPurchase->created_at->format(config('date_format'));
        $this->warehouse = $returnPurchase->warehouse->name ?? 'N/A';
        $this->supplier = $returnPurchase->supplier
            ? $returnPurchase->supplier->name . ' [' . $returnPurchase->supplier->phone_number . ']'
            : 'N/A';
        $this->qty = number_format($returnPurchase->productReturns->sum('qty'), config('decimal'));
        $this->unit_cost = number_format(
            $returnPurchase->productReturns->sum('total') / max(1, $returnPurchase->productReturns->sum('qty')),
            config('decimal')
        );
        $this->sub_total = number_format($returnPurchase->productReturns->sum('total'), config('decimal'));
    }

    public static function fromModel(ReturnPurchase $returnPurchase)
    {
        return new self($returnPurchase);
    }
}

