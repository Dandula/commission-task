<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataValidator\Exceptions;

use CommissionTask\Exceptions\CommissionTaskException;

final class CurrenciesDataValidatorException extends CommissionTaskException
{
    public const NO_REQUIRED_FIELDS_MESSAGE = 'No required fields';

    public const INCORRECT_CURRENCY_CODE_FIELD_MESSAGE = 'Incorrect currency code value';
    public const INCORRECT_IS_ARRAY_FIELD_MESSAGE = 'Incorrect array field';
    public const INCORRECT_CURRENCY_RATE_FIELD_MESSAGE = 'Incorrect currency rate field';
}
