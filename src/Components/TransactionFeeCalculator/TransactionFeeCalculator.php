<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator;

use CommissionTask\Components\TransactionFeeCalculator\Interfaces\TransactionFeeCalculator as TransactionFeeCalculatorContract;
use CommissionTask\Entities\Transaction;
use CommissionTask\Factories\TransactionFeeCalculatorStrategyFactory;

class TransactionFeeCalculator implements TransactionFeeCalculatorContract
{
    /**
     * Create a new transaction fee calculator instance.
     */
    public function __construct(
        private TransactionFeeCalculatorStrategyFactory $transactionFeeCalculatorStrategyFactory
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function calculateTransactionFee(Transaction $transaction): string
    {
        $transactionFeeCalculatorStrategy = $this->transactionFeeCalculatorStrategyFactory->getTransactionFeeCalculatorStrategy($transaction);

        return $transactionFeeCalculatorStrategy->calculateTransactionFee($transaction);
    }
}
