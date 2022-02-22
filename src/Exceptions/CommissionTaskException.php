<?php

declare(strict_types=1);

namespace CommissionTask\Exceptions;

use RuntimeException;

class CommissionTaskException extends RuntimeException
{
    /**
     * Create a basic application exception.
     *
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message = 'Undefined exception of application', int $code = -1)
    {
        parent::__construct($message, $code);
    }
}
