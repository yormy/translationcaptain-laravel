<?php

namespace Yormy\TranslationcaptainLaravel\Exceptions;

use Exception;

class InvalidSetupException extends Exception
{
    /**
     * InvalidValueException constructor.
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
