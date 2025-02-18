<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // يمكنك التحكم في من يمكنه تحديث المنتج هنا
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:products,id'],
            'name' => [
                'max:255',
                Rule::unique('products')
                    ->ignore($this->id)
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at'), // دعم soft delete
            ],
            'code' => [
                'max:255',
                Rule::unique('products')->ignore($this->id)
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at'),
            ],
            'image' => ['nullable', 'array'],
            'image.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,xlsx', 'max:5120'],
            'is_variant' => ['sometimes', 'boolean'],
            'variant_name' => ['nullable', 'array'],
            'variant_name.*' => ['string', 'max:255'],
            'product_id' => ['nullable', 'array'],
            'variant_id' => ['nullable', 'array'],
            'product_qty' => ['nullable', 'array'],
            'unit_price' => ['nullable', 'array'],
            'warehouse_id' => ['nullable', 'array'],
            'diff_price' => ['nullable', 'array'],
        ];
    }
}
