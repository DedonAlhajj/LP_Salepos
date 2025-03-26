<?php

namespace App\DTOs;


use App\Models\PackingSlip;
use App\Models\Sale;
use Illuminate\Support\Collection;

class PackingSlipInvoiceDTO
{
    public function __construct(
        public Collection $packingSlip,
        public Sale $sale,
        public Collection $products
    ) {}
}
