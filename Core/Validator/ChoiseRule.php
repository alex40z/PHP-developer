<?php

namespace App\Core\Validator;

class ChoiseRule extends BaseRule
{
    private array $allowedValues;

    public function __construct(bool $isRequired, array $allowedValues)
    {
        $this->isRequired = $isRequired;
        $this->allowedValues = $allowedValues;
    }

    public function validate(string $paramName, string $paramValue): bool
    {
        if (!in_array($paramValue, $this->allowedValues)) {
            $this->errorMessage = "Параметр $paramName допускает значения: " . implode(', ', $this->allowedValues);
            return false;
        }
        return true;
    }
}