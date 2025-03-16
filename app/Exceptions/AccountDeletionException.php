<?php

namespace App\Exceptions;

use Exception;


class AccountDeletionException extends Exception
{
    /**
     * Customize the exception message.
     */
    public function __construct(string $message = 'Account deletion failed.')
    {
        parent::__construct($message);
    }
}
