<?php

namespace App\Services\Tenant;

use App\Mail\TransferDetails;
use App\Models\Account;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailService
{

    public function sendTransferMail(array $mailData): bool
    {
        try {
            if (!empty($mailData['to_email'])) {
                Mail::to($mailData['to_email'])->send(new TransferDetails($mailData));
            }

            if (!empty($mailData['from_email'])) {
                Mail::to($mailData['from_email'])->send(new TransferDetails($mailData));
            }

            return 1; // نجاح الإرسال
        } catch (\Exception $e) {
            Log::error('Failed to send transfer email: ' . $e->getMessage());
            return 0; // فشل الإرسال
        }
    }


}

