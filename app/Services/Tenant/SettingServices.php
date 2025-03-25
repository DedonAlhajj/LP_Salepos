<?php

namespace App\Services\Tenant;

use App\DTOs\GeneralSettingCentralDTO;
use App\DTOs\GeneralSettingDTO;
use App\DTOs\GeneralSettingStoreDTO;
use App\DTOs\MailSettingDTO;
use App\DTOs\PosSettingDTO;
use App\DTOs\RewardPointSettingDTO;
use App\DTOs\SmsDTO;
use App\DTOs\SmsSettingDTO;
use App\Models\Account;
use App\Models\Biller;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\ExternalService;
use App\Models\GeneralSetting;
use App\Models\HrmSetting;
use App\Models\MailSetting;
use App\Models\PosSetting;
use App\Models\RewardPointSetting;
use App\Models\SmsTemplate;
use App\Models\Warehouse;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use RuntimeException;

class SettingServices
{
    private SmsService $_smsService;
    protected MediaService $mediaService;

    public function __construct(SmsService $smsService, MediaService $mediaService)
    {
        $this->_smsService = $smsService;
        $this->mediaService = $mediaService;
    }


    /**
     * Fetches the general settings from the cache or retrieves them from the database.
     *
     * The settings are cached for 6 hours to improve performance by reducing database queries.
     *
     * @return GeneralSetting The most recent general settings from the database or cache.
     */
    public function getGeneralSetting(): GeneralSetting
    {
        // Caching the general settings for 6 hours
        return Cache::remember('general_settings', now()->addHours(6), function () {
            // Fetch the latest general settings from the database if not cached
            return GeneralSetting::latest()->first();
        });
    }

    /**
     * Updates the general settings in the system.
     *
     * This method updates or creates the general settings record in the database.
     * It accepts a DTO (Data Transfer Object) which contains the settings data.
     *
     * @param GeneralSettingCentralDTO $dto The DTO containing the data to update the general settings.
     * @return GeneralSetting The updated general settings object.
     * @throws Exception If the update process fails, an exception is thrown and logged.
     */
    public function updateGeneralSettingsCentral(GeneralSettingCentralDTO $dto): GeneralSetting
    {
        try {
            // Retrieve or create a general settings object (first record or a new one)
            $generalSetting = GeneralSetting::firstOrNew(['id' => 1]);

            // Fill the settings with data from the DTO
            $generalSetting->fill([
                'site_title' => $dto->site_title,
                'is_rtl' => $dto->is_rtl,
                'phone' => $dto->phone,
                'email' => $dto->email,
                'free_trial_limit' => $dto->free_trial_limit,
                'date_format' => $dto->date_format,
                'dedicated_ip' => $dto->dedicated_ip,
                'currency' => $dto->currency,
                'developed_by' => $dto->developed_by,
                'meta_title' => $dto->meta_title,
                'meta_description' => $dto->meta_description,
                'og_title' => $dto->og_title,
                'og_description' => $dto->og_description,
                'chat_script' => $dto->chat_script,
                'ga_script' => $dto->ga_script,
                'fb_pixel_script' => $dto->fb_pixel_script,
                'active_payment_gateway' => $dto->active_payment_gateway,
                'payment_credentials' => json_encode($dto->payment_credentials) // Encoding payment credentials as JSON
            ]);

            // Uncomment to handle image uploads (commented out for now)
            // if ($dto->og_image) {
            //     $this->mediaService->uploadDocumentWithClear($generalSetting, $dto->og_image, "Setting_og_image");
            // }
            // if ($dto->site_logo) {
            //     $this->mediaService->uploadDocumentWithClear($generalSetting, $dto->site_logo, "Setting_site_logo");
            // }

            // Save the settings after filling
            $generalSetting->save();

            // Clear the cache for general settings after update to ensure fresh data is retrieved
            Cache::forget('general_setting');

            // Return the updated general settings object
            return $generalSetting;
        } catch (Exception $e) {
            // Log the error if an exception occurs during the update process
            Log::error('Failed to update general Setting: ' . $e->getMessage());
            // Throw an exception to notify of the failure
            throw new Exception('Error update general Setting');
        }
    }

