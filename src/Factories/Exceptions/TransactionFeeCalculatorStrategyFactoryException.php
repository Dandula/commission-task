<?php

declare(strict_types=1);

namespace CommissionTask\Factories\Exceptions;

use CommissionTask\Exceptions\CommissionTaskException;
use CommissionTask\Factories\Exceptions\Interfaces\TransactionFeeCalculatorStrategyFactoryException as TransactionFeeCalculatorStrategyFactoryExceptionContract;

final class TransactionFeeCalculatorStrategyFactoryException extends CommissionTaskException implements TransactionFeeCalculatorStrategyFactoryExceptionContract
{
    public const UNDEFINED_TRANSACTION_TYPE_MESSAGE = 'Undefined transaction type';
    public const UNDEFINED_USER_TYPE_MESSAGE = 'Undefined user type';
}
