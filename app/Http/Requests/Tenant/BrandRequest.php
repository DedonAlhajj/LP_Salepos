<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'brand_id' => ['nullable', 'exists:brands,id'],
        ];
    }




}
