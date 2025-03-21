<?php

namespace App\Notifications\ChannelsNotification;

use App\Mail\UserDetails;
use App\Services\Mail\MailConfigSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailNotification implements NotificationInterface
{

    public function send(array $data): array
    {
        $mailSettings = MailConfigSetting::getSettings();
        if (!$mailSettings) {
            Log::warning('Email sending failed: No mail settings found.');
            return [
                'statue' => false,
                'message' => "Email sending failed: No mail settings found."
            ];
        }

        MailConfigSetting::setMailInfo($mailSettings);

        try {
            Mail::to($data['email'])->send(new UserDetails::class);
            return [
                'statue' => true,
                'message' => ""
            ];
        } catch (\Exception $e) {
            Log::error('Error sending email: ' . $e->getMessage());
            return [
                'statue' => false,
                'message' => 'Error sending email: ' .$e->getMessage()
            ];
        }
    }


}
//
//Schema::create('notifications', function (Blueprint $table) {
//    $table->uuid('id')->primary();
//    $table->uuid('batch_id')->nullable()->index(); // لدعم الإشعارات الجماعية
//    $table->string('type'); // نوع الإشعار
//    $table->morphs('notifiable'); // علاقة polymorphic لدعم جميع الكيانات
//    $table->jsonb('channels'); // دعم إشعار واحد مع قنوات متعددة
//    $table->enum('status', ['queued', 'processing', 'sent', 'delivered', 'failed', 'read'])
//        ->default('queued'); // تتبع حالة الإشعار بدقة
//    $table->text('error_message')->nullable(); // تسجيل الأخطاء
//    $table->jsonb('data'); // تخزين بيانات الإشعار لتكون مرنة جدًا
//    $table->timestamp('sent_at')->nullable(); // تسجيل وقت الإرسال الفعلي
//    $table->timestamp('read_at')->nullable(); // تسجيل وقت القراءة
//    $table->timestamps();
//});

