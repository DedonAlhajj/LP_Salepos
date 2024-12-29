<?php

namespace App\Http\Requests\Central;

use Illuminate\Foundation\Http\FormRequest;

class FeatureRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مخولًا لإجراء هذا الطلب.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * قواعد التحقق من المدخلات.
     */
    public function rules()
    {
        return [
            'description' => 'required|string|max:255',
        ];
    }
}
