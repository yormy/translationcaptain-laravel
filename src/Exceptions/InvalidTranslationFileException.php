<?php

namespace Yormy\TranslationcaptainLaravel\Exceptions;

use Exception;

class InvalidTranslationFileException extends Exception
{
    /**
     * InvalidValueException constructor.
     */
    public function __construct(string $path)
    {
        parent::__construct("Translation file is invalid: $path");
    }
}
