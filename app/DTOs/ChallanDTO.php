<?php

namespace App\DTOs;

class ChallanDTO
{
    public function __construct(
        public int $id,
        public string $date,
        public string $reference_no,
        public array $sale_references,
        public string $courier,
        public string $status,
        public ?string $closing_date,
        public float $total_amount,
        public ?string $created_by,
        public ?string $closed_by
    ) {}

    public static function fromModel($challan)
    {
        return new self(
            id: $challan->id,
            date: $challan->created_at->format(config('date_format') . ' h:i:s'),
            reference_no: 'DC-' . $challan->reference_no,
            sale_references: $challan->packingSlips->map(fn($slip) => $slip->sale->reference_no)->toArray(),
            courier: $challan->courier->name . ' [' . $challan->courier->phone_number . ']',
            status: $challan->status,
            closing_date: $challan->closing_date ? $challan->closing_date->format('d/m/Y') : 'N/A',
            total_amount:  array_sum(json_decode($challan->amount_list, true) ?? []),
            created_by: $challan->createdBy->name ?? 'N/A',
            closed_by: $challan->closedBy->name ?? 'N/A'
        );
    }
}

