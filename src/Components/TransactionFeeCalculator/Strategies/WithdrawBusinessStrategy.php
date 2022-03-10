<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Strategies;

use CommissionTask\Components\TransactionFeeCalculator\Strategies\Interfaces\TransactionFeeCalculateStrategy as TransactionFeeCalculateStrategyContract;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\Traits\CommonCalculateOperations;
use CommissionTask\Entities\Transaction;
use CommissionTask\Services\Math;

class WithdrawBusinessStrategy implements TransactionFeeCalculateStrategyContract
{
    use CommonCalculateOperations;

    const FEE_RATE = '0.005';

    /**
     * {@inheritDoc}
     */
    public function calculateTransactionFee(Transaction $transaction): string
    {
        $amount = $transaction->getAmount();
        $mathService = new Math($this->determineScaleOfAmount($amount) + self::ROUNDED_OFF_DIGITS_NUMBER);

        $feeAmount = $mathService->mul($amount, self::FEE_RATE);

        return $this->ceilAmount($feeAmount);
    }
}
