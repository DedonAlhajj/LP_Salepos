<?php

namespace App\Http\Requests\Central;

use App\Rules\UniqueSubdomain;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:super_users,email',
            'password' => 'required|string|min:8|confirmed',
            'store_name' => 'required|string|max:255',
            'domain' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9-_]+$/', // التحقق فقط من النطاق الفرعي
                new UniqueSubdomain(), // يجب أن يكون فريدًا
            ],
            'package_id' => 'required|exists:packages,id',
            'OperationType' =>'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',

            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',

            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a string.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',

            'store_name.required' => 'The store name field is required.',
            'store_name.string' => 'The store name must be a string.',
            'store_name.max' => 'The store name must not exceed 255 characters.',

            'domain.required' => 'The domain field is required.',
            'domain.string' => 'The domain must be a string.',
            'domain.max' => 'The domain must not exceed 255 characters.',
            'domain.regex' => 'The domain may only contain letters, numbers, hyphens (-), and underscores (_).',

            'package_id.required' => 'A package must be selected.',
            'package_id.exists' => 'The selected package does not exist.',
        ];
    }
}
