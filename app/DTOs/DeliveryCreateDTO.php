<?php

namespace App\DTOs;

class DeliveryCreateDTO
{
    public function __construct(
        public string $referenceNo,
        public string $saleReference,
        public ?int $status,
        public ?string $deliveredBy,
        public ?string $recievedBy,
        public string $customerName,
        public string $address,
        public ?string $note,
        public ?int $courierId
    ) {}

    public static function fromExistingDelivery($delivery, $customerSale): self
    {
        return new self(
            referenceNo: $delivery->reference_no,
            saleReference: $customerSale->reference_no,
            status: $delivery->status,
            deliveredBy: $delivery->delivered_by,
            recievedBy: $delivery->recieved_by,
            customerName: $customerSale->name,
            address: $delivery->address,
            note: $delivery->note,
            courierId: $delivery->courier_id
        );
    }

    public static function fromNewDelivery($customerSale): self
    {
        return new self(
            referenceNo: 'dr-' . date("Ymd") . '-' . date("his"),
            saleReference: $customerSale->reference_no,
            status: null,
            deliveredBy: null,
            recievedBy: null,
            customerName: $customerSale->name,
            address: trim("{$customerSale->address} {$customerSale->city} {$customerSale->country}"),
            note: null,
            courierId: null
        );
    }
}
