<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataReaders\Exceptions;

use CommissionTask\Components\CurrenciesDataReaders\Exceptions\Interfaces\CurrenciesDataReaderException as CurrenciesDataReaderExceptionContract;
use CommissionTask\Exceptions\CommissionTaskException;

final class ApiCurrenciesDataReaderException extends CommissionTaskException implements CurrenciesDataReaderExceptionContract
{
    const FAILED_RECEIVE_DATA_MESSAGE = 'Failed to receive data on currencies rates';
    const INVALID_JSON_DATA_MESSAGE = 'Invalid JSON data';
}
