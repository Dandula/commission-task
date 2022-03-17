<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Exceptions;

use CommissionTask\Exceptions\CommissionTaskException;

final class TransactionFeeCalculatorException extends CommissionTaskException
{
    public const UNDEFINED_SCALE_MESSAGE = 'Undefined scale of amount';

    public const INSUFFICIENT_ACCURACY_MESSAGE = 'Insufficient accuracy of amount';
}
