<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataValidators\Exceptions;

use CommissionTask\Components\CurrenciesDataValidators\Exceptions\Interfaces\CurrenciesDataValidatorException as CurrenciesDataValidatorExceptionContract;
use CommissionTask\Exceptions\CommissionTaskException;

final class ApiCurrenciesDataValidatorException extends CommissionTaskException implements CurrenciesDataValidatorExceptionContract
{
    const NO_REQUIRED_FIELDS_MESSAGE = 'No required fields';
}
