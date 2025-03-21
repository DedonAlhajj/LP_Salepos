<?php

namespace App\Services\Mail;

use App\Models\MailSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class MailConfigSetting
{
    protected static $settings;

    /**
     * استرجاع إعدادات البريد بناءً على المزود المختار
     */
    public static function getSettings(string $provider = 'default')
    {
        if (!self::$settings || self::$settings->provider !== $provider) {
            self::$settings = MailSetting::where('provider', $provider)->first();
        }
        return self::$settings;
    }

    /**
     * تهيئة إعدادات البريد بناءً على المزود المختار
     */
    public static function configureMail(string $provider = 'default')
    {
        $mailSetting = self::getSettings($provider);

        if ($mailSetting) {
            self::setMailInfo($mailSetting);
        } else {
            Log::warning("Mail settings not found for provider: $provider. Falling back to .env settings.");
        }
    }

    /**
     * تطبيق إعدادات البريد
     */
    public static function setMailInfo($mailSetting)
    {
        Config::set('mail.mailers.smtp.transport', $mailSetting->driver);
        Config::set('mail.mailers.smtp.host', $mailSetting->host);
        Config::set('mail.mailers.smtp.port', $mailSetting->port);
        Config::set('mail.mailers.smtp.username', $mailSetting->username);
        Config::set('mail.mailers.smtp.password', $mailSetting->password);
        Config::set('mail.mailers.smtp.encryption', $mailSetting->encryption);
        Config::set('mail.from.address', $mailSetting->from_address);
        Config::set('mail.from.name', $mailSetting->from_name);
    }

}
