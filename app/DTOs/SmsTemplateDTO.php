<?php

namespace App\DTOs;

use App\Http\Requests\Tenant\SmsTemplateRequest;

class SmsTemplateDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $content,
        public readonly ?bool $is_default,
        public readonly ?bool $is_default_ecommerce,
        public readonly ?int $smstemplate_id,
    ) {}

    public static function fromRequest(SmsTemplateRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            content: $request->validated('content'),
            is_default: $request->validated('is_default', false),
            is_default_ecommerce: $request->validated('is_default_ecommerce', false),
            smstemplate_id: $request->validated('smstemplate_id'),
        );
    }


    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
