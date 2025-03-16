<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountRequest extends FormRequest
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
            'account_no' => [
                'required',
                'string',
                'max:255',
                Rule::unique('accounts')
                    ->ignore($this->account_id)
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at')

            ],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'name' => ['required', 'string', 'max:255'],
            'initial_balance' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }




}
