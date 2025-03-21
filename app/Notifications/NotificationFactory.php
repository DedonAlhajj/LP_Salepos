<?php

namespace App\Notifications;

use Illuminate\Support\Facades\Notification;

class NotificationFactory
{
    public static function send($notifiable, array $data, array $channels)
    {
        Notification::send($notifiable, new CustomNotification($data, $channels));
    }
}

