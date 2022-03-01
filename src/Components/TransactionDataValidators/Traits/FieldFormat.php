<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionDataValidators\Traits;

use CommissionTask\Components\TransactionDataValidators\CsvTransactionDataValidator;
use CommissionTask\Components\TransactionDataValidators\Exceptions\IncorrectFieldFormat;
use CommissionTask\Exceptions\CommissionTaskArgumentException;

trait FieldFormat
{
    /**
     * Validate date field.
     *
     * @param mixed $value
     * @return $this
     * @throws IncorrectFieldFormat
     */
    private function validateDateField($value, string $format = 'Y-m-d')
    {
        try {
            $this->dateService->parseDate($value, $format);
        } catch (CommissionTaskArgumentException $exception) {
            throw new IncorrectFieldFormat('Incorrect date column');
        }

        return $this;
    }

    /**
     * Validate unsigned integer field.
     *
     * @param mixed $value
     * @return $this
     * @throws IncorrectFieldFormat
     */
    private function validateUnsignedIntegerField($value)
    {
        if (!preg_match('/^[1-9]\d*$/', $value)) {
            throw new IncorrectFieldFormat('Incorrect unsigned integer column');
        }

        return $this;
    }

    /**
     * Validate unsigned float field.
     *
     * @param mixed $value
     * @return $this
     * @throws IncorrectFieldFormat
     */
    private function validateUnsignedFloatField($value)
    {
        if (!preg_match('/^[1-9]\d*\.?\d*$/', $value)) {
            throw new IncorrectFieldFormat('Incorrect unsigned float column');
        }

        return $this;
    }

    /**
     * Validate field in array of acceptable values.
     *
     * @param mixed $value
     * @return $this
     * @throws IncorrectFieldFormat
     */
    private function validateInArrayField($value, array $acceptableValues)
    {
        if (!in_array($value, $acceptableValues)) {
            throw new IncorrectFieldFormat('The value is not included in the list of acceptable values');
        }

        return $this;
    }

    /**
     * Validate currency code field.
     *
     * @param mixed $value
     * @return $this
     * @throws IncorrectFieldFormat
     */
    private function validateCurrencyCodeField($value): CsvTransactionDataValidator
    {
        if (!preg_match('/^[a-zA-Z]{3}$/', $value)) {
            throw new IncorrectFieldFormat('Incorrect currency code column');
        }

        return $this;
    }
}
