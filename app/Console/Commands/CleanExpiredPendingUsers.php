<?php

namespace App\Console\Commands;

use App\Models\PendingUser;
use Illuminate\Console\Command;

class CleanExpiredPendingUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-expired-pending-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired pending user records';

    // الكود الذي يتم تنفيذه عند تشغيل الأمر
    public function handle()
    {
        // حذف السجلات التي انتهت صلاحيتها
        $deletedRows = PendingUser::where('expires_at', '<', now())->delete();

        // طباعة رسالة تأكيد في الطرفية
        $this->info("Deleted {$deletedRows} expired pending users.");
    }
}
