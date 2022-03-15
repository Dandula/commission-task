<?php

declare(strict_types=1);

namespace CommissionTask\Exceptions;

use CommissionTask\Exceptions\Interfaces\CommissionTaskThrowable;
use OutOfBoundsException;

class CommissionTaskOutOfBoundsException extends OutOfBoundsException implements CommissionTaskThrowable
{
    public const DEFAULT_MESSAGE = 'ID does not exist';

    /**
     * Create an invalid ID exception.
     */
    public function __construct(string $message = self::DEFAULT_MESSAGE, int $code = self::EXCEPTION_CODE_OUT_OF_BOUNDS)
    {
        parent::__construct($message, $code);
    }
}
