<?php

namespace App\DTOs;
use App\Models\Product;
use App\Models\Unit;


class ReturnUpdateDTO
{
    public function __construct(
        public int $returnId,
        public array $data,
        public ?\Illuminate\Http\UploadedFile $document = null
    ) {}

    public static function fromRequest(\Illuminate\Http\Request $request, int $id): self
    {
        return new self(
            returnId: $id,
            data: $request->except('document'),
            document: $request->file('document')
        );
    }
}
