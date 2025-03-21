<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class InAppChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toInApp')) {
            return $notification->toInApp($notifiable);
        }

        Log::warning('In-App notification skipped: no toInApp() method found.');
    }
}
