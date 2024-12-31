<?php

namespace App\Exceptions;

use Exception;

class DomainAlreadyExistsException extends Exception
{

    public function __construct($message = 'Domain already exists.')
    {
        parent::__construct($message);
    }
}
