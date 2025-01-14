<?php

namespace App\Exceptions;

use Exception;

class FeatureDeletionException extends Exception
{
    public function __construct($message = "خطأ أثناء حذف الخاصية.")
    {
        parent::__construct($message);
    }
}