    /**
     * Retrieves the HRM (Human Resource Management) settings from the cache or database.
     *
     * The HRM settings are cached for 6 hours to reduce database calls and improve performance.
     *
     * @return HrmSetting The most recent HRM settings from the cache or the database.
     * @throws Exception If the fetching process fails, an exception is thrown and logged.
     */
    public function hrmSetting(): HrmSetting
    {
        try {
            // Caching the HRM settings for 6 hours
            return Cache::remember('HrmSetting', now()->addHours(6), function () {
                // Fetch the latest HRM settings if not cached
                return HrmSetting::latest()->first();
            });
        } catch (Exception $exception) {
            // Log the error if an exception occurs during fetching HRM settings
            Log::error('Failed to fetch POS settings');
            // Throw an exception to notify of the failure
            throw new Exception('Error fetching Hrm settings');
        }
    }

    /**
     * Updates the HRM settings in the system.
     *
     * This method updates the HRM settings in the database.
     * It accepts an array containing the data to be updated.
     *
     * @param array $data The HRM settings data to be updated.
     * @return void
     * @throws Exception If the update process fails, an exception is thrown and logged.
     */
    public function updateHrmSettings(array $data)
    {
        try {
            // Retrieve or create the HRM settings object
            $hrmSetting = HrmSetting::firstOrNew(['id' => 1]);

            // Fill the settings with data from the request
            $hrmSetting->fill([
                'checkin' => $data['checkin'],
                'checkout' => $data['checkout'],
            ]);

            // Save the HRM settings
            $hrmSetting->save();

            // Clear the cache for HRM settings after update to ensure fresh data is retrieved
            Cache::forget('HrmSetting');

        } catch (Exception $e) {
            // Log the error if an exception occurs during the update process
            Log::error('Failed to update Hrm settings: ' . $e->getMessage());
            // Throw an exception to notify of the failure
            throw new Exception('Error update Hrm settings');
        }
    }

    /**
     * Changes the theme of the application by updating the theme setting in the GeneralSetting table.
     *
     * @param string $theme The name of the theme to be applied.
     * @throws Exception If there is an error updating the theme setting.
     */
    public function changeTheme($theme)
    {
        try {
            // Fetch the general settings (it assumes that a general setting object exists).
            $theme = $this->getGeneralSetting();

            // Update the theme with the provided value.
            $theme->theme = $theme;

            // Save the updated theme to the database.
            $theme->save();
        } catch (Exception $e) {
            // Log any error that occurs during the process.
            Log::error('Failed to update change Theme settings: ' . $e->getMessage());

            // Throw a generic exception for handling by the calling function.
            throw new Exception('Error update change Theme settings');
        }
    }

    /**
     * Retrieves POS (Point of Sale) settings from the cache or database.
     *
     * Caches the settings for 10 minutes to improve performance and reduce repeated database queries.
     *
     * @return array An array containing the customers, warehouses, billers, POS settings, and payment options.
     * @throws Exception If there is an error fetching POS settings.
     */
    public function getPosSettings(): array
    {
        try {
            // Attempt to fetch POS settings from cache, or fetch from database and cache for 10 minutes.
            return Cache::remember('pos_settings', now()->addMinutes(10), function () {
                // Fetch customers, warehouses, billers, and the latest POS settings.
                $customers = Customer::select('id', 'name', 'phone_number')->get();
                $warehouses = Warehouse::select('id', 'name')->get();
                $billers = Biller::select('id', 'name', 'company_name')->get();
                $posSetting = PosSetting::latest()->first();

                // Return the fetched settings and options in an associative array.
                return [
                    'customers' => $customers,
                    'warehouses' => $warehouses,
                    'billers' => $billers,
                    'posSetting' => $posSetting,
                    'options' => $posSetting ? explode(',', $posSetting->payment_options) : [],
                ];
            });

        } catch (Exception $exception) {
            // Log any error that occurs during the process.
            Log::error('Error fetching POS settings: ' . $exception->getMessage());

            // Throw a generic exception for handling by the calling function.
            throw new Exception('Error fetching POS settings');
        }
    }

