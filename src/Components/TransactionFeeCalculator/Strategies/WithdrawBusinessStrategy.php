<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Strategies;

use CommissionTask\Components\TransactionFeeCalculator\Strategies\Interfaces\TransactionFeeCalculateStrategy as TransactionFeeCalculateStrategyContract;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\Traits\CommonCalculateOperations;
use CommissionTask\Entities\Transaction;
use CommissionTask\Services\Config as ConfigService;
use CommissionTask\Services\Math as MathService;

class WithdrawBusinessStrategy implements TransactionFeeCalculateStrategyContract
{
    use CommonCalculateOperations;

    /**
     * {@inheritDoc}
     */
    public function calculateTransactionFee(Transaction $transaction): string
    {
        $amount = $transaction->getAmount();
        $mathService = new MathService($this->determineScaleOfAmount($amount) + $this->getRoundedOffDigitsNumber());

        $feeAmount = $mathService->mul(
            $amount,
            ConfigService::getConfigByName('feeCalculator.feeRateWithdrawBusiness')
        );

        return $this->ceilAmount($feeAmount);
    }
}
