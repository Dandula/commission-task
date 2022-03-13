<?php

declare(strict_types=1);

namespace CommissionTask\Components\Outputter\Exceptions;

use CommissionTask\Components\Outputter\Exceptions\Interfaces\OutputterException as OutputerExceptionContract;
use CommissionTask\Exceptions\CommissionTaskArgumentException;

final class ConsoleOutputterException extends CommissionTaskArgumentException implements OutputerExceptionContract
{
    const UNSUPPORTED_OUTPUT_DATA_MESSAGE = 'Unsupported output data';
}
