<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseCategoryRequest extends FormRequest
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
        $expenseCategoriesId = $this->route('expense_categories');
        return [
            'code' => [
                'max:255',
                Rule::unique('expense_categories')
                    ->where('tenant_id', tenant('id'))
                    ->whereNull('deleted_at')
                    ->ignore($expenseCategoriesId),
            ],
            'name' => 'required|string|max:255',
        ];
    }




}
