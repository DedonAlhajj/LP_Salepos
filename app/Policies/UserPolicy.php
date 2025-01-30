<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function usersIndex(User $user)
    {
        // تحقق من الصلاحيات أو أي شروط أخرى
        return $user->hasPermissionTo('users-index'); // إذا كنت تستخدم Spatie
    }
    public function usersAdd(User $user)
    {
        // تحقق من الصلاحيات أو أي شروط أخرى
        return $user->hasPermissionTo('users-add'); // إذا كنت تستخدم Spatie
    }

}
