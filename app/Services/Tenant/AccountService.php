<?php

namespace App\Services\Tenant;

use App\Models\Account;

class AccountService
{

    public function getDefaultAccountId(): int
    {
        return Account::where('is_default', 1)->value('id');
    }

}

