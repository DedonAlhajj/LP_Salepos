<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SmsSettingRequest extends FormRequest
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
            "type" => 'nullable|string',
            "sms_id" =>'nullable|integer',
            "gateway_hidden" => 'nullable|string',
            'gateway' => 'required|string',
            'token' => 'nullable|string',
            'apikey' => 'nullable|string',
            'secretkey' => 'nullable|string',
            'callerID' => 'nullable|string',
            'api_token' => 'nullable|string',
            'sender_id' => 'nullable|string',
            'account_sid' => 'nullable|string',
            'active' => 'nullable|boolean',
            'details' => 'nullable|array',
            'auth_token' => 'nullable|string',
            'twilio_number' => 'nullable|string',
            'api_key' => 'nullable|string',
        ];
    }




}
