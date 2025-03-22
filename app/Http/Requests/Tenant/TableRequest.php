<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class TableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    #[ArrayShape(["table_id" => "string[]", "name" => "string[]", "number_of_person" => "string[]", "description" => "string[]"])]
    public function rules(): array
    {

        return [
            "table_id" => ['nullable'],
            "name" => ['required', 'string', 'max:255'],
            "number_of_person" => ['nullable', 'integer', 'min:0'],
            "description" => ['nullable', 'string', 'max:1000'],

        ];
    }


}
