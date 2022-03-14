<?php

declare(strict_types=1);

namespace CommissionTask\Components\Outputter\Exceptions;

use CommissionTask\Components\Outputter\Exceptions\Interfaces\OutputterException as OutputterExceptionContract;
use CommissionTask\Exceptions\CommissionTaskArgumentException;

final class ConsoleOutputterException extends CommissionTaskArgumentException implements OutputterExceptionContract
{
    const UNSUPPORTED_OUTPUT_DATA_MESSAGE = 'Unsupported output data';
}
