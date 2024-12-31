<?php

namespace App\Actions;

use App\Exceptions\DomainAlreadyExistsException;
use App\Models\Domain;
use App\Models\Tenant;

class ValidateDomainAction
{
    public function execute(string $domain): void
    {
        if (Domain::where('domain', $domain)->exists()) {
            throw new DomainAlreadyExistsException('The domain is already taken.');
        }
    }
}
