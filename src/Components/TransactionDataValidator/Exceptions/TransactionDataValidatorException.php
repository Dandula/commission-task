<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionDataValidator\Exceptions;

use CommissionTask\Exceptions\CommissionTaskException;

final class TransactionDataValidatorException extends CommissionTaskException
{
    public const REQUIRED_FIELD_NOT_SET_MESSAGE = 'Required column is not set';

    public const INCORRECT_DATE_COLUMN_MESSAGE = 'Incorrect date column';
    public const INCORRECT_UNSIGNED_INTEGER_COLUMN_MESSAGE = 'Incorrect unsigned integer column';
    public const INCORRECT_UNSIGNED_FLOAT_COLUMN_MESSAGE = 'Incorrect unsigned float column';
    public const INCORRECT_IN_ARRAY_COLUMN_MESSAGE = 'The value is not included in the list of acceptable values';
    public const INSUFFICIENT_NUMBER_COLUMNS_MESSAGE = 'Insufficient number of columns in transaction data';
}
