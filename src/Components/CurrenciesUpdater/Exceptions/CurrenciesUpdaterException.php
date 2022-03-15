<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesUpdater\Exceptions;

use CommissionTask\Exceptions\CommissionTaskException;

final class CurrenciesUpdaterException extends CommissionTaskException
{
    public const NO_BASE_CURRENCY_RATE_MESSAGE = 'No base currency rate';
}
