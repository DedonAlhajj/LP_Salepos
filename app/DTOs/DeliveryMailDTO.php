<?php

namespace App\DTOs;


class DeliveryMailDTO
{
    public function __construct(
        public string $email,
        public string $date,
        public string $deliveryReference,
        public string $saleReference,
        public int $status,
        public string $customerName,
        public string $address,
        public string $phoneNumber,
        public ?string $note,
        public string $preparedBy,
        public string $deliveredBy,
        public string $recievedBy,
        public array $products
    ) {}

    public static function fromEntities($delivery, $sale, $customer, $products): self
    {
        return new self(
            email: $customer->email,
            date: now()->format(config('date_format')),
            deliveryReference: $delivery->reference_no,
            saleReference: $sale->reference_no,
            status: $delivery->status,
            customerName: $customer->name,
            address: trim("{$customer->address}, {$customer->city}"),
            phoneNumber: $customer->phone_number,
            note: $delivery->note,
            preparedBy: $delivery->user->name,
            deliveredBy: $delivery->delivered_by ?: 'N/A',
            recievedBy: $delivery->recieved_by ?: 'N/A',
            products: array_map(fn ($product) => [
                'code' => $product['code'],
                'name' => $product['name'],
                'qty' => $product['qty']
            ], $products)
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'date' => $this->date,
            'delivery_reference_no' => $this->deliveryReference,
            'sale_reference_no' => $this->saleReference,
            'status' => $this->status,
            'customer_name' => $this->customerName,
            'address' => $this->address,
            'phone_number' => $this->phoneNumber,
            'note' => $this->note,
            'prepared_by' => $this->preparedBy,
            'delivered_by' => $this->deliveredBy,
            'recieved_by' => $this->recievedBy,
            'products' => $this->products,
        ];
    }
}

