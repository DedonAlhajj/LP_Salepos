<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
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
                Rule::unique('departments')
                    ->ignore($this->department_id)
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at'),
            ],
            'department_id' => ['nullable', 'exists:departments,id'],
        ];
    }




}
