<?php

declare(strict_types=1);

namespace CommissionTask\Components\CurrenciesDataReader\Exceptions;

use CommissionTask\Exceptions\CommissionTaskException;

final class CurrenciesDataReaderException extends CommissionTaskException
{
    public const FAILED_RECEIVE_DATA_MESSAGE = 'Failed to receive data on currencies rates';
    public const INVALID_JSON_DATA_MESSAGE = 'Invalid JSON data';
}
