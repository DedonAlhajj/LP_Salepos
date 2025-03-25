<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PosSettingRequest extends FormRequest
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
            'customer_id' => 'required|integer|exists:customers,id',
            'warehouse_id' => 'required|integer|exists:warehouses,id',
            'biller_id' => 'required|integer|exists:billers,id',
            'product_number' => 'required|integer|min:1',
            'stripe_public_key' => 'nullable|string|max:255',
            'stripe_secret_key' => 'nullable|string|max:255',
            'paypal_username' => 'nullable|string|max:255',
            'paypal_password' => 'nullable|string|max:255',
            'paypal_signature' => 'nullable|string|max:255',
            'invoice_size' => 'required|string|max:50',
            'thermal_invoice_size' => 'required|string|max:50',
            'options' => 'nullable|array',
            'options.*' => 'string|max:50',
            'keyboard_active' => 'sometimes|boolean',
            'is_table' => 'sometimes|boolean',
            'send_sms' => 'sometimes|boolean',
        ];
    }




}
