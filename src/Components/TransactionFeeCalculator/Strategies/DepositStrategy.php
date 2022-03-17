<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator\Strategies;

use CommissionTask\Components\TransactionFeeCalculator\Strategies\Interfaces\TransactionFeeCalculateStrategy as TransactionFeeCalculateStrategyContract;
use CommissionTask\Components\TransactionFeeCalculator\Strategies\Traits\CommonCalculateOperations;
use CommissionTask\Entities\Transaction;
use CommissionTask\Services\Config as ConfigService;
use CommissionTask\Services\Currency as CurrencyService;
use CommissionTask\Services\Math as MathService;

class DepositStrategy implements TransactionFeeCalculateStrategyContract
{
    use CommonCalculateOperations;

    /**
     * Create a new transaction fee calculator strategy instance for deposit transactions.
     */
    public function __construct(
        private CurrencyService $currencyService,
        private MathService $mathService
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function calculateTransactionFee(Transaction $transaction): string
    {
        $amount = $transaction->getAmount();
        $amountScale = $this->currencyService->getCurrencyScale($transaction->getCurrencyCode());
        $taxableAmountScale = $amountScale + $this->getRoundedOffDigitsNumber();

        $feeAmount = $this->mathService->mul(
            $amount,
            $this->getFeeRate(),
            $taxableAmountScale
        );

        return $this->ceilAmount($feeAmount, $amountScale);
    }

    /**
     * Get fee rate.
     */
    private function getFeeRate(): string
    {
        return ConfigService::getConfigByName('feeCalculator.feeRateDeposit');
    }
}
