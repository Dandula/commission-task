<?php

namespace CommissionTask\Components\TransactionFeeCalculator;

use CommissionTask\Entities\Transaction;
use CommissionTask\Components\TransactionFeeCalculator\Interfaces\TransactionFeeCalculator as TransactionFeeCalculatorContract;
use CommissionTask\Factories\TransactionFeeCalculatorStrategyFactory;

class TransactionFeeCalculator implements TransactionFeeCalculatorContract
{
    /**
     * @var TransactionFeeCalculatorStrategyFactory
     */
    private $transactionFeeCalculatorStrategyFactory;

    /**
     * Create a new transaction fee calculator instance.
     */
    public function __construct(
        TransactionFeeCalculatorStrategyFactory $transactionFeeCalculatorStrategyFactory
    )
    {
        $this->transactionFeeCalculatorStrategyFactory = $transactionFeeCalculatorStrategyFactory;
    }

    /**
     * @inheritDoc
     */
    public function calculateTransactionFee(Transaction $transaction): string
    {
        $transactionFeeCalculatorStrategy = $this->transactionFeeCalculatorStrategyFactory->getTransactionFeeCalculatorStrategy($transaction);

        return $transactionFeeCalculatorStrategy->calculateTransactionFee($transaction);
    }
}