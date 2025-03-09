<?php

namespace App\DTOs;

class TransferDTO
{
    public function __construct(
        public object $transfer,
        public object $warehouses,
        public object $products
    ) {}

    public function toArray(): array
    {
        return [
            'transfer' => $this->transfer,
            'warehouses' => $this->warehouses,
            'products' => $this->products,
        ];
    }
}
