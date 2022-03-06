<?php

declare(strict_types=1);

namespace CommissionTask\Factories\Exceptions;

use CommissionTask\Factories\Exceptions\Interfaces\TransactionFeeCalculatorStrategyFactoryException as TransactionFeeCalculatorStrategyFactoryExceptionContract;
use CommissionTask\Exceptions\CommissionTaskException;

final class TransactionFeeCalculatorStrategyFactoryException extends CommissionTaskException implements TransactionFeeCalculatorStrategyFactoryExceptionContract
{
    const UNDEFINED_TRANSACTION_TYPE_MESSAGE = 'Undefined transaction type';
    const UNDEFINED_USER_TYPE_MESSAGE        = 'Undefined user type';
}
