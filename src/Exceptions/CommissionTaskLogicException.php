<?php

declare(strict_types=1);

namespace CommissionTask\Exceptions;

use CommissionTask\Exceptions\Interfaces\CommissionTaskThrowable;
use LogicException;

class CommissionTaskLogicException extends LogicException implements CommissionTaskThrowable
{
    public const DEFAULT_MESSAGE = 'Undefined logic exception';

    /**
     * Create an invalid argument exception.
     */
    public function __construct(string $message = self::DEFAULT_MESSAGE, int $code = self::EXCEPTION_CODE_ARGUMENT)
    {
        parent::__construct($message, $code);
    }
}
