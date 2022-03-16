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
     * Create a new transaction fee calculator strategy instance for withdraw transactions of business user.
     */
    public function __construct(
        private MathService $mathService
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function calculateTransactionFee(Transaction $transaction): string
    {
        $amount = $transaction->getAmount();
        $taxableAmountScale = $this->determineScaleOfAmount($amount) + $this->getRoundedOffDigitsNumber();

        $feeAmount = $this->mathService->mul(
            $amount,
            ConfigService::getConfigByName('feeCalculator.feeRateWithdrawBusiness'),
            $taxableAmountScale
        );

        return $this->ceilAmount($feeAmount);
    }
}
