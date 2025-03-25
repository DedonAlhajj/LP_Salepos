<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HrmRequest extends FormRequest
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
            'checkin'  => ['required', 'date_format:g:ia'],
            'checkout' => ['required', 'date_format:g:ia', 'after:checkin'],
        ];
    }




}