    /**
     * Updates POS settings with the provided data.
     *
     * @param PosSettingDTO $dto Data transfer object containing the new POS settings.
     * @return PosSetting The updated POS setting object.
     * @throws Exception If there is an error updating POS settings.
     */
    public function updatePosSettings(PosSettingDTO $dto): PosSetting
    {
        try {
            // Retrieve or create a new POS setting (first or new with id = 1).
            $posSetting = PosSetting::firstOrNew(['id' => 1]);

            // Fill the POS setting object with the provided data.
            $posSetting->fill([
                'customer_id' => $dto->customer_id,
                'warehouse_id' => $dto->warehouse_id,
                'biller_id' => $dto->biller_id,
                'product_number' => $dto->product_number,
                'stripe_public_key' => $dto->stripe_public_key,
                'stripe_secret_key' => $dto->stripe_secret_key,
                'paypal_live_api_username' => $dto->paypal_username,
                'paypal_live_api_password' => $dto->paypal_password,
                'paypal_live_api_secret' => $dto->paypal_signature,
                'invoice_option' => $dto->invoice_size,
                'thermal_invoice_size' => $dto->thermal_invoice_size,
                'keybord_active' => $dto->keyboard_active,
                'is_table' => $dto->is_table,
                'send_sms' => $dto->send_sms,
                'payment_options' => implode(',', $dto->payment_options)
            ]);

            // Save the updated POS setting to the database.
            $posSetting->save();

            // Forget the cached 'pos_settings' data to force a refresh.
            Cache::forget('pos_settings');

            // Return the updated POS setting object.
            return $posSetting;

        } catch (Exception $e) {
            // Log any error that occurs during the process.
            Log::error('Failed to update POS settings: ' . $e->getMessage());

            // Throw a generic exception for handling by the calling function.
            throw new Exception('Error fetching POS settings');
        }
    }

    /**
     * Retrieves general settings from the cache or database.
     *
     * Caches the general settings, accounts, currencies, and timezones for improved performance.
     *
     * @return GeneralSettingDTO A DTO object containing the general settings, accounts, currencies, and timezones.
     * @throws RuntimeException If there is an error fetching general settings.
     */
    public function getGeneralSettings(): GeneralSettingDTO
    {
        try {
            // Fetch the general settings from the cache or database.
            $generalSetting = Cache::remember('general_settings', now()->addHours(6), function () {
                return GeneralSetting::latest()->first();
            });

            // Cache accounts and currencies for 6 hours.
            $accounts = Cache::remember('accounts', now()->addHours(6), function () {
                return Account::all()->toArray();
            });

            $currencies = Cache::remember('currencies', now()->addHours(6), function () {
                return Currency::all()->toArray();
            });

            // Cache timezones for 1 day, and calculate the difference from GMT for each timezone.
            $timezones = Cache::remember('timezones', now()->addDays(1), function () {
                return collect(timezone_identifiers_list())->map(fn($zone) => [
                    'zone' => $zone,
                    'diff_from_GMT' => 'UTC/GMT ' . (new \DateTimeZone($zone))->getOffset(new \DateTime("now", new \DateTimeZone('UTC'))) / 3600
                ])->toArray();
            });

            // Return the general settings and related data wrapped in a DTO.
            return new GeneralSettingDTO($generalSetting, $accounts, $currencies, $timezones);

        } catch (Exception $e) {
            // Log any error that occurs during the process.
            Log::error("Error fetching general settings: " . $e->getMessage());

            // Throw an exception to signal an issue fetching the settings.
            throw new RuntimeException("An error occurred while loading the settings.");
        }
    }

    /**
     * Updates general settings with the provided data.
     *
     * Uses a database transaction to ensure the settings are updated atomically.
     *
     * @param GeneralSettingStoreDTO $data Data transfer object containing the new general settings.
     * @throws RuntimeException If there is an error updating the general settings.
     */
    public function updateGeneralSettings(GeneralSettingStoreDTO $data): void
    {
        try {
            // Use a database transaction to ensure atomicity.
            DB::transaction(function () use ($data) {
                // Retrieve or create the general settings record (first or new with id = 1).
                $generalSetting = GeneralSetting::firstOrNew(['id' => 1]);

                // Fill the general setting object with the provided data.
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

                // Update the timezone if it has changed.
                if ($data->timezone !== $generalSetting->timezone) {
                    $generalSetting->timezone = $data->timezone;
                    Cache::forget('app_timezone');
                    Cache::forever('app_timezone', $data->timezone);
                }

                // Save the updated general settings.
                $generalSetting->save();

                // Forget the cached general settings to force a refresh.
                Cache::forget('general_settings');
            });

        } catch (Exception $e) {
            // Log any error that occurs during the process.
            Log::error("updateGeneralSettings: " . $e->getMessage());

            // Throw a runtime exception to signal an error during the update.
            throw new RuntimeException("An error occurred while updating the settings.");
        }
    }

