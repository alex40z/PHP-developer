<?php

namespace App\Core\Validator;

class StringRule extends BaseRule
{
    private ?int $minValue;
    private ?int $maxValue;

    public function __construct(bool $isRequired, int $minValue = null, int $maxValue = null)
    {
        $this->isRequired = $isRequired;
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
    }

    public function validate(string $paramName, string $paramValue): bool
    {
        if (isset($this->minValue) && mb_strlen($paramValue) < $this->minValue) {
            $this->errorMessage = "Параметр $paramName должен быть не короче $this->minValue символов";
            return false;
        }
        if (isset($this->maxValue) && mb_strlen($paramValue) > $this->maxValue) {
            $this->errorMessage = "Параметр $paramName должен быть не длиннее $this->maxValue символов";
            return false;
        }
        return true;
    }
}
