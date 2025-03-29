<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

class DeliveryEditDTO
{
    public function __construct(
        public int $deliveryId,
        public string $referenceNo,
        public int $status,
        public int $courierId,
        public string $deliveredBy,
        public string $recievedBy,
        public string $address,
        public string $note,
        public ?UploadedFile $file = null
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            deliveryId: (int) $request->delivery_id,
            referenceNo: (string) $request->reference_no,
            status: (int) $request->status,
            courierId: (int) $request->courier_id,
            deliveredBy: (string) $request->delivered_by,
            recievedBy: (string) $request->recieved_by,
            address: (string) $request->address,
            note: (string) $request->note,
            file: $request->file
        );
    }
}
