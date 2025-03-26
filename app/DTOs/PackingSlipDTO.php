<?php

namespace App\DTOs;


use App\Models\PackingSlip;
use App\Services\Tenant\PackingSlipService;

class PackingSlipDTO
{
    public function __construct(
        public int $id,
        public string $reference,
        public ?string $saleReference,
        public ?string $deliveryReference,
        public float $amount,
        public string $itemList,
        public string $status,
        public PackingSlip $packingSlip,
    ) {}

    public static function fromModel($packingSlip): self
    {
        return new self(
            id: $packingSlip->id,
            reference: 'P' . $packingSlip->reference_no,
            saleReference: optional($packingSlip->sale)->reference_no,
            deliveryReference: optional($packingSlip->delivery)->reference_no,
            amount: $packingSlip->amount,
            itemList: PackingSlipService::formatProducts($packingSlip),
            status: $packingSlip->status,
            packingSlip:$packingSlip,
        );
    }
    // ✅ إضافة دالة لتحويل الكائن إلى مصفوفة
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'sale_reference' => $this->saleReference,
            'delivery_reference' => $this->deliveryReference,
            'amount' => $this->amount,
            'item_list' => $this->itemList,
            'status' => $this->status,
            'packingSlip'=> $this->packingSlip,
        ];
    }
}
