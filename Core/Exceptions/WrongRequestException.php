<?php

namespace App\Core\Exceptions;

class WrongRequestException extends ClientException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 400);
    }
}
