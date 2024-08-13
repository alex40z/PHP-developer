<?php

namespace App\Core\Validator;

use App\Core\Config\Config;

class IntegerRule extends BaseRule
{
    private int|string|null $minValue;
    private int|string|null $maxValue;

    public function __construct(bool $isRequired, int $minValue = null, int|string $maxValue = null)
    {
        $this->isRequired = $isRequired;
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
    }

    public function validate(string $paramName, string $paramValue): bool
    {
        if (!is_numeric($paramValue)) {
            $this->errorMessage = "Параметр $paramName должен быть числом";
            return false;
        }
        if (isset($this->minValue) && $paramValue < $this->minValue) {
            $this->errorMessage = "Параметр $paramName должен быть не меньше $this->minValue";
            return false;
        }
        if (isset($this->maxValue)) {
            if ($this->maxValue == 'max_files') {
                $config = new Config();
                $this->maxValue = $config->configParam('storage_maxfiles');
            }
            if ($paramValue > $this->maxValue) {
                $this->errorMessage = "Параметр $paramName должен быть не больше $this->maxValue";
                return false;
            }
        }
        return true;
    }
}
