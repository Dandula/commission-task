<?php

declare(strict_types=1);

namespace CommissionTask\Exceptions;

use CommissionTask\Exceptions\Interfaces\CommissionTaskThrowable;
use RuntimeException;

class CommissionTaskException extends RuntimeException implements CommissionTaskThrowable
{
    public const DEFAULT_MESSAGE = 'Undefined exception of application';

    public const COMMAND_LINE_PARAMETER_IS_NOT_SET_MESSAGE = 'The command line parameter #%d is not set';

    public const UNDEFINED_CURRENCY_RATE_MESSAGE = "Undetermined currency '%s' rate";

    public const UNDEFINED_TRANSACTION_TYPE_MESSAGE = 'Undefined transaction type';
    public const UNDEFINED_USER_TYPE_MESSAGE = 'Undefined user type';

    /**
     * Create a basic application exception.
     */
    public function __construct(string $message = self::DEFAULT_MESSAGE, int $code = self::EXCEPTION_CODE_BASIC)
    {
        parent::__construct($message, $code);
    }
}
