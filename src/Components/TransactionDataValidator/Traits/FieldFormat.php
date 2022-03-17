<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionDataValidator\Traits;

use CommissionTask\Components\TransactionDataValidator\Exceptions\TransactionDataValidatorException;
use CommissionTask\Exceptions\CommissionTaskArgumentException;

trait FieldFormat
{
    /**
     * Validate date field.
     *
     * @return $this
     *
     * @throws TransactionDataValidatorException
     */
    private function validateDateField(mixed $value, string $format = self::DEFAULT_DATE_FORMAT): self
    {
        try {
            $this->dateService->parseDate($value, $format);
        } catch (CommissionTaskArgumentException) {
            throw new TransactionDataValidatorException(TransactionDataValidatorException::INCORRECT_DATE_COLUMN_MESSAGE);
        }

        return $this;
    }

    /**
     * Validate unsigned integer field.
     *
     * @return $this
     *
     * @throws TransactionDataValidatorException
     */
    private function validateUnsignedIntegerField(mixed $value): self
    {
        if (!preg_match(self::UNSIGNED_INTEGER_REGEXP, $value)) {
            throw new TransactionDataValidatorException(TransactionDataValidatorException::INCORRECT_UNSIGNED_INTEGER_COLUMN_MESSAGE);
        }

        return $this;
    }

    /**
     * Validate unsigned float field.
     *
     * @return $this
     *
     * @throws TransactionDataValidatorException
     */
    private function validateUnsignedFloatField(mixed $value): self
    {
        if (!preg_match(self::UNSIGNED_FLOAT_REGEXP, $value)) {
            throw new TransactionDataValidatorException(TransactionDataValidatorException::INCORRECT_UNSIGNED_FLOAT_COLUMN_MESSAGE);
        }

        return $this;
    }

    /**
     * Validate field in array of acceptable values.
     *
     * @return $this
     *
     * @throws TransactionDataValidatorException
     */
    private function validateInArrayField(mixed $value, array $acceptableValues): self
    {
        if (!in_array($value, $acceptableValues, strict: true)) {
            throw new TransactionDataValidatorException(TransactionDataValidatorException::INCORRECT_IN_ARRAY_COLUMN_MESSAGE);
        }

        return $this;
    }
}