    /**
     * Retrieve the latest mail setting from cache or database.
     *
     * This method attempts to fetch the most recent MailSetting record from the cache.
     * If not found in the cache, it retrieves it from the database and stores it in the cache
     * for faster access on subsequent requests.
     *
     * @return MailSetting|null The latest mail setting object or null if not found.
     * @throws RuntimeException If an error occurs while fetching the mail setting.
     */
    public function mailSetting(): ?MailSetting
    {
        try {
            // Use caching to store the mail settings and avoid repeated database queries.
            return Cache::remember('MailSetting', now()->addHours(6), function () {
                // Fetch the latest MailSetting from the database.
                return MailSetting::latest()->first();
            });
        } catch (Exception $e) {
            // Log the error and throw a RuntimeException if something goes wrong.
            Log::error("Error fetching modifications (mailSetting) : " . $e->getMessage());
            throw new \RuntimeException("Error fetching modifications.");
        }
    }

    /**
     * Store or update the mail settings in the database.
     *
     * This method stores the provided mail settings into the database. If the settings do not exist,
     * it creates a new record. It also invalidates the cache to ensure future requests fetch the most
     * up-to-date information.
     *
     * @param MailSettingDTO $dto The data transfer object containing the mail settings.
     * @return void
     * @throws RuntimeException If an error occurs while saving the mail settings.
     */
    public function storeMailSettings(MailSettingDTO $dto): void
    {
        try {
            // Search for the latest MailSetting or create a new one if not found.
            $mailSetting = MailSetting::latest()->firstOrNew();

            // Fill the mail setting with data from the DTO.
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

            // Save the updated MailSetting.
            $mailSetting->save();

            // Invalidate the cache to ensure that future requests fetch the latest data.
            Cache::forget('MailSetting');
        } catch (Exception $e) {
            // Log the error and throw a RuntimeException if something goes wrong.
            Log::error('MailSettingService Error: ' . $e->getMessage());
            throw new \RuntimeException('Failed to update mail settings. Please try again later.');
        }
    }

    /**
     * Retrieve the latest reward point setting from cache or database.
     *
     * Similar to the mail setting method, this retrieves the latest RewardPointSetting record,
     * either from the cache or the database. The result is cached for subsequent accesses.
     *
     * @return RewardPointSetting|null The latest reward point setting object or null if not found.
     * @throws RuntimeException If an error occurs while fetching the reward point setting.
     */
    public function rewardPointSetting(): ?RewardPointSetting
    {
        try {
            // Use caching to store the reward point settings and avoid repeated database queries.
            return Cache::remember('RewardPointSetting', now()->addHours(6), function () {
                // Fetch the latest RewardPointSetting from the database.
                return RewardPointSetting::latest()->first();
            });
        } catch (Exception $e) {
            // Log the error and throw a RuntimeException if something goes wrong.
            Log::error("Error fetching modifications (RewardPointSetting) : " . $e->getMessage());
            throw new \RuntimeException("Error fetching modifications.");
        }
    }

    /**
     * Store or update the reward point settings in the database.
     *
     * This method allows you to update the reward point settings. If settings already exist, they are updated;
     * if not, a new record is created. It also invalidates the cache to ensure that the most recent settings are used.
     *
     * @param RewardPointSettingDTO $dto The data transfer object containing the reward point settings.
     * @return void
     * @throws RuntimeException If an error occurs while saving the reward point settings.
     */
    public function storeRewardPointSetting(RewardPointSettingDTO $dto): void
    {
        try {
            // Use updateOrCreate to either update the existing record or create a new one.
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

            // Invalidate the cache to ensure future requests fetch the latest data.
            Cache::forget('RewardPointSetting');
        } catch (Exception $e) {
            // Log the error and throw a RuntimeException if something goes wrong.
            Log::error('RewardPointSettingService Error: ' . $e->getMessage());
            throw new \RuntimeException('Failed to update reward point settings. Please try again later.');
        }
    }

