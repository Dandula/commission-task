<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataValidators\Traits;

use CommissionTask\Components\CurrenciesDataValidators\Exceptions\CurrenciesDataValidatorException;
use CommissionTask\Components\CurrenciesDataValidators\Interfaces\CurrenciesDataValidator;
use CommissionTask\Exceptions\CommissionTaskArgumentException;

trait FieldFormat
{
    /**
     * Validate currency code field.
     *
     * @param mixed $value
     *
     * @return $this
     *
     * @throws CurrenciesDataValidatorException
     */
    private function validateCurrencyCodeField($value): CurrenciesDataValidator
    {
        if (!preg_match(self::CURRENCY_CODE_REGEXP, $value)) {
            throw new CurrenciesDataValidatorException(CurrenciesDataValidatorException::INCORRECT_CURRENCY_CODE_FIELD_MESSAGE);
        }

        return $this;
    }

    /**
     * Validate date field.
     *
     * @param mixed $value
     *
     * @return $this
     *
     * @throws CurrenciesDataValidatorException
     */
    private function validateDateField($value, string $format = self::DEFAULT_DATE_FORMAT): CurrenciesDataValidator
    {
        try {
            $this->dateService->parseDate($value, $format);
        } catch (CommissionTaskArgumentException $exception) {
            throw new CurrenciesDataValidatorException(CurrenciesDataValidatorException::INCORRECT_DATE_FIELD_MESSAGE);
        }

        return $this;
    }

    /**
     * Validate field in array of acceptable values.
     *
     * @param mixed $value
     *
     * @return $this
     *
     * @throws CurrenciesDataValidatorException
     */
    private function validateIsArrayField($value): CurrenciesDataValidator
    {
        if (!is_array($value)) {
            throw new CurrenciesDataValidatorException(CurrenciesDataValidatorException::INCORRECT_IS_ARRAY_FIELD_MESSAGE);
        }

        return $this;
    }

    /**
     * Validate currency rate field.
     *
     * @param mixed $value
     *
     * @return $this
     *
     * @throws CurrenciesDataValidatorException
     */
    private function validateCurrencyRateField($value): CurrenciesDataValidator
    {
        if (!(is_float($value) || is_int($value))) {
            throw new CurrenciesDataValidatorException(CurrenciesDataValidatorException::INCORRECT_CURRENCY_RATE_FIELD_MESSAGE);
        }

        return $this;
    }
}
