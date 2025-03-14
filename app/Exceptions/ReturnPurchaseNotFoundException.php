<?php

namespace App\Exceptions;

use Exception;

class ReturnPurchaseNotFoundException extends Exception
{
    public function __construct($message = "Return Purchase not found.", $code = 404)
    {
        parent::__construct($message, $code);
    }
}
