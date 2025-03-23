<?php

namespace App\Services\Tenant;

use App\DTOs\SmsDTO;
use App\Models\Customer;
use App\Models\ExternalService;
use App\Models\SmsTemplate;
use Exception;
use Illuminate\Support\Facades\Log;

class SettingServices
{
    private SmsService $_smsService;


    public function __construct(SmsService $smsService)
    {
        $this->_smsService = $smsService;
        // تحميل بيانات مزود الرسائل عند إنشاء الكائن
    }

    private function getActiveSmsProvider(): array
    {
        $provider = ExternalService::where('active', true)->where('type', 'sms')->first();

        if (!$provider) {
            throw new Exception('No active SMS provider found.');
        }

        return [
            'name' => $provider->name,
            'details' => $provider->details
        ];
    }

    public function sendSms(SmsDTO $smsDTO): bool
    {
        try {
            // هنا يتم تنفيذ منطق الإرسال (API Call أو أي طريقة أخرى)
            $this->_smsService->initialize($smsDTO->toArray());

            Log::info('SMS Sent Successfully', [
                'provider' => $smsDTO->providerName,
                'recipients' => $smsDTO->recipients,
                'message' => $smsDTO->message,
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('SMS Sending Failed', [
                'error' => $e->getMessage(),
                'provider' => $smsDTO->providerName,
            ]);
            return false;
        }
    }

    public function getProviderDetails(): array
    {
        return $this->getActiveSmsProvider();
    }
    public function createSms()
    {
        return [
            'lims_customer_list' => Customer::all(),
            'smsTemplates' => SmsTemplate::all(),
        ];
    }
}
