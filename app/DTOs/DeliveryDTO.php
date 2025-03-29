<?php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class DeliveryDTO
{
    public function __construct(
        public readonly string $reference_no,
        public readonly int $sale_id,
        public readonly int $courier_id,
        public readonly string $address,
        public readonly string $delivered_by,
        public readonly string $recieved_by,
        public readonly int $status,
        public readonly ?string $note,
        public readonly ?UploadedFile $file = null
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            reference_no: $request->input('reference_no'),
            sale_id: (int) $request->input('sale_id'),
            courier_id: (int) $request->input('courier_id'),
            address: $request->input('address'),
            delivered_by: $request->input('delivered_by'),
            recieved_by: $request->input('recieved_by'),
            status: (int) $request->input('status'),
            note: $request->input('note'),
            file: $request->file('file')
        );
    }
}
