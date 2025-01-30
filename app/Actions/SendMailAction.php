<?php

namespace App\Actions;

use App\Jobs\SendEmailJob;
use App\Services\Mail\MailConfig;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendMailAction
{
    public function execute(array $data, string $mailableClass): bool
    {
        $mailSetting = MailConfig::getSettings();

        if (!$mailSetting) {
            return false;
        }

        MailConfig::setMailInfo($mailSetting);

        try {
            dispatch(new SendEmailJob($data, $mailableClass));
            return true;
        } catch (\Exception $e) {
            Log::error('Error while sending email: ' . $e->getMessage());
            return false;
        }
    }
}
