<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnitRequest extends FormRequest
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
            'unit_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units')
                    ->ignore($this->unit_id)
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at')

            ],
            'unit_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units')
                    ->ignore($this->unit_id)
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at')

            ],
            'unit_id' => ['nullable', 'exists:units,id'],
            'operator' => ['nullable', 'string', 'max:255'],
            'base_unit' => ['nullable', 'integer'],
            'operation_value' => ['nullable',  'numeric', 'min:0'],
        ];
    }




}
