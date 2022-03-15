<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataReader\Exceptions;

use CommissionTask\Components\CurrenciesDataReader\Exceptions\Interfaces\CurrenciesDataReaderException as CurrenciesDataReaderExceptionContract;
use CommissionTask\Exceptions\CommissionTaskException;

final class ApiCurrenciesDataReaderException extends CommissionTaskException implements CurrenciesDataReaderExceptionContract
{
    public const FAILED_RECEIVE_DATA_MESSAGE = 'Failed to receive data on currencies rates';
    public const INVALID_JSON_DATA_MESSAGE = 'Invalid JSON data';
}
