<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuotationRequest extends FormRequest
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
            'biller_id'       => 'required|integer|exists:billers,id',
            'supplier_id'     => 'required|integer|exists:suppliers,id',
            'customer_id'     => 'required|integer|exists:customers,id',
            'warehouse_id'    => 'required|integer|exists:warehouses,id',
            'product_id'      => 'required|array|min:1',
            'product_id.*'    => 'required|integer|exists:products,id',
            'qty'             => 'required|array|min:1',
            'qty.*'           => 'required|numeric|min:1',
            'sale_unit'       => 'nullable|array',
            'sale_unit.*'     => 'nullable|string|exists:units,unit_name',
            'net_unit_price'  => 'required|array',
            'net_unit_price.*'=> 'required|numeric|min:0',
            'discount'        => 'nullable|array',
            'discount.*'      => 'nullable|numeric|min:0',
            'tax_rate'        => 'nullable|array',
            'tax_rate.*'      => 'nullable|numeric|min:0',
            'tax'             => 'nullable|array',
            'tax.*'           => 'nullable|numeric|min:0',
            'subtotal'        => 'required|array',
            'subtotal.*'      => 'required|numeric|min:0',
            'total_qty'       => 'required|numeric|min:1',
            'total_discount'  => 'required|numeric|min:0',
            'total_tax'       => 'required|numeric|min:0',
            'total_price'     => 'required|numeric|min:0',
            'item'            => 'required|integer|min:1',
            'order_tax'       => 'required|numeric|min:0',
            'grand_total'     => 'required|numeric|min:0',
            'order_tax_rate'  => 'required|numeric|min:0',
            'order_discount'  => 'nullable|numeric|min:0',
            'shipping_cost'   => 'nullable|numeric|min:0',
            'quotation_status'=> 'required|integer|in:1,2,3', // حسب الحالات المتاحة
            'note'            => 'nullable|string|max:1000',

            // **التحقق من الملف (document)**
            'document'        => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt|max:2048', // الحجم 2MB

        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         => 'The name field is required.',
            'name.string'           => 'The name must be a valid string.',
            'name.max'              => 'The name may not be greater than 255 characters.',

            'image.image'           => 'The uploaded file must be an image.',
            'image.mimes'           => 'Only JPEG, PNG, JPG, and GIF images are allowed.',
            'image.max'             => 'The image size must not exceed 2MB.',

            'company_name.required' => 'The company name field is required.',
            'company_name.string'   => 'The company name must be a valid string.',
            'company_name.max'      => 'The company name may not be greater than 255 characters.',

            'vat_number.string'     => 'The VAT number must be a valid string.',
            'vat_number.max'        => 'The VAT number may not be greater than 50 characters.',
            'vat_number.regex'      => 'The VAT number format is invalid.',

            'email.required'        => 'The email field is required.',
            'email.email'           => 'The email must be a valid email address.',
            'email.max'             => 'The email may not be greater than 255 characters.',
            'email.unique'          => 'This email is already taken.',

            'phone_number.required' => 'The phone number field is required.',
            'phone_number.string'   => 'The phone number must be a valid string.',
            'phone_number.max'      => 'The phone number may not be greater than 20 characters.',
            'phone_number.regex'    => 'The phone number format is invalid.',

            'address.required'      => 'The address field is required.',
            'address.string'        => 'The address must be a valid string.',
            'address.max'           => 'The address may not be greater than 500 characters.',

            'city.required'         => 'The city field is required.',
            'city.string'           => 'The city must be a valid string.',
            'city.max'              => 'The city may not be greater than 100 characters.',

            'state.required'        => 'The state field is required.',
            'state.string'          => 'The state must be a valid string.',
            'state.max'             => 'The state may not be greater than 100 characters.',

            'postal_code.string'    => 'The postal code must be a valid string.',
            'postal_code.max'       => 'The postal code may not be greater than 20 characters.',
            'postal_code.regex'     => 'The postal code format is invalid.',

            'country.required'      => 'The country field is required.',
            'country.string'        => 'The country must be a valid string.',
            'country.max'           => 'The country may not be greater than 100 characters.',
        ];
    }


}
