<?php

namespace App\Jobs;

use App\Notifications\TenantUserCreated;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class CreateAdminAccount
{
    public function __invoke($event)
    {//Call to undefined method App\Jobs\CreateAdminAccount::handle()
        $tenant = $event->tenant;
        $password = Str::random(10);
        // الحصول على super_user المرتبط بالمستأجر
        $superUser = $tenant->superUser;

        // إنشاء حساب مستخدم في جدول users باستخدام بيانات super_user
        $user = User::create([
            'name' => $tenant->name . ' Admin', // أو اسم super_user إذا كان مطلوبًا
            'email' => $superUser->email, // استخدام البريد الإلكتروني الخاص بـ super_user
            'password' => bcrypt($password), // استخدام كلمة المرور المشفرة من super_user
            'tenant_id' => $tenant->id, // إذا كان هناك علاقة مع المستأجر
            'role' => 1, // تحديد دور المستخدم كمسؤول
        ]);

        // إرسال بريد إلكتروني إلى المستخدم
        $dashboardUrl = 'https://' . $tenant->domains->first()->domain . '/dash'; // رابط الداشبورد
        Notification::send($user, new TenantUserCreated([
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password, // رسالة مخصصة
        ], $dashboardUrl));
    }
}
