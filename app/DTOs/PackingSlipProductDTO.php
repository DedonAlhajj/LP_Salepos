<?php

namespace App\DTOs;

namespace App\DTOs;

class PackingSlipProductDTO
{
    public int $packingSlipId;
    public int $saleId;
    public int $warehouseId;
    public string $saleType;
    public array $products;

    public function __construct(int $packingSlipId, int $saleId, int $warehouseId, string $saleType, array $products)
    {
        $this->packingSlipId = $packingSlipId;
        $this->saleId = $saleId;
        $this->warehouseId = $warehouseId;
        $this->saleType = $saleType;
        $this->products = $products;
    }
}
