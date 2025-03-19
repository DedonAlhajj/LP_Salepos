<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDiscountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'discount_plan_id' => 'required|array|min:1',
            'discount_plan_id.*' => 'exists:discount_plans,id',
            'applicable_for' => 'required|in:All,Specific',
            'product_code' => 'nullable|string|max:255',
            'product_list' => 'nullable|array',
            'product_list.*' => 'exists:products,id',
            'valid_from' => 'required|date_format:d-m-Y',
            'valid_till' => 'required|date_format:d-m-Y|after_or_equal:valid_from',
            'type' => 'required|in:flat,percentage',
            'value' => 'required|numeric|min:0',
            'minimum_qty' => 'required|integer|min:1',
            'maximum_qty' => 'required|integer|min:1',
            'days' => 'required|array|min:1',
            'days.*' => 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun'
        ];
    }




}
