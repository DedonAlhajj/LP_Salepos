<?php

namespace App\Jobs;

use App\Notifications\ChannelsNotification\EmailNotification;
use App\Services\Tenant\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notifiable;
    protected $data;
    protected $notificationId;

    public function __construct($notifiable, array $data, $notificationId)
    {
        $this->notifiable = $notifiable;
        $this->data = $data;
        $this->notificationId = $notificationId; // تخزين الـ ID
    }

    public function handle(NotificationService $notificationService)
    {
        $result = (new EmailNotification())->send($this->data);
        //$notificationService->updateNotificationStatus($this->notifiable, 'mail', $result, $this->notificationId);
    }

}

