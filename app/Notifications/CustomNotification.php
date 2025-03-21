<?php

namespace App\Notifications;

use App\Jobs\SendEmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CustomNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $data;
    protected array $channels;
    protected string $batchId;
    protected int $sender_id;

    public function __construct(array $data, array $channels = ['mail', 'sms', 'push', 'inApp'], $sender_id = 0, $batchId = null)
    {
        $this->data = $data;
        $this->channels = $channels;
        $this->batchId = $batchId ?? Str::uuid(); // استخدام batch_id المرسل أو إنشاء واحد جديد
        $this->sender_id = $sender_id;
    }

    public function via($notifiable)
    {
        return array_unique(array_merge($this->channels, ['database']));
    }


    public function toDatabase($notifiable)
    {
        return [
            'sender_id' => $this->sender_id,
            'data' => $this->data, // تأكد من تخزين البيانات هنا
            'batch_id' => $this->batchId,
            'channels' => $this->channels, // تخزين القنوات المستخدمة
            'status' => 'queued',
        ];
    }


    public function toMail($notifiable)
    {
        $notification = $notifiable->notifications()->latest()->first();

        if (!$notification) {
            Log::error("No notification record found for user: {$notifiable->id}");
            return;
        }

        dispatch(new SendEmailNotification($notifiable, $this->data, $notification->id));
    }

    public function toSms($notifiable)
    {
//        $result = (new SmsNotification())->send($this->data);
//        $this->updateNotificationStatus($notifiable, 'sms', $result);

    }

    public function toPush($notifiable)
    {
//        $result = (new PushNotification())->send($this->data);
//        $this->updateNotificationStatus($notifiable, 'push', $result);
    }

    public function toInApp($notifiable)
    {
//        $result = (new InAppNotification())->send($this->data);
//        $this->updateNotificationStatus($notifiable, 'in_app', $result);

    }





}
