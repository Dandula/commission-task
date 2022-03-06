<?php

declare(strict_types=1);

namespace CommissionTask\Exceptions;

use CommissionTask\Exceptions\Interfaces\CommissionTaskThrowable;
use Exception;

class CommissionTaskKernelException extends Exception implements CommissionTaskThrowable
{
    const DEFAULT_MESSAGE = 'Application system error';

    const SCRIPT_IS_NOT_RUN_FROM_COMMAND_LINE_MESSAGE = 'The script is not run from the command line';

    /**
     * Create a system application exception.
     *
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message = self::DEFAULT_MESSAGE, int $code = self::EXCEPTION_CODE_KERNEL)
    {
        parent::__construct($message, $code);
    }
}
