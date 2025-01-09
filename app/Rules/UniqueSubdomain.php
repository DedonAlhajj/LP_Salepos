<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueSubdomain implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        // استعلام التحقق من وجود الدومين في الجدول المؤقت
        $existsInPending = $this->checkInPendingUsers($value);

        // استعلام التحقق من وجود الدومين في الجدول الدائم
        $existsInDomains = $this->checkInDomains($value);
        // التحقق إذا كان النطاق الكامل موجودًا في قاعدة البيانات
        if ($existsInPending || $existsInDomains) {
            $fail("The domain '{$value}' is already taken. Please choose another one."); // رسالة الخطأ
        }
    }

    /**
     * التحقق من وجود الدومين في جدول pending_users
     */
    private function checkInPendingUsers(string $domain): bool
    {
        return DB::table('pending_users')
            ->where('domain', $domain)
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * التحقق من وجود الدومين في جدول domains
     */
    private function checkInDomains(string $domain): bool
    {

        $fullDomain = $domain . '.' . env('SESSION_DOMAIN_CENTRAL', null);
        return DB::table('domains')
            ->where('domain', $fullDomain)
            ->exists();
    }
}
