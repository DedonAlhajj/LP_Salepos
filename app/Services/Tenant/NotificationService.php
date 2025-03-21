<?php

namespace App\Services\Tenant;



use App\Models\User;
use App\Notifications\CustomNotification;
use Illuminate\Support\Facades\Log;

class NotificationService
{

    public function getNotifications(): \Illuminate\Database\Eloquent\Collection|array
    {
        return \App\Models\CustomNotification::with('user','userFrom')->where('notifiable_type', 'App\Models\User')->get();
    }

    public function resendFailedNotifications($failedNotifications)
    {

        foreach ($failedNotifications as $notification) {
            try {
                $notifiable = $notification->notifiable; // استرجاع الكيان المرتبط بالإشعار

                if (!$notifiable) {
                    continue; // تخطي إذا لم يكن هناك مستخدم أو كيان متصل
                }

                $channels = json_decode($notification->channels, true);
                $data = json_decode($notification->data, true);

                // إعادة إرسال الإشعار
                $notifiable->notify(new CustomNotification($data, $channels, $notification->batch_id));

                // تحديث حالته
                $notification->update([
                    'status' => 'queued',
                    'error_message' => null, // إعادة المحاولة، بالتالي إزالة الخطأ القديم
                ]);
            } catch (\Exception $e) {
                Log::error("إعادة إرسال الإشعار فشلت: " . $e->getMessage());
            }
        }
    }

    public function updateNotificationStatus($notifiable, string $channel, $result, $notificationId)
    {
        try {
            // 🔴 تحقق مما إذا كان `notificationId` غير موجود
            if (!$notificationId) {
                Log::error('Notification ID is missing.');
                return;
            }

            $notification = $notifiable->notifications()->where('id', $notificationId)->first();

            if (!$notification) {
                Log::error("Notification not found for ID: {$notificationId}");
                return;
            }

            $channels = $notification->channels ?? [];
            if (!in_array($channel, $channels)) {
                $channels[] = $channel;
            }

            $status = ($result['status'] ?? false) === true ? 'sent' : 'failed';
            $errorMessage = $status === 'failed' ? ($result['message'] ?? 'Unknown error') : null;

            $notification->update([
                'status' => $status,
                'sent_at' => $status === 'sent' ? now() : $notification->sent_at,
                'error_message' => $errorMessage,
                'channels' => json_encode($channels),
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating notification status: ' . $e->getMessage());
        }
    }





}

