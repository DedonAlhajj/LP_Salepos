<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WarehousesRequest extends FormRequest
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
                'required',
                'string',
                'max:255',
                Rule::unique('warehouses')
                    ->ignore($this->warehouse_id)
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at')

            ],
            'warehouse_id' => 'nullable',
            "phone" => 'nullable|string|max:15',
            "email" => 'required|email|max:255',
            "address" => 'nullable|string|max:255' ,

        ];
    }


}
