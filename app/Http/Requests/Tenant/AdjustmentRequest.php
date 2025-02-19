<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdjustmentRequest extends FormRequest
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
       // $userId = $this->route('biller');
        return [
            'product_id'   => 'required|array',
            'product_code' => 'required|array',
            'qty'          => 'required|array',
            'unit_cost'    => 'required|array',
            'action'       => 'required|array',
            'warehouse_id' => 'required|integer|exists:warehouses,id',
            'document'     => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048',
            'product_code_name' => 'nullable|string',
            'total_qty' => 'nullable|integer',
            'item' => 'nullable|integer',
            'note' => 'nullable|string' ,
        ];
    }




}
