<?php

namespace App\DTOs;
namespace App\DTOs;

class ProductSearchDTO
{
    public function __construct(
        public string $name,
        public string $code,
        public float $price,
        public float $taxRate,
        public string $taxName,
        public string $taxMethod,
        public string $unitName,
        public string $unitOperator,
        public string $unitOperationValue,
        public int $productId,
        public ?int $productVariantId,
        public ?bool $promotion,
        public ?bool $isImei
    ) {}

    public function toArray(): array
    {
        return [
            $this->name,
            $this->code,
            $this->price,
            $this->taxRate,
            $this->taxName,
            $this->taxMethod,
            $this->unitName,
            $this->unitOperator,
            $this->unitOperationValue,
            $this->productId,
            $this->productVariantId,
            $this->promotion,
            $this->isImei,
        ];
    }
}
