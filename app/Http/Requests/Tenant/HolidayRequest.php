<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HolidayRequest extends FormRequest
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
            "id" => ['nullable'],
            'from_date' => ['required', 'date_format:d-m-Y'],
            'to_date' => ['required', 'date_format:d-m-Y', 'after_or_equal:from_date'],
            'note' => ['nullable', 'string', 'max:500']
        ];
    }





}
