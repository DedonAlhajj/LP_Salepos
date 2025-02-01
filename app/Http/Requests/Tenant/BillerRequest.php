<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BillerRequest extends FormRequest
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
        $userId = $this->route('biller');
        return [
            'name'         => 'required|string|max:255',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_name' => [
                'required', 'string', 'max:255',
                Rule::unique('billers')
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at')
                    ->ignore($userId),
            ],
            'vat_number'   => 'nullable|string|max:50|regex:/^[A-Za-z0-9\-]+$/',
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('billers')
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at')
                    ->ignore($userId),
            ],
            'phone_number' => 'required|string|max:20|regex:/^\+?[0-9\-]+$/',
            'address'      => 'required|string|max:500',
            'city'         => 'required|string|max:100',
            'state'        => 'nullable|string|max:100',
            'postal_code'  => 'nullable|string|max:20|regex:/^\d{4,10}$/',
            'country'      => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         => 'The name field is required.',
            'name.string'           => 'The name must be a valid string.',
            'name.max'              => 'The name may not be greater than 255 characters.',

            'image.image'           => 'The uploaded file must be an image.',
            'image.mimes'           => 'Only JPEG, PNG, JPG, and GIF images are allowed.',
            'image.max'             => 'The image size must not exceed 2MB.',

            'company_name.required' => 'The company name field is required.',
            'company_name.string'   => 'The company name must be a valid string.',
            'company_name.max'      => 'The company name may not be greater than 255 characters.',

            'vat_number.string'     => 'The VAT number must be a valid string.',
            'vat_number.max'        => 'The VAT number may not be greater than 50 characters.',
            'vat_number.regex'      => 'The VAT number format is invalid.',

            'email.required'        => 'The email field is required.',
            'email.email'           => 'The email must be a valid email address.',
            'email.max'             => 'The email may not be greater than 255 characters.',
            'email.unique'          => 'This email is already taken.',

            'phone_number.required' => 'The phone number field is required.',
            'phone_number.string'   => 'The phone number must be a valid string.',
            'phone_number.max'      => 'The phone number may not be greater than 20 characters.',
            'phone_number.regex'    => 'The phone number format is invalid.',

            'address.required'      => 'The address field is required.',
            'address.string'        => 'The address must be a valid string.',
            'address.max'           => 'The address may not be greater than 500 characters.',

            'city.required'         => 'The city field is required.',
            'city.string'           => 'The city must be a valid string.',
            'city.max'              => 'The city may not be greater than 100 characters.',

            'state.required'        => 'The state field is required.',
            'state.string'          => 'The state must be a valid string.',
            'state.max'             => 'The state may not be greater than 100 characters.',

            'postal_code.string'    => 'The postal code must be a valid string.',
            'postal_code.max'       => 'The postal code may not be greater than 20 characters.',
            'postal_code.regex'     => 'The postal code format is invalid.',

            'country.required'      => 'The country field is required.',
            'country.string'        => 'The country must be a valid string.',
            'country.max'           => 'The country may not be greater than 100 characters.',
        ];
    }


}
