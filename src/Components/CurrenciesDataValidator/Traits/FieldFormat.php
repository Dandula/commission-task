<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataValidator\Traits;

use CommissionTask\Components\CurrenciesDataValidator\Exceptions\CurrenciesDataValidatorException;

trait FieldFormat
{
    /**
     * Validate currency code field.
     *
     * @return $this
     *
     * @throws CurrenciesDataValidatorException
     */
    private function validateCurrencyCodeField(mixed $value): static
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
    private function validateIsArrayField(mixed $value): static
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
    private function validateCurrencyRateField(mixed $value): static
    {
        if (!(is_float($value) || is_int($value))) {
            throw new CurrenciesDataValidatorException(CurrenciesDataValidatorException::INCORRECT_CURRENCY_RATE_FIELD_MESSAGE);
        }

        return $this;
    }
}
