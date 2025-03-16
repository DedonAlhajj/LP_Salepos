<?php

namespace App\Exceptions;

use Exception;

class AccountCreationException extends Exception
{
    public function __construct($message = "Some Thing is Wrong")
    {
        parent::__construct($message);
    }
}
