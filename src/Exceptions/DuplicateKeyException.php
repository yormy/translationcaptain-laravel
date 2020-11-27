<?php

namespace Yormy\TranslationcaptainLaravel\Exceptions;

use Exception;

class DuplicateKeyException extends Exception
{
    /**
     * InvalidValueException constructor.
     */
    public function __construct(string $key, string $valueOrigin, string $valuetoMerge)
    {
        parent::__construct("Duplicate key found for key='$key' original value='$valueOrigin' wanted to merge='$valuetoMerge'");
    }
}