    /**
     * Retrieves SMS settings for various gateways.
     *
     * This method queries the ExternalService model for specific SMS gateway services like Tonkra, Revesms,
     * Bdbulksms, Twilio, and Clickatell. It fetches the active settings for these services and formats them
     * into a structured array.
     *
     * @return array The array of SMS settings with gateway-specific details.
     * @throws RuntimeException If there is an error while fetching SMS settings.
     */
    public function getSmsSettings(): array
    {
        try {
            // Fetch the settings for the listed gateways
            $settings = ExternalService::whereIn('name', ['tonkra', 'revesms', 'bdbulksms', 'twilio', 'clickatell'])->get();
            $result = [];

            // Iterate over each service to extract and format the details
            foreach ($settings as $setting) {
                $dto = SmsSettingDTO::fromModel($setting);
                $result[$setting->name] = array_merge(
                    [
                        'sms_id' => $dto->sms_id,
                        'active' => $dto->active,
                    ],
                    // Merge additional gateway-specific details using the extractDetails method
                    $this->extractDetails($setting->name, $dto->details)
                );
            }

            return $result;
        } catch (Exception $e) {
            // Log the error if something goes wrong during the fetching process
            Log::error('SmsSettingService Error: ' . $e->getMessage());
            // Throw a runtime exception with a user-friendly message
            throw new RuntimeException('Failed to retrieve SMS settings. Please try again later.');
        }
    }

