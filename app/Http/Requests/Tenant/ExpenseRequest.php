<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
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
            'warehouse_id'  => 'required|exists:warehouses,id',
            'amount'        => 'required|numeric|min:0',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'note'          => 'nullable|string|max:500',
            'account_id'    => 'required|exists:accounts,id',
            'created_at'    => 'nullable|date',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'created_at' => $this->created_at ? now()->parse($this->created_at) : now(),
        ]);
    }



}
