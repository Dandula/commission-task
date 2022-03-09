<?php

declare(strict_types=1);

namespace CommissionTask\Components\Outputer\Exceptions;

use CommissionTask\Components\Outputer\Exceptions\Interfaces\OutputerException as OutputerExceptionContract;
use CommissionTask\Exceptions\CommissionTaskArgumentException;

final class ConsoleOutputerException extends CommissionTaskArgumentException implements OutputerExceptionContract
{
    const UNSUPPORTED_OUTPUT_DATA_MESSAGE = 'Unsupported output data';
}
