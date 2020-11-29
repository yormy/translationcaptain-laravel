<?php

namespace Yormy\TranslationcaptainLaravel\Exceptions;

use Exception;

class MissingTranslationException extends Exception
{
    /**
     * InvalidValueException constructor.
     */
    public function __construct(string $key)
    {
        parent::__construct("Key not translated: $key");
    }
}
