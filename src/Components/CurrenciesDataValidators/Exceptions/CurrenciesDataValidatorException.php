<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataValidators\Exceptions;

use CommissionTask\Components\CurrenciesDataValidators\Exceptions\Interfaces\CurrenciesDataValidatorException as CurrenciesDataValidatorExceptionContract;
use CommissionTask\Exceptions\CommissionTaskException;

final class CurrenciesDataValidatorException extends CommissionTaskException implements CurrenciesDataValidatorExceptionContract
{
    const INCORRECT_CURRENCY_CODE_FIELD_MESSAGE = 'Incorrect currency code value';
    const INCORRECT_DATE_FIELD_MESSAGE = 'Incorrect date';
    const INCORRECT_IS_ARRAY_FIELD_MESSAGE = 'Incorrect array field';
    const INCORRECT_CURRENCY_RATE_FIELD_MESSAGE = 'Incorrect currency rate field';
}
