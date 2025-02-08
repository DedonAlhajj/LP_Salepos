<?php

namespace App\Http\Requests\Tenant;

use App\Models\Customer;
use App\Models\CustomField;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
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
        $supplierId = $this->route('supplier'); // الحصول على معرف العميل في حالة التحديث


        $rules = [
            'both' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'customer_group_id' => 'nullable|exists:customer_groups,id',
            'name' => 'required|string|max:255',

            'company_name' => [
                'nullable', 'string', 'max:255',
                Rule::unique('suppliers')
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at')
                    ->ignore($supplierId)
            ],

            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('suppliers')
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at')
                    ->ignore($supplierId)
            ],

            'phone_number' => [
                'nullable', 'string', 'max:15',
                Rule::unique('customers')
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at')
            ],

            'vat_number' => 'nullable|string|max:50|regex:/^[A-Za-z0-9\-]+$/',


            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',

        ];


        return $rules;

    }




    public function messages()
    {
        return [
            'both.boolean' => 'The both field must be true or false.',
            'customer_group_id.exists' => 'The selected customer group is invalid.',

            'image.image'           => 'The uploaded file must be an image.',
            'image.mimes'           => 'Only JPEG, PNG, JPG, and GIF images are allowed.',
            'image.max'             => 'The image size must not exceed 2MB.',

            'name.string' => 'The customer name must be a valid string.',
            'name.max' => 'The customer name must not exceed 255 characters.',

            'company_name.string' => 'The company name must be a valid string.',
            'company_name.max' => 'The company name must not exceed 255 characters.',
            'company_name.unique' => 'The company name is already in use.',

            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email must not exceed 255 characters.',
            'email.unique' => 'The email is already registered.',

            'phone_number.string' => 'The phone number must be a valid string.',
            'phone_number.max' => 'The phone number must not exceed 15 characters.',
            'phone_number.unique' => 'This phone number is already in use.',

            'vat_number.string'     => 'The VAT number must be a valid string.',
            'vat_number.max'        => 'The VAT number may not be greater than 50 characters.',
            'vat_number.regex'      => 'The VAT number format is invalid.',

            'address.string' => 'The address must be a valid string.',
            'address.max' => 'The address must not exceed 255 characters.',

            'city.string' => 'The city name must be a valid string.',
            'city.max' => 'The city name must not exceed 255 characters.',

            'state.string' => 'The state name must be a valid string.',
            'state.max' => 'The state name must not exceed 255 characters.',

            'postal_code.string' => 'The postal code must be a valid string.',
            'postal_code.max' => 'The postal code must not exceed 20 characters.',

            'country.string' => 'The country name must be a valid string.',
            'country.max' => 'The country name must not exceed 255 characters.',

            'name.required' => 'The name field is required.',


        ];

    }

}
