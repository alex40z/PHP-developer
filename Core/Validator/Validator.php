<?php

namespace App\Core\Validator;

use App\Core\Exceptions\WrongRequestException;

class Validator
{
    private array $errorsList = [];

    public function checkParamsValidity(array $params, array $validationRules, int $minParamsCount = null, int $maxParamsCount = null): void
    {
        if (isset($minParamsCount) && count($params) < $minParamsCount) {
            $this->errorsList[] = "Количество параметров должно быть не меньше $minParamsCount";
        }
        if (isset($maxParamsCount) && count($params) > $maxParamsCount) {
            $this->errorsList[] = "Количество параметров должно быть не больше $maxParamsCount";
        }

        foreach ($validationRules as $validationRule) {
            $paramName = $validationRule[0];
            $ruleObject = $validationRule[1];
            $paramValue = $params[$paramName];
            if (!isset($paramValue)) {
                if ($ruleObject->getIsRequired()) {
                    $this->errorsList[] = "Не указан обязательный параметр: $paramName";
                }
            } else {
                if (!$ruleObject->validate($paramName, $paramValue)) {
                    $this->errorsList[] = $ruleObject->getErrorMessage();
                };
            }
        }

        foreach ($params as $paramName => $paramValue) {
            $wrongParam = true;
            foreach ($validationRules as $validationRule) {
                if ($paramName == $validationRule[0]) {
                    $wrongParam = false;
                    break;
                };
            }
            if ($wrongParam) {
                $this->errorsList[] = "Недопустимый параметр: $paramName";
            }
        }

        if (count($this->errorsList) > 0) {
            throw new WrongRequestException(implode(';', $this->errorsList));
        }
    }
}
