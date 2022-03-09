<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionDataValidators\Traits;

use CommissionTask\Components\TransactionDataValidators\CsvTransactionDataValidator;
use CommissionTask\Components\TransactionDataValidators\Exceptions\TransactionDataValidatorException;
use CommissionTask\Components\TransactionDataValidators\Interfaces\TransactionDataValidator;
use CommissionTask\Exceptions\CommissionTaskArgumentException;

trait FieldFormat
{
    /**
     * Validate date field.
     *
     * @param mixed $value
     * @return $this
     * @throws TransactionDataValidatorException
     */
    private function validateDateField($value, string $format = self::DEFAULT_DATE_FORMAT): TransactionDataValidator
    {
        try {
            $this->dateService->parseDate($value, $format);
        } catch (CommissionTaskArgumentException $exception) {
            throw new TransactionDataValidatorException(
                TransactionDataValidatorException::INCORRECT_DATE_COLUMN_MESSAGE
            );
        }

        return $this;
    }

    /**
     * Validate unsigned integer field.
     *
     * @param mixed $value
     * @return $this
     * @throws TransactionDataValidatorException
     */
    private function validateUnsignedIntegerField($value): TransactionDataValidator
    {
        if (!preg_match(self::UNSIGNED_INTEGER_REGEXP, $value)) {
            throw new TransactionDataValidatorException(
                TransactionDataValidatorException::INCORRECT_UNSIGNED_INTEGER_COLUMN_MESSAGE
            );
        }

        return $this;
    }

    /**
     * Validate unsigned float field.
     *
     * @param mixed $value
     * @return $this
     * @throws TransactionDataValidatorException
     */
    private function validateUnsignedFloatField($value): TransactionDataValidator
    {
        if (!preg_match(self::UNSIGNED_FLOAT_REGEXP, $value)) {
            throw new TransactionDataValidatorException(
                TransactionDataValidatorException::INCORRECT_UNSIGNED_FLOAT_COLUMN_MESSAGE
            );
        }

        return $this;
    }

    /**
     * Validate field in array of acceptable values.
     *
     * @param mixed $value
     * @return $this
     * @throws TransactionDataValidatorException
     */
    private function validateInArrayField($value, array $acceptableValues): TransactionDataValidator
    {
        if (!in_array($value, $acceptableValues)) {
            throw new TransactionDataValidatorException(
                TransactionDataValidatorException::INCORRECT_IN_ARRAY_COLUMN_MESSAGE
            );
        }

        return $this;
    }

    /**
     * Validate currency code field.
     *
     * @param mixed $value
     * @return $this
     * @throws TransactionDataValidatorException
     */
    private function validateCurrencyCodeField($value): TransactionDataValidator
    {
        if (!preg_match(self::CURRENCY_CODE_REGEXP, $value)) {
            throw new TransactionDataValidatorException(
                TransactionDataValidatorException::INCORRECT_CURRENCY_CODE_COLUMN_MESSAGE
            );
        }

        return $this;
    }
}
