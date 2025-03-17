<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeEditRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('employees')
                    ->ignore($this->employee_id)
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at'),
            ],
            'employee_id' => 'nullable|integer',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'department_id' => 'nullable|exists:departments,id',
            'staff_id' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:100000',
        ];
    }

    public function  messages(): array
    {
        return [
            'employee_name' => 'The employee name is required and must be a string with a maximum length of 255 characters.',

            'name' => [
                'nullable' => 'The name field is optional.',
                'string' => 'The name must be a valid string.',
                'max' => 'The name must not exceed 255 characters.',
                'unique' => 'The name must be unique in the users table for the specified tenant.',
                'where' => 'The name must be unique for the specified tenant and must not have been soft deleted.',
            ],

            'email' => [
                'required' => 'The email is required.',
                'email' => 'The email must be a valid email address.',
                'max' => 'The email must not exceed 255 characters.',
                'unique' => 'The email must be unique in the employees and users tables for the specified tenant.',
                'where' => 'The email must be unique for the specified tenant and must not have been soft deleted.',
            ],

            'phone_number' => 'The phone number is required and must be a string with a maximum length of 20 characters.',

            'address' => 'The address is optional but must be a string with a maximum length of 255 characters.',

            'city' => 'The city is optional but must be a string with a maximum length of 100 characters.',

            'country' => 'The country is optional but must be a string with a maximum length of 100 characters.',

            'department_id' => 'The department ID is optional, but if provided, it must exist in the departments table.',

            'warehouse_id' => 'The warehouse ID is optional, but if provided, it must exist in the warehouses table.',

            'biller_id' => 'The biller ID is optional, but if provided, it must exist in the billers table.',

            'role_id' => 'The role ID is optional, but if provided, it must exist in the roles table.',

            'user' => 'The user field must be a boolean value.',

            'staff_id' => 'The staff ID is optional but must be a valid string if provided.',

            'password' => 'The password is optional but must be at least 6 characters long if provided.',

            'image' => 'The image must be an image file and can only be in jpg, jpeg, png, or gif formats. The maximum file size is 100MB.',
        ];

    }


}
