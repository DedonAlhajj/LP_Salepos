<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterTenantRequest extends FormRequest
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
    public function rules()
    {
        $userId = $this->route('user'); // احصل على معرف المستخدم إذا كان موجودًا (في التعديل)

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users')
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at')
                    ->ignore($userId), // استثناء السجل الحالي
            ],
            'password' => $userId ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed', // كلمة المرور ليست مطلوبة عند التعديل
            'phone' => 'nullable', 'string', 'max:15',
            'phone_number' => [
                'nullable', 'string', 'max:15',
                Rule::unique('customers')
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at')

            ],
            'company_name' => 'nullable|string|max:255',
            'role' => 'required|exists:roles,name',
            'biller_id' => 'nullable|exists:billers,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_group_id' => 'nullable|exists:customer_groups,id',
            'tax_no' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }



    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name must not exceed 255 characters.',

            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email must not exceed 255 characters.',
            'email.unique' => 'This email address is already in use.',

            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a valid string.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',

            'phone_number.required' => 'The phone number field is required.',
            'phone_number.string' => 'The phone number must be a valid string.',
            'phone_number.max' => 'The phone number must not exceed 15 characters.',
            'phone_number.unique' => 'This phone number is already in use by an active customer.',

            'role.required' => 'The role field is required.',
            'role.exists' => 'The selected role is invalid.',

            'biller_id.exists' => 'The selected biller is invalid.',
            'warehouse_id.exists' => 'The selected warehouse is invalid.',

            'customer_name.string' => 'The customer name must be a valid string.',
            'customer_name.max' => 'The customer name must not exceed 255 characters.',

            'customer_group_id.exists' => 'The selected customer group is invalid.',

            'tax_no.string' => 'The tax number must be a valid string.',
            'tax_no.max' => 'The tax number must not exceed 50 characters.',

            'address.string' => 'The address must be a valid string.',
            'address.max' => 'The address must not exceed 255 characters.',

            'city.string' => 'The city must be a valid string.',
            'city.max' => 'The city must not exceed 255 characters.',

            'state.string' => 'The state must be a valid string.',
            'state.max' => 'The state must not exceed 255 characters.',

            'postal_code.string' => 'The postal code must be a valid string.',
            'postal_code.max' => 'The postal code must not exceed 20 characters.',

            'country.string' => 'The country name must be a valid string.',
            'country.max' => 'The country name must not exceed 255 characters.',
        ];
    }


}
