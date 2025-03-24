<?php

namespace App\Services\Tenant;

use App\DTOs\GeneralSettingDTO;
use App\DTOs\GeneralSettingStoreDTO;
use App\DTOs\MailSettingDTO;
use App\DTOs\RewardPointSettingDTO;
use App\DTOs\SmsDTO;
use App\DTOs\SmsSettingDTO;
use App\Models\Account;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\ExternalService;
use App\Models\GeneralSetting;
use App\Models\MailSetting;
use App\Models\RewardPointSetting;
use App\Models\SmsTemplate;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingServices
{
    private SmsService $_smsService;
    protected MediaService $mediaService;

    public function __construct(SmsService $smsService, MediaService $mediaService)
    {
        $this->_smsService = $smsService;
        $this->mediaService = $mediaService;
        // تحميل بيانات مزود الرسائل عند إنشاء الكائن
    }

    public function getGeneralSettings(): GeneralSettingDTO
    {
        try {
            // استخدام الكاش لتخزين البيانات وتجنب الاستعلامات المتكررة
            $generalSetting = Cache::remember('general_settings', now()->addHours(6), function () {
                return GeneralSetting::latest()->first();
            });

            $accounts = Cache::remember('accounts', now()->addHours(6), function () {
                return Account::all()->toArray();
            });

            $currencies = Cache::remember('currencies', now()->addHours(6), function () {
                return Currency::all();
            });

            // تحسين حساب المناطق الزمنية
            $timezones = Cache::remember('timezones', now()->addDays(1), function () {
                return collect(timezone_identifiers_list())->map(fn($zone) => [
                    'zone' => $zone,
                    'diff_from_GMT' => 'UTC/GMT ' . (new \DateTimeZone($zone))->getOffset(new \DateTime("now", new \DateTimeZone('UTC'))) / 3600
                ])->toArray();
            });

            return new GeneralSettingDTO($generalSetting, $accounts, $currencies, $timezones);
        } catch (Exception $e) {
            Log::error("خطأ أثناء جلب الإعدادات العامة: " . $e->getMessage());
            throw new \RuntimeException("حدث خطأ أثناء تحميل الإعدادات.");
        }
    }

    public function updateGeneralSettings(GeneralSettingStoreDTO $data): void
    {
        try {
            DB::transaction(function () use ($data) {
                // جلب أو إنشاء سجل الإعدادات العامة
                $generalSetting = GeneralSetting::firstOrNew(['id' => 1]);


                $generalSetting->fill([
                    'site_title' => $data->site_title,
                    'is_rtl' => $data->is_rtl,
                    'is_zatca' => $data->is_zatca,
                    'company_name' => $data->company_name,
                    'vat_registration_number' => $data->vat_registration_number,
                    'currency' => $data->currency,
                    'currency_position' => $data->currency_position,
                    'decimal' => $data->decimal,
                    'staff_access' => $data->staff_access,
                    'without_stock' => $data->without_stock,
                    'is_packing_slip' => $data->is_packing_slip,
                    'date_format' => $data->date_format,
                    'developed_by' => $data->developed_by,
                    'invoice_format' => $data->invoice_format,
                    'state' => $data->state,
                    'expiry_type' => $data->expiry_type,
                    'expiry_value' => $data->expiry_value,
                ]);

                // حفظ الشعار إذا تم رفعه
//        if ($data->site_logo_path) {
//            $this->mediaService->uploadDocumentWithClear($generalSetting, $data->site_logo_path, "Setting_logo");
//        }
                // تحديث المنطقة الزمنية فقط عند تغييرها
                if ($data->timezone !== $generalSetting->timezone) {
                    $generalSetting->timezone = $data->timezone;
                    Cache::forget('app_timezone');
                    Cache::forever('app_timezone', $data->timezone);
                }
                $generalSetting->save();

                Cache::forget('general_settings');
            });

        } catch (Exception $e) {
            Log::error("updateGeneralSettings: " . $e->getMessage());
            throw new \RuntimeException("حدث خطأ أثناء تحديث الإعدادات.");
        }
    }

    public function mailSetting()
    {
        try {
            // استخدام الكاش لتخزين البيانات وتجنب الاستعلامات المتكررة
            return Cache::remember('MailSetting', now()->addHours(6), function () {
                return MailSetting::latest()->first();
            });
        } catch (Exception $e) {
            Log::error("Error fetching modifications (mailSetting) : " . $e->getMessage());
            throw new \RuntimeException("Error fetching modifications.");
        }
    }

    public function storeMailSettings(MailSettingDTO $dto): void
    {
        try {
            // البحث عن أحدث إعدادات أو إنشاء جديد عند عدم وجودها
            $mailSetting = MailSetting::latest()->firstOrNew();

            $mailSetting->fill([
                'driver' => $dto->driver,
                'host' => $dto->host,
                'port' => $dto->port,
                'from_address' => $dto->from_address,
                'from_name' => $dto->from_name,
                'username' => $dto->username,
                'password' => $dto->password,
                'encryption' => $dto->encryption,
            ]);

            $mailSetting->save();
            Cache::forget('MailSetting');
        } catch (Exception $e) {
            Log::error('MailSettingService Error: ' . $e->getMessage());
            throw new \RuntimeException('فشل تحديث إعدادات البريد الإلكتروني. يرجى المحاولة لاحقًا.');
        }
    }

    public function rewardPointSetting()
    {
        try {
            // استخدام الكاش لتخزين البيانات وتجنب الاستعلامات المتكررة
            return Cache::remember('RewardPointSetting', now()->addHours(6), function () {
                return RewardPointSetting::latest()->first();
            });
        } catch (Exception $e) {
            Log::error("Error fetching modifications (RewardPointSetting) : " . $e->getMessage());
            throw new \RuntimeException("Error fetching modifications.");
        }
    }

    public function storeRewardPointSetting(RewardPointSettingDTO $dto): void
    {
        try {
            RewardPointSetting::updateOrCreate(
                [],
                [
                    'is_active' => $dto->is_active,
                    'per_point_amount' => $dto->per_point_amount,
                    'minimum_amount' => $dto->minimum_amount,
                    'duration' => $dto->duration,
                    'type' => $dto->type,
                ]
            );
            Cache::forget('RewardPointSetting');
        } catch (Exception $e) {
            Log::error('RewardPointSettingService Error: ' . $e->getMessage());
            throw new \RuntimeException('فشل تحديث إعدادات نقاط المكافأة. يرجى المحاولة لاحقًا.');
        }
    }


    /** getSmsSettings*/
    public function getSmsSettings(): array
    {
        try {
            $settings = ExternalService::whereIn('name', ['tonkra', 'revesms', 'bdbulksms', 'twilio', 'clickatell'])->get();
            $result = [];


            foreach ($settings as $setting) {
                $dto = SmsSettingDTO::fromModel($setting);
                $result[$setting->name] = array_merge(
                    [
                        'sms_id' => $dto->sms_id,
                        'active' => $dto->active,
                    ],
                    $this->extractDetails($setting->name, $dto->details)
                );
            }

            return $result;
        } catch (Exception $e) {
            Log::error('SmsSettingService Error: ' . $e->getMessage());
            throw new \RuntimeException('فشل استرجاع إعدادات SMS. يرجى المحاولة لاحقًا.');
        }
    }

    private function extractDetails(string $name, ?array $details): array
    {
        return match ($name) {
            'tonkra' => [
                'api_token' => $details['api_token'] ?? '',
                'recipent' => $details['recipent'] ?? '',
                'sender_id' => $details['sender_id'] ?? '',
            ],
            'revesms' => [
                'apikey' => $details['apikey'] ?? '',
                'secretkey' => $details['secretkey'] ?? '',
                'callerID' => $details['callerID'] ?? '',
            ],
            'bdbulksms' => [
                'token' => $details['token'] ?? '',
            ],
            'twilio' => [
                'account_sid' => $details['account_sid'] ?? '',
                'auth_token' => $details['auth_token'] ?? '',
                'twilio_number' => $details['twilio_number'] ?? '',
            ],
            'clickatell' => [
                'api_key' => $details['api_key'] ?? '',
            ],
            default => [],
        };
    }


    public function getSmsSettingsStore(array $smsSetting)
    {
        try {
            DB::transaction(function () use ($smsSetting) {
                // إدارة البوابات المختلفة
                $details = $this->getGatewayDetails($smsSetting['gateway'], $smsSetting);

                // التعامل مع البوابة النشطة
                if ($smsSetting['active']) {
                    ExternalService::where('type', 'sms')
                        ->where('active', true)
                        ->update(['active' => false]);
                }

                // تحديث أو إنشاء بيانات البوابة
                ExternalService::updateOrCreate(
                    ['name' => $smsSetting['gateway']],
                    [
                        'name' => $smsSetting['gateway'],
                        'type' => 'sms',
                        'details' => json_encode($details),
                        'active' => $smsSetting['active']
                    ]
                );
            });

        } catch (\Exception $e) {
            // رفع الأخطاء المناسبة
            Log::error('getSmsSettingsStore Error: ' . $e->getMessage());
            throw new \Exception('Error updating SMS settings: ' . $e->getMessage());
        }
    }

    private function getGatewayDetails(string $gateway, array $data)
    {
        // تحديد التفاصيل بناءً على البوابة المختارة
        switch ($gateway) {
            case 'revesms':
                return $this->getRevesmsDetails($data);
            case 'bdbulksms':
                return $this->getBdbulksmsDetails($data);
            case 'twilio':
                return $this->getTwilioDetails($data);
            case 'tonkra':
                return $this->getTonkraDetails($data);
            case 'clickatell':
                return $this->getClickatellDetails($data);
            default:
                throw new \InvalidArgumentException('Unknown gateway type');
        }
    }

    private function getRevesmsDetails(array $data)
    {
        // فقط استخدام البيانات المتاحة
        return [
            'apikey' => $data['apikey'] ?? null,
            'secretkey' => $data['secretkey'] ?? null,
            'callerID' => $data['callerID'] ?? null,
        ];
    }

    private function getBdbulksmsDetails(array $data)
    {
        return [
            'token' => $data['token'] ?? null,
        ];
    }

    private function getTwilioDetails(array $data)
    {
        return [
            'account_sid' => $data['account_sid'] ?? null,
            'auth_token' => $data['auth_token'] ?? null,
            'twilio_number' => $data['twilio_number'] ?? null,
        ];
    }

    private function getTonkraDetails(array $data)
    {
        return [
            'api_token' => $data['api_token'] ?? null,
            'sender_id' => $data['sender_id'] ?? null,
        ];
    }

    private function getClickatellDetails(array $data)
    {
        return [
            'api_key' => $data['api_key'] ?? null,
        ];
    }

    /** getSmsSettings*/
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
