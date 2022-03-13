<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionDataValidator\Exceptions;

use CommissionTask\Components\TransactionDataValidator\Exceptions\Interfaces\TransactionDataValidatorException as TransactionDataValidatorExceptionContract;
use CommissionTask\Exceptions\CommissionTaskException;

final class TransactionDataValidatorException extends CommissionTaskException implements TransactionDataValidatorExceptionContract
{
    const INCORRECT_FIELDS_NUMBER_MESSAGE = 'Incorrect number of columns in the transaction data';

    const REQUIRED_FIELD_NOT_SET_MESSAGE = 'Required column is not set';

    const INCORRECT_DATE_COLUMN_MESSAGE = 'Incorrect date column';
    const INCORRECT_UNSIGNED_INTEGER_COLUMN_MESSAGE = 'Incorrect unsigned integer column';
    const INCORRECT_UNSIGNED_FLOAT_COLUMN_MESSAGE = 'Incorrect unsigned float column';
    const INCORRECT_IN_ARRAY_COLUMN_MESSAGE = 'The value is not included in the list of acceptable values';
    const INCORRECT_CURRENCY_CODE_COLUMN_MESSAGE = 'Incorrect currency code column';
}
