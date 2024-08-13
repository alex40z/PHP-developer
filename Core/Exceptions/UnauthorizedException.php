<?php

namespace App\Core\Exceptions;

class UnauthorizedException extends ClientException
{
    public function __construct()
    {
        parent::__construct('Необходима авторизация', 401);
    }
}
