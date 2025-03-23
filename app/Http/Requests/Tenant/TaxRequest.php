<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaxRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {

        return [
            'name' => [
                'max:255',
                Rule::unique('taxes') ->ignore($this->tax_id)
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at')
            ],
            'rate' => 'numeric|min:0|max:100',
            'tax_id' => ['nullable', 'exists:taxes,id'],

        ];
    }




}
