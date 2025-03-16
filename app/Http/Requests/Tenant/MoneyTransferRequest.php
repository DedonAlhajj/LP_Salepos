<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MoneyTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // إذا كنت تحتاج للتحقق من صلاحية المستخدم لتنفيذ هذه العملية، يمكن تنفيذها هنا
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'from_account_id' => [
                'required',
                'exists:accounts,id',
                Rule::notIn([$this->to_account_id]), // التحقق من عدم تطابق الحسابين
            ],
            'to_account_id' => [
                'required',
                'exists:accounts,id',
            ],
            'id' => 'nullable|integer',
            'amount' => 'required|numeric|min:0.01',
        ];
    }


    public function messages()
    {
        return [
            'from_account_id.not_in' => 'The from_account_id cannot be the same as to_account_id.',
            'from_account_id.required' => 'The from_account_id is required.',
            'to_account_id.required' => 'The to_account_id is required.',
            'amount.required' => 'The amount is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.01.',
        ];
    }



}
