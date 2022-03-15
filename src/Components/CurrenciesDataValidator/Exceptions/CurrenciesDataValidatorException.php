<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataValidator\Exceptions;

use CommissionTask\Components\CurrenciesDataValidator\Exceptions\Interfaces\CurrenciesDataValidatorException as CurrenciesDataValidatorExceptionContract;
use CommissionTask\Exceptions\CommissionTaskException;

final class CurrenciesDataValidatorException extends CommissionTaskException implements CurrenciesDataValidatorExceptionContract
{
    public const INCORRECT_CURRENCY_CODE_FIELD_MESSAGE = 'Incorrect currency code value';
    public const INCORRECT_DATE_FIELD_MESSAGE = 'Incorrect date';
    public const INCORRECT_IS_ARRAY_FIELD_MESSAGE = 'Incorrect array field';
    public const INCORRECT_CURRENCY_RATE_FIELD_MESSAGE = 'Incorrect currency rate field';
}
