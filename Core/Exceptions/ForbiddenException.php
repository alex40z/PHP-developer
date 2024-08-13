<?php

namespace App\Core\Exceptions;

class ForbiddenException extends ClientException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 403);
    }
}
