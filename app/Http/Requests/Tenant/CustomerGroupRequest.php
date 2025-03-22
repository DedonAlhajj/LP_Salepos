<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerGroupRequest extends FormRequest
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
            "customer_group_id" => ['nullable', 'exists:customer_groups,id'],
            "percentage" => ['required', 'string', 'max:255'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('customer_groups')
                    ->ignore($this->customer_group_id)
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at')

            ],
        ];
    }




}
