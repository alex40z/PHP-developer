<?php

namespace App\Core\Validator;

abstract class BaseRule
{
    protected bool $isRequired;
    protected string $errorMessage;

    public function getIsRequired(): bool
    {
        return $this->isRequired;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
