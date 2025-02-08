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

    public function billersIndex(User $user)
    {
        // تحقق من الصلاحيات أو أي شروط أخرى
        return $user->hasPermissionTo('billers-index'); // إذا كنت تستخدم Spatie
    }

    public function billersAdd(User $user)
    {
        // تحقق من الصلاحيات أو أي شروط أخرى
        return $user->hasPermissionTo('billers-add'); // إذا كنت تستخدم Spatie
    }

    public function billersEdit(User $user)
    {
        // تحقق من الصلاحيات أو أي شروط أخرى
        return $user->hasPermissionTo('billers-edit'); // إذا كنت تستخدم Spatie
    }
    public function billersDelete(User $user)
    {
        // تحقق من الصلاحيات أو أي شروط أخرى
        return $user->hasPermissionTo('billers-delete'); // إذا كنت تستخدم Spatie
    }

    public function customers_Index(User $user)
    {
        // تحقق من الصلاحيات أو أي شروط أخرى
        return $user->hasPermissionTo('customers-index'); // إذا كنت تستخدم Spatie
    }


}
