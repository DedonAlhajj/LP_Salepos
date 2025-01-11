<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\User;
use App\Notifications\TenantUserCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class CreateAdminAccount implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /** @var Tenant */
    protected $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle()
    {
        // تعيين المستأجر الحالي إذا لم يتم تعيينه تلقائيًا
        tenancy()->initialize($this->tenant);

        // بيانات المستأجر
        $tenant = $this->tenant;
        $password = Str::random(10);

        // الحصول على super_user المرتبط بالمستأجر
        $superUser = $tenant->superUser;

        // إنشاء حساب المستخدم
        $user = User::create([
            'name' => $tenant->name . ' Admin',
            'email' => $superUser->email,
            'password' => bcrypt($password),
            'tenant_id' => $tenant->id,
            'role_id' => 1,
        ]);

        // إرسال بريد إلكتروني للمستخدم
        $dashboardUrl = 'https://' . $tenant->domains[0]->domain . '/dash';
        $superUser->notify(new TenantUserCreated([
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password,
        ], $dashboardUrl));


    }
}
