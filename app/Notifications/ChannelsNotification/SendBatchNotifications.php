<?php

namespace App\Notifications\ChannelsNotification;

use App\Notifications\CustomNotification;
use Illuminate\Support\Str;

class SendBatchNotifications
{
    public function sendBatchNotifications($users, $data, $channels = ['mail', 'sms', 'push', 'in_app'])
    {
        // إنشاء batch_id واحد لجميع الإشعارات في هذه الدفعة
        $batchId = Str::uuid();

        foreach ($users as $user) {
            // إرسال الإشعار للمستخدم
            $user->notify(new CustomNotification($data, $channels, $batchId));
        }
    }
}

