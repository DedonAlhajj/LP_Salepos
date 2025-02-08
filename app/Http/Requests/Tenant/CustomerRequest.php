<?php

namespace App\Http\Requests\Tenant;

use App\Models\Customer;
use App\Models\CustomField;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
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
        $customerId = $this->route('customer'); // الحصول على معرف العميل في حالة التحديث

        $userId = $customerId ? $customerId->user_id : null; // تجنب الأخطاء إذا لم يكن هناك `customerId`

        $rules = [
            'both' => 'nullable|boolean',
            'customer_group_id' => 'nullable|exists:customer_groups,id',
            'customer_name' => 'nullable|string|max:255',

            'company_name' => $customerId ? 'nullable|string|max:255' : [
                'nullable', 'string', 'max:255',
                Rule::unique('suppliers')
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at')
            ],

            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users')
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at')
                    ->ignore($userId)
            ],

            'phone_number' => [
                'nullable', 'string', 'max:15',
                Rule::unique('customers')
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at')
                    ->ignore($customerId)
            ],

            'tax_no' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',

            'password' => 'nullable|string|min:8',

            'pos' => 'nullable|boolean',
            'user' => 'nullable|boolean'
        ];

           // **إضافة قواعد التحقق للحقول المخصصة**
        $customFields = CustomField::where('entity_type', 'customer')->get(); // جلب الحقول المخصصة الخاصة بالـ Customer
        foreach ($customFields as $field) {
            $fieldName = str_replace(' ', '_', strtolower($field->name)); // توليد اسم الحقل الديناميكي
            $fieldRules = [];

            // إذا كان الحقل مطلوبًا
            if ($field->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // التحقق من نوع الحقل
            switch ($field->type) {
                case 'text':
                case 'textarea':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:255';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'checkbox':
                case 'radio_button':
                case 'select':
                case 'multi_select':
                    $fieldRules[] = 'in:' . $field->option_value; // السماح فقط بالقيم المحددة مسبقًا
                    break;
                case 'date_picker':
                    $fieldRules[] = 'date';
                    break;
            }

            // إضافة القاعدة للدوال
            $rules[$fieldName] = $fieldRules;
        }
        return $rules;


    }




    public function messages()
    {
        return [
            'both.boolean' => 'The both field must be true or false.',
            'customer_group_id.exists' => 'The selected customer group is invalid.',
            'customer_name.string' => 'The customer name must be a valid string.',
            'customer_name.max' => 'The customer name must not exceed 255 characters.',

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

            'tax_no.string' => 'The tax number must be a valid string.',
            'tax_no.max' => 'The tax number must not exceed 50 characters.',

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
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name must not exceed 255 characters.',

            'password.required' => 'The password is required when creating a new user.',
            'password.string' => 'The password must be a valid string.',
            'password.min' => 'The password must be at least 8 characters long.',
            'password.confirmed' => 'The password confirmation does not match.',

            'pos.required' => 'The POS field is required.',
            'pos.boolean' => 'The POS field must be true or false.',

            'user.boolean' => 'The user field must be true or false.'
        ];

    }

}
