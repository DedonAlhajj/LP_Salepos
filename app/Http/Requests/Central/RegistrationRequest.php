<?php

namespace App\Http\Requests\Central;

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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'store_name' => 'required|string|max:255',
            'domain' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9-_]+$/', // التحقق فقط من النطاق الفرعي
                'unique:domains,domain', // يجب أن يكون فريدًا
            ],
            'package_id' => 'required|exists:packages,id',
        ];
    }

    /**
     * رسائل الأخطاء المخصصة.
     */
    public function messages()
    {
        return [
            'domain.unique' => 'The domain is already taken. Please choose another one.',
            'name.required' => 'The name required and must be string and not more then 255',
            'email.required' => 'The email required',
            'email.unique' => 'The email is already taken. Please choose another one.',
            'domain.regex' => 'The subdomain format is invalid.',

        ];
    }
}
