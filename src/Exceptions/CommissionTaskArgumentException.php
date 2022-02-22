<?php

declare(strict_types=1);

namespace CommissionTask\Exceptions;

use CommissionTask\Exceptions\Interfaces\CommissionTaskThrowable;
use InvalidArgumentException;

class CommissionTaskArgumentException extends InvalidArgumentException implements CommissionTaskThrowable
{
    /**
     * Create an invalid argument exception.
     *
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message = 'Invalid argument given', int $code = self::EXCEPTION_CODE_BASIC)
    {
        parent::__construct($message, $code);
    }
}
