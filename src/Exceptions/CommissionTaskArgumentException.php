<?php

declare(strict_types=1);

namespace CommissionTask\Exceptions;

use CommissionTask\Exceptions\Interfaces\CommissionTaskThrowable;
use InvalidArgumentException;

class CommissionTaskArgumentException extends InvalidArgumentException implements CommissionTaskThrowable
{
    const DEFAULT_MESSAGE = 'Invalid argument given';

    /**
     * Create an invalid argument exception.
     */
    public function __construct(string $message = self::DEFAULT_MESSAGE, int $code = self::EXCEPTION_CODE_ARGUMENT)
    {
        parent::__construct($message, $code);
    }
}
