<?php

declare(strict_types=1);

namespace CommissionTask\Components\TransactionFeeCalculator;

use CommissionTask\Components\TransactionFeeCalculator\Interfaces\TransactionFeeCalculator as TransactionFeeCalculatorContract;
use CommissionTask\Components\TransactionFeeCalculator\Interfaces\TransactionFeeCalculatorStrategyResolver as TransactionFeeCalculatorStrategyResolverContract;
use CommissionTask\Entities\Transaction;

class TransactionFeeCalculator implements TransactionFeeCalculatorContract
{
    /**
     * Create a new transaction fee calculator instance.
     */
    public function __construct(
        private TransactionFeeCalculatorStrategyResolverContract $transactionFeeCalculatorStrategyFactory
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function calculateTransactionFee(Transaction $transaction, int $id): string
    {
        $transactionFeeCalculatorStrategy = $this->transactionFeeCalculatorStrategyFactory->getTransactionFeeCalculatorStrategy($transaction);

        return $transactionFeeCalculatorStrategy->calculateTransactionFee($transaction, $id);
    }
}
