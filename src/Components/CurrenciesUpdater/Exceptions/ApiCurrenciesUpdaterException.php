<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesUpdater\Exceptions;

use CommissionTask\Components\CurrenciesUpdater\Exceptions\Interfaces\CurrenciesUpdaterException as CurrenciesUpdaterExceptionContract;
use CommissionTask\Exceptions\CommissionTaskException;

final class ApiCurrenciesUpdaterException extends CommissionTaskException implements CurrenciesUpdaterExceptionContract
{
    public const NO_BASE_CURRENCY_RATE_MESSAGE = 'No base currency rate';
}
