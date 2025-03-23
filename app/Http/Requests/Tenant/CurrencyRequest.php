<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CurrencyRequest extends FormRequest
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
            'currency_id' => ['nullable', 'exists:currencies,id'],
            'name' => ['required', 'string', 'max:255'],
            'exchange_rate' => ['required', 'numeric', 'min:0'],
            'code' => ['required', 'string', 'max:1000'],
        ];
    }




}
