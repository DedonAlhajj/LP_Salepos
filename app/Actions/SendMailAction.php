<?php

namespace App\Actions;

use App\Jobs\SendEmailJob;
use App\Mail\PayrollDetails;
use App\Services\Mail\MailConfig;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendMailAction
{
    public function execute(array $data, string $mailableClass,string $view = null): bool
    {
        $mailSetting = MailConfig::getSettings();

        if (!$mailSetting) {
            return false;
        }

        MailConfig::setMailInfo($mailSetting);

        try {
            dispatch(new SendEmailJob($data, $mailableClass,$view));
            return true;
        } catch (\Exception $e) {
            Log::error('Error while sending email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendMail(array $data, string $mailableClass,string $view = null): \Illuminate\Foundation\Application|array|string|\Illuminate\Contracts\Translation\Translator|\Illuminate\Contracts\Foundation\Application|null
    {
        if (!$this->execute($data, $mailableClass,$view)) {
            return __('Data created successfully. Please setup your mail settings to send mail.');
        } else {
            return __('Data created successfully.');
        }
    }


}
