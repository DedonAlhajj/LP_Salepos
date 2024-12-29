<?php

namespace App\Policies;

use App\Models\SuperUser;
use App\Models\User;

class CheckUserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
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
