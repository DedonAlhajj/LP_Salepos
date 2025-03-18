<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
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
            'employee_id' => ['required', 'array', 'min:1'],
            'employee_id.*' => ['integer', 'exists:employees,id'],
            'date' => ['required', 'date_format:d-m-Y'], // ✅ تصحيح تنسيق التاريخ
            'checkin' => ['required'], // ✅ تصحيح تنسيق الوقت
            'checkout' => ['nullable', 'after:checkin'], // ✅ تصحيح التنسيق
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }




}
