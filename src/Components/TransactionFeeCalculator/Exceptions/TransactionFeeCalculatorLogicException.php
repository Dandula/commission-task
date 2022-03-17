<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Exceptions;

use CommissionTask\Exceptions\CommissionTaskException;

final class TransactionFeeCalculatorLogicException extends CommissionTaskException
{
    public const INSUFFICIENT_ACCURACY_MESSAGE = 'Insufficient accuracy of amount';
}
