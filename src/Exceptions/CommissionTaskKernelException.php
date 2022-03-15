<?php

declare(strict_types=1);

namespace CommissionTask\Exceptions;

use CommissionTask\Exceptions\Interfaces\CommissionTaskThrowable;
use Exception;

class CommissionTaskKernelException extends Exception implements CommissionTaskThrowable
{
    public const DEFAULT_MESSAGE = 'Application system error';

    public const CANNOT_DESERIALIZE_SINGLETON = 'Cannot deserialize singleton';

    public const SCRIPT_IS_NOT_RUN_FROM_COMMAND_LINE_MESSAGE = 'The script is not run from the command line';

    /**
     * Create a system application exception.
     */
    public function __construct(string $message = self::DEFAULT_MESSAGE, int $code = self::EXCEPTION_CODE_KERNEL)
    {
        parent::__construct($message, $code);
    }
}
