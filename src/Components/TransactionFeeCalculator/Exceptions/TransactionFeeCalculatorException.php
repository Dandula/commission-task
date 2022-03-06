<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Exceptions;

use CommissionTask\Components\TransactionFeeCalculator\Exceptions\Interfaces\TransactionFeeCalculatorException as TransactionFeeCalculatorExceptionContract;
use CommissionTask\Exceptions\CommissionTaskException;

final class TransactionFeeCalculatorException extends CommissionTaskException implements TransactionFeeCalculatorExceptionContract
{
    const UNDEFINED_SCALE_MESSAGE = 'Undefined scale of amount';
}
