<?php

namespace App\Http\Requests\Tenant;

use App\Rules\UniqueSubdomain;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'max:255', Rule::unique('products')
                ->where('tenant_id', tenant('id'))
                ->whereNull('deleted_at')],
            'name' => ['required', 'max:255', Rule::unique('products')
                ->where('tenant_id', tenant('id'))
                ->whereNull('deleted_at')],
            'type' => ['required', 'in:simple,combo,digital,service'],
            'image' => ['nullable', 'array'],
            'image.*' => ['file', 'mimes:jpeg,png,jpg,gif,svg'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,xlsx'],
            'variant_option' => ['nullable', 'array'],
            'variant_value' => ['nullable', 'array'],
            'starting_date' => ['nullable', 'date'],
            'last_date' => ['nullable', 'date'],
            'is_variant' => ['nullable', 'boolean'],
            'is_initial_stock' => ['nullable', 'boolean'],
            'stock_warehouse_id' => ['nullable', 'array'],
            'stock' => ['nullable', 'array'],
        ];

    }

    public function messages()
    {
        return [

        ];
    }
}
