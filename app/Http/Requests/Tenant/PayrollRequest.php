<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PayrollRequest extends FormRequest
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
            'employee_id' => ['required','integer', 'exists:employees,id'],
            "payroll_id" => ['nullable'],
            'created_at' => ['required', 'date_format:d-m-Y'], // ✅ تصحيح تنسيق التاريخ
            'account_id' => ['required','integer', 'exists:accounts,id'],
            'amount' => ['required', 'integer'], // ✅ تصحيح التنسيق
            'note' => ['nullable', 'string', 'max:1000'],
            "paying_method" => ['nullable', 'string', 'max:1000'],
        ];
    }




}
