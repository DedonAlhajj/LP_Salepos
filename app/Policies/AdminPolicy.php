<?php

namespace App\Policies;

use App\Models\SuperUser;
use App\Models\User;

class AdminPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function isAdmin(SuperUser $user)
    {
        return $user->role === 'admin';
    }

    /**
     * التحقق من أن المستخدم عادي.
     */
    public function isUser(SuperUser $user)
    {
        return $user->role === 'user';
    }

    public function checkRole(SuperUser $user, $role)
    {
        return $user->role === $role;
    }
    /*
     * if ($this->authorize('checkRole', ['user'])) {
    // منطق المستخدم العادي
     }*/
}
