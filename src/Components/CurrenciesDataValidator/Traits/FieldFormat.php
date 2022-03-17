<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataValidator\Traits;

use CommissionTask\Components\CurrenciesDataValidator\Exceptions\CurrenciesDataValidatorException;
use CommissionTask\Components\CurrenciesDataValidator\Interfaces\CurrenciesDataValidator;
use CommissionTask\Exceptions\CommissionTaskArgumentException;

trait FieldFormat
{
    /**
     * Validate currency code field.
     *
     * @return $this
     *
     * @throws CurrenciesDataValidatorException
     */
    private function validateCurrencyCodeField(mixed $value): self
    {
        if (!preg_match(self::CURRENCY_CODE_REGEXP, $value)) {
            throw new CurrenciesDataValidatorException(CurrenciesDataValidatorException::INCORRECT_CURRENCY_CODE_FIELD_MESSAGE);
        }

        return $this;
    }

    /**
     * Validate field in array of acceptable values.
     *
     * @return $this
     *
     * @throws CurrenciesDataValidatorException
     */
    private function validateIsArrayField(mixed $value): CurrenciesDataValidator
    {
        if (!is_array($value)) {
            throw new CurrenciesDataValidatorException(CurrenciesDataValidatorException::INCORRECT_IS_ARRAY_FIELD_MESSAGE);
        }

        return $this;
    }

    /**
     * Validate currency rate field.
     *
     * @return $this
     *
     * @throws CurrenciesDataValidatorException
     */
    private function validateCurrencyRateField(mixed $value): CurrenciesDataValidator
    {
        if (!(is_float($value) || is_int($value))) {
            throw new CurrenciesDataValidatorException(CurrenciesDataValidatorException::INCORRECT_CURRENCY_RATE_FIELD_MESSAGE);
        }

        return $this;
    }

    /**
     * Validate field in array of acceptable values.
     *
     * @return $this
     *
     * @throws CurrenciesDataValidatorException
     */
    private function validateInArrayField(mixed $value, array $acceptableValues): self
    {
        if (!in_array($value, $acceptableValues, strict: true)) {
            throw new CurrenciesDataValidatorException(CurrenciesDataValidatorException::INCORRECT_IN_ARRAY_COLUMN_MESSAGE);
        }

        return $this;
    }
}
