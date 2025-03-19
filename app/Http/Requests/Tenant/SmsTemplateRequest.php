<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SmsTemplateRequest extends FormRequest
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
            'smstemplate_id' => 'sometimes',
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'is_default' => 'sometimes|boolean',
            'is_default_ecommerce' => 'sometimes|boolean',
        ];

    }




}
