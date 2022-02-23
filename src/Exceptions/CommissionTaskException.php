<?php

declare(strict_types=1);

namespace CommissionTask\Exceptions;

use CommissionTask\Exceptions\Interfaces\CommissionTaskThrowable;
use RuntimeException;

class CommissionTaskException extends RuntimeException implements CommissionTaskThrowable
{
    const DEFAULT_MESSAGE = 'Undefined exception of application';

    /**
     * Create a basic application exception.
     *
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message = self::DEFAULT_MESSAGE, int $code = self::EXCEPTION_CODE_BASIC)
    {
        parent::__construct($message, $code);
    }
}
