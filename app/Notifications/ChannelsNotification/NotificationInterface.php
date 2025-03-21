<?php

namespace App\Notifications\ChannelsNotification;

interface NotificationInterface
{

    public function send(array $data): array;
}
