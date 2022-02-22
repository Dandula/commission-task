<?php

declare(strict_types=1);

namespace CommissionTask\Exceptions;

use Exception;

class CommissionTaskKernelException extends Exception
{
    /**
     * Create a basic application exception.
     *
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message = 'Application system error', int $code = -128)
    {
        parent::__construct($message, $code);
    }
}
