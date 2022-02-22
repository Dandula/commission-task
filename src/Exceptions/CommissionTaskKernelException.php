<?php

declare(strict_types=1);

namespace CommissionTask\Exceptions;

use CommissionTask\Exceptions\Interfaces\CommissionTaskThrowable;
use Exception;

class CommissionTaskKernelException extends Exception implements CommissionTaskThrowable
{
    /**
     * Create a system application exception.
     *
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message = 'Application system error', int $code = self::EXCEPTION_CODE_KERNEL)
    {
        parent::__construct($message, $code);
    }
}