    /**
     * Extracts specific details for a given SMS gateway based on its name.
     *
     * This helper method returns an associative array of gateway-specific settings.
     * The details are structured according to the service's requirements.
     *
     * @param string $name The name of the SMS gateway.
     * @param array|null $details The raw details of the gateway settings.
     * @return array The formatted details for the specified gateway.
     */
    private function extractDetails(string $name, ?array $details): array
    {
        // Switch case to handle each gateway and extract necessary details
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
            default => [], // Return empty array if the gateway is unknown
        };
    }

    /**
     * Stores SMS settings to the database.
     *
     * This method processes the provided SMS settings, updates the corresponding external service record,
     * and handles gateway-specific details using a transaction. It also deactivates any previously active
     * SMS gateway before updating the current one.
     *
     * @param array $smsSetting The SMS settings to store.
     * @throws Exception If there is an error during the process.
     */
    public function getSmsSettingsStore(array $smsSetting)
    {
        try {
            // Wrap the process in a transaction to ensure atomicity
            DB::transaction(function () use ($smsSetting) {
                // Extract details based on the selected gateway
                $details = $this->getGatewayDetails($smsSetting['gateway'], $smsSetting);

                // Deactivate the currently active gateway (if any)
                if (isset($smsSetting['active'])) {
                    ExternalService::where('type', 'sms')
                        ->where('active', true)
                        ->update(['active' => false]);
                }

                // Update or create the new SMS gateway settings in the database
                ExternalService::updateOrCreate(
                    ['name' => $smsSetting['gateway']],
                    [
                        'name' => $smsSetting['gateway'],
                        'type' => 'sms',
                        'details' => json_encode($details), // Store details as JSON
                        'active' => $smsSetting['active'] ?? 0 // Set the active flag for this gateway
                    ]
                );
            });
        } catch (\Exception $e) {
            // Log the error if something goes wrong during the process
            Log::error('getSmsSettingsStore Error: ' . $e->getMessage());
            // Throw an exception with an error message
            throw new \Exception('Error updating SMS settings: ' . $e->getMessage());
        }
    }

    /**
     * Retrieves the specific details for the selected SMS gateway.
     *
     * This helper method determines the appropriate details based on the selected gateway and the provided data.
     *
     * @param string $gateway The name of the SMS gateway.
     * @param array $data The settings data for the gateway.
     * @return array The extracted details for the selected gateway.
     * @throws InvalidArgumentException If the gateway type is unknown.
     */
    private function getGatewayDetails(string $gateway, array $data): array
    {
        // Determine which gateway-specific details to retrieve
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
                // Throw an exception if the gateway type is unknown
                throw new InvalidArgumentException('Unknown gateway type');
        }
    }

    // The following methods are similar, extracting specific details for each gateway.

    #[ArrayShape(['apikey' => "mixed|null", 'secretkey' => "mixed|null", 'callerID' => "mixed|null"])]
    private function getRevesmsDetails(array $data): array
    {
        return [
            'apikey' => $data['apikey'] ?? null,
            'secretkey' => $data['secretkey'] ?? null,
            'callerID' => $data['callerID'] ?? null,
        ];
    }

    #[ArrayShape(['token' => "mixed|null"])]
    private function getBdbulksmsDetails(array $data): array
    {
        return [
            'token' => $data['token'] ?? null,
        ];
    }

    #[ArrayShape(['account_sid' => "mixed|null", 'auth_token' => "mixed|null", 'twilio_number' => "mixed|null"])]
    private function getTwilioDetails(array $data): array
    {
        return [
            'account_sid' => $data['account_sid'] ?? null,
            'auth_token' => $data['auth_token'] ?? null,
            'twilio_number' => $data['twilio_number'] ?? null,
        ];
    }

    #[ArrayShape(['api_token' => "mixed|null", 'sender_id' => "mixed|null"])]
    private function getTonkraDetails(array $data): array
    {
        return [
            'api_token' => $data['api_token'] ?? null,
            'sender_id' => $data['sender_id'] ?? null,
        ];
    }

    #[ArrayShape(['api_key' => "mixed|null"])]
    private function getClickatellDetails(array $data): array
    {
        return [
            'api_key' => $data['api_key'] ?? null,
        ];
    }

    /**
     * Retrieves the currently active SMS provider.
     *
     * This method queries the `ExternalService` table to find the active SMS provider.
     * If no active provider is found, an exception is thrown.
     *
     * @return array An associative array containing the provider name and its details.
     * @throws Exception If no active SMS provider is found.
     */
    #[ArrayShape(['name' => "mixed", 'details' => "mixed"])]
    private function getActiveSmsProvider(): array
    {
        // Fetch the active SMS provider from the database
        $provider = ExternalService::where('active', true)->where('type', 'sms')->first();

        // If no active provider is found, throw an exception
        if (!$provider) {
            throw new Exception('No active SMS provider found.');
        }

        // Return the provider's name and details
        return [
            'name' => $provider->name,
            'details' => $provider->details
        ];
    }

    /**
     * Sends an SMS message using the configured SMS service.
     *
     * This method initializes the SMS service with the provided message details
     * and attempts to send the SMS. If successful, it logs the message details.
     * In case of failure, it logs an error and returns false.
     *
     * @param SmsDTO $smsDTO The data transfer object containing SMS details.
     * @return bool Returns true if the SMS is sent successfully, otherwise false.
     */
    public function sendSms(SmsDTO $smsDTO): bool
    {
        try {
            // Initialize the SMS service with the given message data
            $this->_smsService->initialize($smsDTO->toArray());

            // Log success message
            Log::info('SMS Sent Successfully', [
                'provider' => $smsDTO->providerName,
                'recipients' => $smsDTO->recipients,
                'message' => $smsDTO->message,
            ]);

            return true;
        } catch (Exception $e) {
            // Log the error message in case of failure
            Log::error('SMS Sending Failed', [
                'error' => $e->getMessage(),
                'provider' => $smsDTO->providerName,
            ]);

            return false;
        }
    }

    /**
     * Retrieves the details of the currently active SMS provider.
     *
     * This method calls `getActiveSmsProvider()` to get the active provider details.
     *
     * @return array The active SMS provider's details.
     * @throws Exception
     */
    #[ArrayShape(['name' => "mixed", 'details' => "mixed"])]
    public function getProviderDetails(): array
    {
        return $this->getActiveSmsProvider();
    }

    /**
     * Prepares data for the SMS creation view.
     *
     * This method retrieves the list of customers and available SMS templates
     * to be used in the SMS sending interface.
     *
     * @return array An array containing customer list and SMS templates.
     */
    #[ArrayShape(['lims_customer_list' => "mixed", 'smsTemplates' => "mixed"])]
    public function createSms(): array
    {
        return [
            'lims_customer_list' => Customer::all(), // Retrieve all customers
            'smsTemplates' => SmsTemplate::all(),   // Retrieve all SMS templates
        ];
    }

}
