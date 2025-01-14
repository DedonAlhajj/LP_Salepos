<?php

namespace App\Http\Requests\Central;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
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
            'package_name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'duration' => 'required|integer',
            'duration_unit'=> 'required',
            'description' => 'nullable|string',
            'max_users' => 'integer',
            'max_storage' => 'string',
            'is_active' => 'required|boolean',
            'is_trial' => 'boolean',
            'features' => 'nullable|array',
            'features.*' => 'exists:features,id', // التحقق من أن المعرفات موجودة في جدول الميزات
        ];
    }
}
