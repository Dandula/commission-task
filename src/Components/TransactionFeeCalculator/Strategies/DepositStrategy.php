<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Strategies;

use CommissionTask\Components\TransactionFeeCalculator\Strategies\Interfaces\TransactionFeeCalculateStrategy as TransactionFeeCalculateStrategyContract;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\Traits\CommonCalculateOperations;
use CommissionTask\Entities\Transaction;
use CommissionTask\Services\Math as MathService;

class DepositStrategy implements TransactionFeeCalculateStrategyContract
{
    use CommonCalculateOperations;

    const FEE_RATE = '0.0003';

    /**
     * {@inheritDoc}
     */
    public function calculateTransactionFee(Transaction $transaction): string
    {
        $amount = $transaction->getAmount();
        $mathService = new MathService($this->determineScaleOfAmount($amount) + self::ROUNDED_OFF_DIGITS_NUMBER);

        $feeAmount = $mathService->mul($amount, self::FEE_RATE);

        return $this->ceilAmount($feeAmount);
    }
}
