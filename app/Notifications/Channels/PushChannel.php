<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class PushChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toPush')) {
            return $notification->toPush($notifiable);
        }

        Log::warning('Push notification skipped: no toPush() method found.');
    }
}
