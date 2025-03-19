<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomFieldRequest extends FormRequest
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
            'entity_type' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:text,select,number,date',
            'default_value_1' => 'nullable|string',
            'default_value_2' => 'nullable|string',
            'option_value' => 'nullable|string',
            'grid_value' => 'nullable|integer|min:1|max:12',
            'is_table' => 'sometimes|boolean',
            'is_invoice' => 'sometimes|boolean',
            'is_required' => 'sometimes|boolean',
            'is_admin' => 'sometimes|boolean',
            'is_disable' => 'sometimes|boolean',
        ];
    }

//{"is_table":["The is table field must be true or false."],"is_invoice":["The is invoice field must be true or false."]}


}
